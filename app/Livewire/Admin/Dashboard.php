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
        $totalRevenue = (float) Order::query()->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'], 'and', false)->sum('total');
        $totalOrders = Order::query()->count('*');
        $pendingOrders = Order::query()->where('status', '=', 'pending_payment')->count('*');
        $paidOrders = Order::query()->whereIn('status', ['paid', 'delivered'], 'and', false)->count('*');
        $totalProducts = Product::query()->count('*');
        $lowStockVariants = ProductVariant::query()->with('product')->where('stock', '<', 10)->get();

        $recentOrders = Order::query()->with(['items', 'payment', 'shipment'])
            ->latest('id')
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
