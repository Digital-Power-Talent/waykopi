<?php

namespace App\Livewire\Storefront;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.storefront')]
#[Title('Cerita Kebun & Edukasi Kopi Robusta — Way Kopi')]
class BlogIndex extends Component
{
    use WithPagination;

    public string $selectedCategory = '';

    public string $search = '';

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    public function render(): View
    {
        $categories = Post::where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $query = Post::with('author')
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->latest('published_at');

        if ($this->selectedCategory) {
            $query->where('category', $this->selectedCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('excerpt', 'like', '%'.$this->search.'%')
                    ->orWhere('content', 'like', '%'.$this->search.'%');
            });
        }

        $featuredPost = (clone $query)->first();

        $posts = $query->when($featuredPost, fn ($q) => $q->where('id', '!=', $featuredPost->id))
            ->paginate(6);

        return view('livewire.storefront.blog-index', [
            'featuredPost' => $featuredPost,
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
