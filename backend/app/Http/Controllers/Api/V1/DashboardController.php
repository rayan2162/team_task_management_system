<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Dashboard\Services\DashboardService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $stats = $this->dashboardService->getStats($request->user()->id);

        return $this->success($stats, 'Dashboard data retrieved.');
    }

    public function analytics(Request $request): JsonResponse
    {
        $analytics = $this->dashboardService->getAnalytics($request->user()->id);

        return $this->success($analytics, 'Analytics data retrieved.');
    }
}
