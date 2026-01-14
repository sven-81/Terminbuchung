<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class ConsultantController
{
    public function index(): JsonResponse
    {
        $consultants = DB::table('consultants')
            ->select('id', 'name', 'email', 'daily_capacity_minutes', 'created_at')
            ->get();

        return response()->json([
            'data' => $consultants->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'daily_capacity_minutes' => $c->daily_capacity_minutes,
                'created_at' => $c->created_at,
            ]),
        ]);
    }
}

