<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShippingLabelTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->create([
            'name' => 'Admin Test Label',
            'email' => 'admin_label_'.time().'@waykopi.com',
            'role' => 'admin',
        ]);
        $this->admin = $admin;

        /** @var Product $product */
        $product = Product::create([
            'name' => 'Arabika Tanggamus Special',
            'slug' => 'arabika-tanggamus-special',
            'description' => 'Biji kopi arabika pilihan dari Tanggamus.',
            'roast_profile' => 'Light Medium',
            'origin' => 'Tanggamus, Lampung',
            'is_active' => true,
        ]);

        /** @var ProductVariant $variant */
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-ARABIKA-200',
            'grind_type' => 'whole_bean',
            'weight_grams' => 200,
            'price' => 65000,
            'stock' => 20,
            'is_active' => true,
        ]);

        /** @var Order $order */
        $order = Order::create([
            'order_number' => 'WK-TEST-LABEL-01',
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 12, Bogor Barat',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 65000,
            'shipping_cost' => 15000,
            'unique_code' => 123,
            'total' => 80123,
            'status' => 'processing',
            'courier_name' => 'JNE Reguler',
        ]);

        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_label' => 'Biji Utuh, 200g',
            'price_at_purchase' => 65000,
            'quantity' => 1,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'cod',
            'amount' => 80123,
            'status' => 'pending',
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'courier_code' => 'jne',
            'courier_service' => 'JNE Reguler',
            'status' => 'pending',
            'tracking_number' => 'JN123456789ID',
        ]);

        $this->order = $order;
    }

    public function test_admin_can_view_printable_shipping_label(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.orders.shipping-label', $this->order->id))
            ->assertStatus(200)
            ->assertSee('WAY KOPI ROASTERY')
            ->assertSee($this->order->order_number)
            ->assertSee('Budi Santoso')
            ->assertSee('COD (BAYAR DI TEMPAT)');
    }
}
