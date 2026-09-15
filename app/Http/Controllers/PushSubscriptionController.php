<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enregistrement / suppression des abonnements Web Push du navigateur.
 */
class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:2000',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
            'contentEncoding' => 'nullable|string|max:50',
        ]);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint_hash' => PushSubscription::hashEndpoint($validated['endpoint'])],
            [
                'user_id' => $request->user()->id,
                'endpoint' => $validated['endpoint'],
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aesgcm',
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['id' => $subscription->id], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:2000',
        ]);

        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint_hash', PushSubscription::hashEndpoint($validated['endpoint']))
            ->delete();

        return response()->json(['deleted' => true]);
    }
}
