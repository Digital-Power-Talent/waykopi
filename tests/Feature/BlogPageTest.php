<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_index_blog_menampilkan_artikel_terbit(): void
    {
        $author = User::factory()->create();

        Post::create([
            'author_id' => $author->id,
            'title' => 'Artikel Rahasia Seduh Kopi',
            'slug' => 'artikel-rahasia-seduh-kopi',
            'excerpt' => 'Ringkasan panduan menyeduh kopi Robusta.',
            'content' => 'Konten lengkap panduan menyeduh kopi Robusta Lampung.',
            'category' => 'Edukasi Kopi',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('Artikel Rahasia Seduh Kopi');
        $response->assertSee('Edukasi Kopi');
    }

    public function test_halaman_detail_blog_menampilkan_artikel_berdasarkan_slug(): void
    {
        $author = User::factory()->create();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Cerita Kebun Tanggamus',
            'slug' => 'cerita-kebun-tanggamus',
            'excerpt' => 'Kisah dedikasi petani lokal Tanggamus.',
            'content' => 'Di lereng Gunung Tanggamus yang hijau, kopi tumbuh subur.',
            'category' => 'Cerita Petani',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get("/blog/{$post->slug}");
        $response->assertStatus(200);
        $response->assertSee('Cerita Kebun Tanggamus');
        $response->assertSee('Di lereng Gunung Tanggamus yang hijau');
    }

    public function test_halaman_detail_blog_mengembalikan_404_jika_slug_tidak_ditemukan(): void
    {
        $response = $this->get('/blog/artikel-tidak-ada');
        $response->assertStatus(404);
    }
}
