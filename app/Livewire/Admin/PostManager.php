<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Postingan Blog — Admin Way Kopi')]
class PostManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $statusFilter = '';

    // Form Modal state
    public bool $showModal = false;

    public ?int $editingPostId = null;

    public string $title = '';

    public string $category = 'Cerita Petani';

    public string $excerpt = '';

    public string $content = '';

    public string $coverImageUrl = '';

    public string $status = 'published';

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'title' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'excerpt' => 'required|string|max:500',
        'content' => 'required|string',
        'coverImageUrl' => 'nullable|string|max:500',
        'status' => 'required|in:draft,published',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $postId): void
    {
        /** @var Post $post */
        $post = Post::findOrFail($postId);
        $this->editingPostId = $post->id;
        $this->title = $post->title;
        $this->category = $post->category;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->coverImageUrl = $post->cover_image_url ?? '';
        $this->status = $post->status;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingPostId = null;
        $this->title = '';
        $this->category = 'Cerita Petani';
        $this->excerpt = '';
        $this->content = '';
        $this->coverImageUrl = '';
        $this->status = 'published';
        $this->resetValidation();
    }

    public function savePost(): void
    {
        $this->validate();

        $slug = Str::slug($this->title);
        $baseSlug = $slug;
        $count = 1;

        // Ensure unique slug
        while (Post::query()->where('slug', $slug)
            ->when($this->editingPostId, fn ($q) => $q->where('id', '!=', $this->editingPostId))
            ->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        $coverUrl = trim($this->coverImageUrl) ?: '/images/lampung_farmer.png';
        /** @var User|null $adminUser */
        $adminUser = User::query()->where('role', 'admin')->first();
        $authorId = Auth::id() ?? ($adminUser ? $adminUser->id : 1);

        if ($this->editingPostId) {
            /** @var Post $post */
            $post = Post::query()->findOrFail($this->editingPostId);
            $post->update([
                'title' => $this->title,
                'slug' => $slug,
                'category' => $this->category,
                'excerpt' => $this->excerpt,
                'content' => $this->content,
                'cover_image_url' => $coverUrl,
                'status' => $this->status,
                'published_at' => $this->status === 'published' ? ($post->published_at ?? now()) : null,
            ]);

            session()->flash('success', "Postingan '{$post->title}' berhasil diperbarui!");
        } else {
            /** @var Post $post */
            $post = Post::query()->create([
                'author_id' => $authorId,
                'title' => $this->title,
                'slug' => $slug,
                'category' => $this->category,
                'excerpt' => $this->excerpt,
                'content' => $this->content,
                'cover_image_url' => $coverUrl,
                'status' => $this->status,
                'published_at' => $this->status === 'published' ? now() : null,
            ]);

            session()->flash('success', "Postingan baru '{$post->title}' berhasil diterbitkan!");
        }

        $this->closeModal();
    }

    public function deletePost(int $postId): void
    {
        /** @var Post|null $post */
        $post = Post::query()->find($postId, ['*']);
        if ($post) {
            $title = $post->title;
            Post::destroy($postId);
            session()->flash('success', "Postingan '{$title}' berhasil dihapus.");
        }
    }

    public function render(): View
    {
        $posts = Post::query()->with('author')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('id')
            ->paginate(10);

        return view('livewire.admin.post-manager', [
            'posts' => $posts,
        ]);
    }
}
