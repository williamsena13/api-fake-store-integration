<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Integrator\FakeStore\SyncContext;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FakeStoreSyncController extends Controller
{
    public function __construct(
        private SyncContext $syncContext
    ) {}

    public function sync(Request $request): JsonResponse
    {
        try {
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
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}