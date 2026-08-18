<?php

namespace App\Livewire\Storefront;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
#[Title('Cerita Kami — Petani Kopi Robusta Lampung & Way Kopi')]
class AboutUs extends Component
{
    public function render(): View
    {
        return view('livewire.storefront.about-us');
    }
}
