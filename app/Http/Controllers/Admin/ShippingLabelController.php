<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class ShippingLabelController extends Controller
{
    /**
     * Display printable shipping label / resi for an order.
     */
    public function show(Order $order): View
    {
        $order->load(['items.productVariant.product', 'payment', 'shipment', 'user']);

        return view('admin.orders.shipping-label', [
            'order' => $order,
        ]);
    }
}
