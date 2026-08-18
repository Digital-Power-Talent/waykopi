<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence();
        return [
            'author_id' => User::factory()->admin(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'cover_image_url' => 'https://placehold.co/800x600/1C1712/C8A050?text=Way+Kopi+Blog',
            'category' => fake()->randomElement(['Coffee', 'Recipes', 'Lifestyle']),
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
