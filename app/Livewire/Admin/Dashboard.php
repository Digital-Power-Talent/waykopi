<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Admin Dashboard — Way Kopi')]
class Dashboard extends Component
{
    public function render(): View
    {
        $totalRevenue = (float) Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending_payment')->count();
        $paidOrders = Order::whereIn('status', ['paid', 'delivered'])->count();
        $totalProducts = Product::count();
        $lowStockVariants = ProductVariant::with('product')->where('stock', '<', 10)->get();

        $recentOrders = Order::with(['items', 'payment', 'shipment'])
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'paidOrders' => $paidOrders,
            'totalProducts' => $totalProducts,
            'lowStockVariants' => $lowStockVariants,
            'recentOrders' => $recentOrders,
        ]);
    }
}
