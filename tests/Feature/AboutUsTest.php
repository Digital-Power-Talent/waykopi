<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_us_page_renders_successfully(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Cerita Kami');
        $response->assertSee('Tanggamus');
        $response->assertSee('Direct-to-Consumer');
    }
}
