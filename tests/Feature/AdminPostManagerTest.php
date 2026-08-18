<?php

namespace Tests\Feature;

use App\Livewire\Admin\PostManager;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPostManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_post_manager(): void
    {
        $response = $this->get(route('admin.posts.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_post_manager(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get(route('admin.posts.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_post_manager_page(): void
    {
        $admin = User::factory()->admin()->create();

        Post::create([
            'author_id' => $admin->id,
            'title' => 'Artikel Uji Coba Way Kopi',
            'slug' => 'artikel-uji-coba-way-kopi',
            'excerpt' => 'Ringkasan artikel uji coba',
            'content' => 'Isi artikel uji coba lengkap',
            'category' => 'Cerita Petani',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.posts.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Blog & Artikel', false);
        $response->assertSee('Artikel Uji Coba Way Kopi');
    }

    public function test_admin_can_create_new_post(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PostManager::class)
            ->set('title', 'Kopi Robusta Tanggamus Terbaik')
            ->set('category', 'Cerita Petani')
            ->set('excerpt', 'Kisah kebun kopi Tanggamus')
            ->set('content', 'Isi cerita lengkap petani kopi Tanggamus')
            ->set('coverImageUrl', '/images/lampung_farmer.png')
            ->set('status', 'published')
            ->call('savePost')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'title' => 'Kopi Robusta Tanggamus Terbaik',
            'slug' => 'kopi-robusta-tanggamus-terbaik',
            'category' => 'Cerita Petani',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_edit_existing_post(): void
    {
        $admin = User::factory()->admin()->create();

        $post = Post::create([
            'author_id' => $admin->id,
            'title' => 'Judul Lama Artikel',
            'slug' => 'judul-lama-artikel',
            'excerpt' => 'Ringkasan lama',
            'content' => 'Isi artikel lama',
            'category' => 'Edukasi Kopi',
            'status' => 'draft',
        ]);

        Livewire::actingAs($admin)
            ->test(PostManager::class)
            ->call('openEditModal', $post->id)
            ->set('title', 'Judul Baru Artikel Diperbarui')
            ->set('status', 'published')
            ->call('savePost')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Judul Baru Artikel Diperbarui',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_delete_post(): void
    {
        $admin = User::factory()->admin()->create();

        $post = Post::create([
            'author_id' => $admin->id,
            'title' => 'Postingan Yang Akan Dihapus',
            'slug' => 'postingan-yang-akan-dihapus',
            'excerpt' => 'Ringkasan hapus',
            'content' => 'Isi artikel hapus',
            'category' => 'Edukasi Kopi',
            'status' => 'draft',
        ]);

        Livewire::actingAs($admin)
            ->test(PostManager::class)
            ->call('deletePost', $post->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
