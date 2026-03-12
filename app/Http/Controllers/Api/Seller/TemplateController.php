<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\AuctionTemplate;
use App\Services\AuctionService;
use App\Services\AuctionTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * T-1301: Auction template management for sellers.
 */
class TemplateController extends Controller
{
    public function __construct(
        private readonly AuctionTemplateService $templateService,
        private readonly AuctionService $auctionService,
    ) {}

    public function index(): JsonResponse
    {
        $templates = $this->templateService->forSeller(Auth::user());

        return response()->json(['data' => $templates]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'title'         => ['required', 'string', 'max:200'],
            'description'   => ['nullable', 'string'],
            'category_id'   => ['nullable', 'uuid'],
            'start_price'   => ['required', 'numeric', 'min:0.01'],
            'buy_now_price' => ['nullable', 'numeric', 'min:0.01'],
            'reserve_price' => ['nullable', 'numeric', 'min:0.01'],
            'condition'     => ['nullable', 'in:new,like_new,used,for_parts'],
            'type'          => ['nullable', 'in:standard,buy_now,reserve'],
        ]);

        $template = AuctionTemplate::create([
            'seller_id' => Auth::id(),
            'name'      => $data['name'],
            'data'      => array_except($data, ['name']),
        ]);

        return response()->json(['data' => $template], 201);
    }

    /**
     * Create a new auction from a template.
     */
    public function createAuction(Request $request, AuctionTemplate $template): JsonResponse
    {
        if ($template->seller_id !== Auth::id()) {
            return response()->json(['error' => ['message' => 'Not found.']], 404);
        }

        $overrides = $request->validate([
            'duration_days' => ['sometimes', 'integer', 'in:1,3,5,7,10,14'],
            'ends_at'       => ['sometimes', 'date', 'after:now'],
        ]);

        $auction = $this->templateService->createAuction($template, $overrides, $this->auctionService);

        return response()->json(['data' => $auction], 201);
    }

    public function destroy(AuctionTemplate $template): JsonResponse
    {
        if ($template->seller_id !== Auth::id()) {
            return response()->json(['error' => ['message' => 'Not found.']], 404);
        }

        $this->templateService->delete($template);

        return response()->json(null, 204);
    }
}
