<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Integrator\FakeStore\SyncContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FakeStoreSyncController extends Controller
{
    public function __construct(
        private SyncContext $syncContext
    ) {}

    public function sync(Request $request): JsonResponse
    {
        $mode = $request->query('mode', 'full');
        $limit = $request->query('limit') ? (int) $request->query('limit') : null;
        
        $strategy = $this->syncContext->getStrategy($mode, $limit);
        $result = $strategy->sync();

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'limit' => $limit,
            'result' => $result->toArray()
        ]);
    }
}