<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomElement([45000, 90000, 135000]);
        $shipping = 15000;

        return [
            'user_id' => User::factory(),
            'guest_email' => null,
            'guest_phone' => null,
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->streetAddress(),
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Tengah',
            'postal_code' => '16121',
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $subtotal + $shipping,
            'status' => fake()->randomElement(['pending_payment', 'paid', 'shipped', 'delivered']),
            'courier_name' => 'JNE REG',
            'notes' => fake()->optional()->sentence(),
            'expires_at' => now()->addHour(),
        ];
    }
}
