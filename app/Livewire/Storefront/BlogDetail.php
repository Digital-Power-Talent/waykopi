<?php

namespace App\Livewire\Storefront;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
class BlogDetail extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        /** @var Post $post */
        $post = Post::with('author')
            ->where('slug', $this->slug)
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->firstOrFail();

        $relatedPosts = Post::where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->when($post->category, fn ($q) => $q->where('category', $post->category))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.storefront.blog-detail', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
