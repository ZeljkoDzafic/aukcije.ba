<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatisticsController extends Controller
{
    public function index(Request $request): JsonResponse|StreamedResponse
    {
        $data = [
            'users' => [
                'total' => User::query()->count(),
                'banned' => User::query()->where('is_banned', true)->count(),
            ],
            'auctions' => [
                'total' => Auction::query()->count(),
                'active' => Auction::query()->where('status', 'active')->count(),
                'featured' => Auction::query()->where('is_featured', true)->count(),
            ],
            'revenue' => [
                'gross' => (float) Order::query()->sum('total_amount'),
                'commission' => (float) Order::query()->sum('commission_amount'),
            ],
            'trust' => [
                'disputes' => Dispute::query()->count(),
                'resolved' => Dispute::query()->where('status', 'resolved')->count(),
            ],
        ];

        if ($request->string('format') === 'csv') {
            return response()->streamDownload(function () use ($data) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['section', 'metric', 'value']);

                foreach ($data as $section => $metrics) {
                    foreach ($metrics as $metric => $value) {
                        fputcsv($handle, [$section, $metric, $value]);
                    }
                }

                fclose($handle);
            }, 'admin-statistics.csv', ['Content-Type' => 'text/csv']);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
