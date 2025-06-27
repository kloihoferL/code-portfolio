<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Slot;

class SlotController extends Controller
{
    public function createSlot(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $slot = Slot::create($request->all());
            DB::commit();
            return response()->json($slot, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Creating Slot failed: ' . $e->getMessage()], 500);
        }
    }



}
