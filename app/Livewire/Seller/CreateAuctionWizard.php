<?php

declare(strict_types=1);

namespace App\Livewire\Seller;

use App\Models\Auction;
use App\Models\AuctionImage;
use App\Models\Category;
use App\Services\AuctionService;
use App\Services\ImageOptimizationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class CreateAuctionWizard extends Component
{
    use WithFileUploads;

    public ?string $auctionId = null;

    public int $step = 1;

    /** @var list<string> */
    public array $steps = ['Osnovno', 'Slike', 'Cijena', 'Dostava', 'Pregled'];

    public string $title = '';

    public string $categoryId = '';

    public string $description = '';

    public string $condition = 'used';

    public string $startPrice = '';

    public string $reservePrice = '';

    public string $buyNowPrice = '';

    public string $startsAt = '';

    public int $durationDays = 7;

    /** @var list<int> */
    public array $durationOptions = [1, 3, 5, 7, 10, 14];

    public string $shippingMethod = '';

    public string $shippingPrice = '';

    public string $location = '';

    public string $statusMessage = '';

    /** @var array<string, string> */
    public array $categoryOptions = [];

    public string $newImageUrl = '';

    /** @var list<string> */
    public array $imageUrls = [];

    /** @var list<TemporaryUploadedFile> */
    public array $uploadedImages = [];

    /**
     * Keyed by the canonical URL stored in $imageUrls.
     * Each entry: ['thumbnail'=>url, 'medium'=>url, 'large'=>url, 'original'=>url, 'blurhash'=>str, 'width'=>int, 'height'=>int]
     *
     * @var array<string, array<string, mixed>>
     */
    public array $imageOptimizedData = [];

    public function mount(): void
    {
        $this->categoryOptions = ['' => 'Odaberi kategoriju'];

        if (Schema::hasTable('categories')) {
            $this->categoryOptions += Category::query()->orderBy('name')->pluck('name', 'id')->all();
        }

        if ($this->auctionId && Schema::hasTable('auctions')) {
            $auction = Auction::query()->find($this->auctionId);

            if ($auction && $auction->seller_id === Auth::id()) {
                $this->title = $auction->title;
                $this->categoryId = (string) ($auction->category_id ?? '');
                $this->description = (string) ($auction->description ?? '');
                $this->condition = (string) $auction->condition;
                $this->startPrice = (string) $auction->start_price;
                $this->reservePrice = (string) ($auction->reserve_price ?? '');
                $this->buyNowPrice = (string) ($auction->buy_now_price ?? '');
                $this->startsAt = $auction->starts_at?->format('Y-m-d\TH:i') ?? '';
                $this->durationDays = (int) ($auction->duration_days ?? 7);
                $this->shippingMethod = (string) ($auction->shipping_method ?? '');
                $this->shippingPrice = (string) ($auction->shipping_cost ?? '');
                $this->location = (string) ($auction->location_city ?? $auction->location ?? '');
                $this->imageUrls = $auction->images()->orderBy('sort_order')->pluck('url')->all();
            }
        }
    }

    public function nextStep(): void
    {
        $this->step = min($this->step + 1, count($this->steps));
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function goToStep(int $step): void
    {
        $this->step = max(1, min($step, count($this->steps)));
    }

    public function saveDraft(): void
    {
        $this->persist('draft');
    }

    public function publish(): void
    {
        $this->persist('active');
    }

    public function addImage(): void
    {
        if ($this->newImageUrl === '' || count($this->imageUrls) >= 10) {
            return;
        }

        $this->imageUrls[] = $this->newImageUrl;
        $this->newImageUrl = '';
    }

    public function removeImage(int $index): void
    {
        unset($this->imageUrls[$index]);
        $this->imageUrls = array_values($this->imageUrls);
    }

    public function moveImage(int $index, string $direction): void
    {
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if (! isset($this->imageUrls[$index], $this->imageUrls[$target])) {
            return;
        }

        [$this->imageUrls[$index], $this->imageUrls[$target]] = [$this->imageUrls[$target], $this->imageUrls[$index]];
    }

    public function updatedUploadedImages(): void
    {
        $this->validate([
            'uploadedImages.*' => ['image', 'max:5120'],
        ]);

        $optimizer = app(ImageOptimizationService::class);

        foreach ($this->uploadedImages as $file) {
            if (count($this->imageUrls) >= 10) {
                break;
            }

            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            try {
                // Optimise and store all size variants; get back URLs + blurhash + dimensions
                $result   = $optimizer->optimizeAndStore($file, 'auction-images');
                $canonical = $result['original'] ?? $result['medium'] ?? '';

                if ($canonical !== '') {
                    $this->imageUrls[]                     = $canonical;
                    $this->imageOptimizedData[$canonical]  = $result;
                }
            } catch (\Throwable) {
                // Fall back to raw upload on optimization failure
                $disk = config('filesystems.default', 'public');
                $path = $file->store('auction-images', $disk);
                $url  = $disk === 'public'
                    ? '/storage/'.$path
                    : Storage::disk($disk)->url($path);

                $this->imageUrls[] = $url;
            }
        }

        $this->uploadedImages = [];
    }

    public function render(): View
    {
        return view('livewire.seller.create-auction-wizard');
    }

    protected function persist(string $status): void
    {
        $this->validate([
            'title'        => ['required', 'string', 'max:500'],
            'description'  => ['nullable', 'string'],
            'startPrice'   => ['required', 'numeric', 'min:0.01'],
            'durationDays' => ['required', 'integer', 'in:1,3,5,7,10,14'],
            'shippingPrice' => ['nullable', 'numeric', 'min:0'],
            'startsAt' => ['nullable', 'date'],
        ]);

        if ($status === 'active' && count($this->imageUrls) === 0) {
            $this->addError('imageUrls', 'Dodaj barem jednu sliku prije objave aukcije.');

            return;
        }

        if (! Auth::check() || ! Schema::hasTable('auctions')) {
            $this->statusMessage = $status === 'draft'
                ? 'Draft je lokalno validiran. Persistencija čeka auth + bazu.'
                : 'Aukcija je validirana za objavu. Persistencija čeka auth + bazu.';

            return;
        }

        $scheduledStartAt = $this->startsAt !== '' ? now()->createFromFormat('Y-m-d\TH:i', $this->startsAt) : null;
        $scheduledStatus = $scheduledStartAt && $status === 'active' && $scheduledStartAt->isFuture() ? 'scheduled' : $status;
        $endAt = ($scheduledStartAt ?? now())->copy()->addDays($this->durationDays);

        $payload = [
            'title'         => $this->title,
            'description'   => $this->description,
            'category_id'   => $this->categoryId ?: Category::query()->value('id'),
            'start_price'   => (float) $this->startPrice,
            'reserve_price' => $this->reservePrice !== '' ? (float) $this->reservePrice : null,
            'buy_now_price' => $this->buyNowPrice !== '' ? (float) $this->buyNowPrice : null,
            'condition'     => $this->condition,
            'type'          => 'standard',
            'duration_days' => $this->durationDays,
            'auto_extension' => true,
            'starts_at'     => $scheduledStartAt,
        ];

        if ($this->auctionId) {
            $auction = Auction::query()->find($this->auctionId);

            if (! $auction || $auction->seller_id !== Auth::id()) {
                $this->statusMessage = 'Aukciju nije moguće urediti.';

                return;
            }

            $auction->update(array_merge($payload, [
                'current_price'     => (float) $this->startPrice,
                'status'            => $scheduledStatus,
                'shipping_available' => true,
                'shipping_method'   => $this->shippingMethod ?: null,
                'shipping_cost'     => $this->shippingPrice !== '' ? (float) $this->shippingPrice : null,
                'location_city'     => $this->location ?: null,
                'starts_at'         => $scheduledStartAt,
                'ends_at'           => $endAt,
                'original_end_at'   => $endAt,
            ]));
        } else {
            try {
                $auction = app(AuctionService::class)->createAuction(Auth::user(), $payload);
            } catch (\RuntimeException $exception) {
                $this->statusMessage = $exception->getMessage();

                return;
            }

            $auction->update([
                'status'             => $scheduledStatus,
                'shipping_available' => true,
                'shipping_method'    => $this->shippingMethod ?: null,
                'shipping_cost'      => $this->shippingPrice !== '' ? (float) $this->shippingPrice : null,
                'location_city'      => $this->location ?: null,
                'starts_at'          => $scheduledStartAt,
                'ends_at'            => $endAt,
                'original_end_at'    => $endAt,
            ]);

            $this->auctionId = $auction->id;
        }

        if ($status === 'active' && $scheduledStatus !== 'scheduled') {
            $auction = app(AuctionService::class)->publishAuction($auction);
        }

        $this->statusMessage = $status === 'draft'
            ? "Draft aukcije '{$auction->title}' je sačuvan."
            : "Aukcija '{$auction->title}' je kreirana i spremna za moderaciju/objavu.";

        $this->syncImages($auction);
    }

    protected function syncImages(Auction $auction): void
    {
        if (! Schema::hasTable('auction_images')) {
            return;
        }

        $auction->images()->delete();

        foreach ($this->imageUrls as $index => $url) {
            $opt = $this->imageOptimizedData[$url] ?? [];

            AuctionImage::query()->create([
                'auction_id'     => $auction->id,
                'url'            => $url,
                'sort_order'     => $index,
                'is_primary'     => $index === 0,
                'blurhash'       => $opt['blurhash'] ?? null,
                'optimized_urls' => $opt !== [] ? array_intersect_key($opt, array_flip(['thumbnail', 'medium', 'large', 'original'])) : null,
                'width'          => $opt['width'] ?? null,
                'height'         => $opt['height'] ?? null,
            ]);
        }
    }
}
