<?php

declare(strict_types=1);

namespace App\Livewire\Seller;

use App\Models\Auction;
use App\Models\AuctionTemplate;
use App\Services\AuctionService;
use App\Services\AuctionTemplateService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class TemplatesManager extends Component
{
    public string $name = '';

    public string $sourceAuctionId = '';

    public string $statusMessage = '';

    public function saveTemplate(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $seller = Auth::user();
        if (! $seller || ! Schema::hasTable('auction_templates')) {
            $this->statusMessage = 'Template nije moguće sačuvati u ovom okruženju.';

            return;
        }

        $sourceAuction = $this->sourceAuctionId !== ''
            ? Auction::query()->where('seller_id', $seller->id)->find($this->sourceAuctionId)
            : $seller->auctions()->latest()->first();

        if (! $sourceAuction) {
            $this->statusMessage = 'Odaberi postojeću aukciju kao osnovu template-a.';

            return;
        }

        app(AuctionTemplateService::class)->createFromAuction($seller, $sourceAuction, $this->name);

        $this->reset(['name', 'sourceAuctionId']);
        $this->statusMessage = 'Template je sačuvan.';
    }

    public function createFromTemplate(string $templateId): void
    {
        $seller = Auth::user();
        $template = AuctionTemplate::query()->where('seller_id', $seller?->id)->find($templateId);

        if (! $seller || ! $template) {
            $this->statusMessage = 'Template nije pronađen.';

            return;
        }

        app(AuctionTemplateService::class)->createAuction($template, ['duration_days' => 7], app(AuctionService::class));
        $this->statusMessage = 'Nova draft aukcija je kreirana iz template-a.';
    }

    public function deleteTemplate(string $templateId): void
    {
        $seller = Auth::user();
        $template = AuctionTemplate::query()->where('seller_id', $seller?->id)->find($templateId);

        if (! $template) {
            return;
        }

        app(AuctionTemplateService::class)->delete($template);
        $this->statusMessage = 'Template je uklonjen.';
    }

    public function render(): View
    {
        $seller = Auth::user();

        return view('livewire.seller.templates-manager', [
            'templates' => $seller && Schema::hasTable('auction_templates')
                ? AuctionTemplate::query()->where('seller_id', $seller->id)->latest()->get()
                : collect(),
            'auctionOptions' => $seller && Schema::hasTable('auctions')
                ? $seller->auctions()->latest()->get(['id', 'title'])
                : collect(),
        ]);
    }
}
