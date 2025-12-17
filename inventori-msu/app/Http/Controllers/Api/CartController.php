<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get current cart items from session.
     */
    public function index(Request $request)
    {
        return response()->json(session('cart', []));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        // Accept 'quantity' as the NEW total or incremental? 
        // FE Guest cart.js logic:
        // "newQty = currentQty + inc;" then sends {quantity: newQty} to /add.
        // But also check duplication? 
        // cart.js sends the exact structure it wants stored.
        
        $newItem = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'quantity' => 'required|integer',
            'imageUrl' => 'nullable',
        ]);
        
        $cart = session('cart', []);
        
        // Check if item exists
        $found = false;
        foreach($cart as &$item) {
            if($item['name'] === $newItem['name'] && $item['type'] === $newItem['type']) {
                $item['quantity'] = $newItem['quantity']; // Updates to the qty sent by client
                // Ensure maxQty if needed, but client handles it.
                if(isset($newItem['imageUrl']) && $newItem['imageUrl']) {
                    $item['imageUrl'] = $newItem['imageUrl'];
                }
                $found = true;
                break;
            }
        }
        unset($item);

        if(!$found) {
            $cart[] = $newItem;
        }

        session(['cart' => $cart]);
        
        return response()->json($cart);
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'quantity' => 'required|integer',
        ]);

        $cart = session('cart', []);

        if ($data['quantity'] <= 0) {
            // Remove item
            $cart = array_values(array_filter($cart, function($item) use ($data) {
                return !($item['name'] === $data['name'] && $item['type'] === $data['type']);
            }));
        } else {
            foreach($cart as &$item) {
                if($item['name'] === $data['name'] && $item['type'] === $data['type']) {
                    $item['quantity'] = $data['quantity'];
                    break;
                }
            }
        }

        session(['cart' => $cart]);
        return response()->json($cart);
    }

    /**
     * Clear cart.
     */
    public function clear(Request $request)
    {
        session()->forget('cart');
        return response()->json([]);
    }
}
