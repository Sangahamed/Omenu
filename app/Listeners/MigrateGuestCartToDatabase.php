<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\CartItem;

class MigrateGuestCartToDatabase
{
    public function handle(Login $event)
    {
        $guestCart = session()->get('cart', []);

        if (!empty($guestCart)) {
            foreach ($guestCart as $menuId => $item) {
                $cartItem = CartItem::firstOrNew([
                    'user_id' => $event->user->id,
                    'menu_id' => $menuId,
                ]);

                $cartItem->quantity = ($cartItem->quantity ?? 0) + $item['quantity'];
                $cartItem->price = $item['price'];
                $cartItem->save();
            }

            // Nettoyer le panier temporaire en session
            session()->forget('cart');
        }
    }
}