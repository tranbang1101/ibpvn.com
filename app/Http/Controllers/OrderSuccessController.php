<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderSuccessController extends Controller
{
    public function __invoke(Request $request): View|Response
    {
        $orderCode = $request->session()->pull('last_order', '');
        $orderTotal = (float) $request->session()->pull('last_order_total', 0);
        $demoOrder = (bool) $request->session()->pull('demo_order', false);
        $orderWasPlaced = $demoOrder || (is_string($orderCode) && $orderCode !== '');

        if (! $orderWasPlaced) {
            return response()->view('storefront.order-success', [
                'orderCode' => '',
                'orderTotal' => 0,
                'orderWasPlaced' => false,
                'demoOrder' => false,
            ], 404);
        }

        return response()->view('storefront.order-success', [
            'orderCode' => $orderCode,
            'orderTotal' => $orderTotal,
            'orderWasPlaced' => true,
            'demoOrder' => $demoOrder,
        ]);
    }
}
