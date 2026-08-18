<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1 Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Way Kopi',
            'email' => 'admin@waykopi.com',
            'phone' => '6281234567890',
        ]);

        // 1 Produk "Way Kopi Robusta" dengan 1 varian (giling medium, 200g)
        $product = Product::create([
            'slug' => 'way-kopi-robusta',
            'name' => 'Way Kopi Fine Robusta',
            'description' => 'Kopi Robusta petik merah dipanen langsung dari kebun petani di Tanggamus, Lampung. Memiliki cita rasa bold kaya dengan sentuhan cokelat hitam, karamel, dan rempah alami khas pegunungan Lampung.',
            'roast_profile' => 'Medium Dark',
            'origin' => 'Tanggamus, Lampung, Indonesia',
            'is_active' => true,
        ]);

        $variant200 = ProductVariant::create([
            'product_id' => $product->id,
            'grind_type' => 'medium',
            'weight_grams' => 200,
            'sku' => 'WK-ROBUSTA-200',
            'price' => 45000,
            'stock' => 100,
            'reserved_stock' => 0,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'url' => '/images/products/produk-utama.jpg',
            'alt_text' => 'Kemasan Premium Way Kopi Fine Robusta Lampung 200g',
            'sort_order' => 1,
        ]);

        // Additional product variants / products
        $productDark = Product::create([
            'slug' => 'way-kopi-dark-roast',
            'name' => 'Way Kopi Dark Roast Robusta',
            'description' => 'Sangrai gelap pekat dengan kadar asam rendah dan crema tebal, sangat ideal untuk racikan espresso, kopi susu kekinian, maupun tubruk tradisional.',
            'roast_profile' => 'Dark Roast',
            'origin' => 'Liwa, Lampung Barat, Indonesia',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $productDark->id,
            'grind_type' => 'whole_bean',
            'weight_grams' => 250,
            'sku' => 'WK-DARK-250',
            'price' => 55000,
            'stock' => 80,
            'reserved_stock' => 0,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $productDark->id,
            'url' => '/images/coffee_beans.png',
            'alt_text' => 'Biji Kopi Robusta Dark Roast Lampung',
            'sort_order' => 1,
        ]);

        // Dummy customer & orders
        $orders = Order::factory()->count(10)->create();
        foreach ($orders as $order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant200->id,
                'product_name' => $product->name,
                'variant_label' => 'Biji Utuh, 200g',
                'price_at_purchase' => $variant200->price,
                'quantity' => 2,
            ]);
        }

        // Authentic Blog Posts (Cerita Petani & Edukasi)
        Post::create([
            'author_id' => $admin->id,
            'title' => 'Cerita Pak Subagyo: 30 Tahun Merawat Kebun Robusta Kaki Gunung Tanggamus',
            'slug' => 'cerita-pak-subagyo-petani-kopi-tanggamus',
            'excerpt' => 'Di lereng Gunung Tanggamus yang dingin dan subur, Pak Subagyo bersama komunitas petani lokal mendedikasikan hidupnya menjaga kualitas petik merah biji kopi Robusta khas Lampung.',
            'content' => "Di ketinggian 800 meter di atas permukaan laut, lereng Gunung Tanggamus menawarkan iklim microclimate yang sempurna untuk tanaman kopi Robusta. Pak Subagyo (58), seorang petani kopi generasi kedua, memulai aktivitasnya sebelum matahari terbit.\n\n\"Kopi Robusta Tanggamus memiliki rasa yang unik karena tanah vulkaniknya kaya akan nutrisi alami. Dulu, petani memetik semua buah kopi tanpa memilih. Namun sejak bergabung dengan gerakan Fine Robusta, kami menerapkan standar ketat: Hanya buah kopi yang berwarna merah ranum yang dipetik,\" cerita Pak Subagyo.\n\nProses petik merah ini membutuhkan ketelitian dan kesabaran ekstra. Pembelian langsung melalui Way Kopi membantu memangkas rantai tengkulak, memberikan harga panen yang layak, serta memastikan setiap kantong kopi yang dinikmati pelanggan berasal dari keringat petani yang dihargai secara adil.",
            'cover_image_url' => '/images/lampung_farmer.png',
            'category' => 'Cerita Petani',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        Post::create([
            'author_id' => $admin->id,
            'title' => 'Rahasia Profil Sangrai Medium Dark Way Kopi yang Gurih dan Harum',
            'slug' => 'rahasia-profil-sangrai-medium-dark-way-kopi',
            'excerpt' => 'Mengapa tingkat sangrai Medium Dark sangat cocok untuk kopi Robusta Lampung? Temukan keseimbangan rasa cokelat hitam, karamel, dan aroma manis rempah.',
            'content' => "Memanggang biji kopi Robusta adalah gabungan antara seni dan sains. Robusta memiliki tingkat kerapatan biji yang lebih padat dibanding Arabika, sehingga memerlukan penyesuaian suhu (roasting curve) yang sangat presisi.\n\nDi roastery Way Kopi, kami memilih profil sangrai Medium Dark. Profil ini berhasil memunculkan rasa gurih manis seperti cokelat hitam (dark chocolate) dan karamel tanpa menghasilkan rasa gosong atau pahit yang menyengat.\n\nSaat diseduh, aromanya merebak manis dan meninggalkan *aftertaste* gurih yang bertahan lama di tenggorokan.",
            'cover_image_url' => '/images/coffee_roaster.png',
            'category' => 'Profil Sangrai',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        Post::create([
            'author_id' => $admin->id,
            'title' => 'Panduan Menyeduh Robusta Lampung Agar Tidak Pahit: Rasakan Sensasi Dark Chocolate',
            'slug' => 'panduan-menyeduh-robusta-lampung-agar-tidak-pahit',
            'excerpt' => 'Sering merasa kopi Robusta terlalu pahit? Ikuti rasio seduh 1:15 dengan suhu air 90°C untuk ekstraksi rasa cokelat & manis alami terbaik.',
            'content' => "Banyak orang menganggap kopi Robusta selalu identik dengan rasa pahit pekat. Padahal, jika menggunakan biji petik merah berkualiti (Fine Robusta) dan teknik seduh yang tepat, rasa kopi Robusta bisa terasa sangat nikmat dan berkarakter.\n\nBerikut langkah menyeduh kopi Robusta ala Way Kopi:\n\n1. Rasio Seduh: Gunakan 15 gram bubuk kopi untuk 225 ml air panas (Rasio 1:15).\n2. Suhu Air: Jangan gunakan air mendidih murni (100°C). Diamkan air setelah mendidih selama 1 menit hingga suhunya mencapai sekitar 90°C - 92°C.\n3. Waktu Ekstraksi: Jika menyeduh dengan metode V60 atau Tubruk, biarkan proses blooming selama 30 detik sebelum penuangan air utama.\n\nHasilnya adalah secangkir kopi mantap dengan rasa manis gurih cokelat alami dan tanpa rasa pahit berlebih!",
            'cover_image_url' => '/images/hero_coffee_cup.png',
            'category' => 'Edukasi Kopi',
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);

        Post::create([
            'author_id' => $admin->id,
            'title' => 'Perbedaan Kopi Robusta & Arabika Lampung: Mana Pilihan Kopi Favoritmu?',
            'slug' => 'perbedaan-kopi-robusta-dan-arabika-lampung',
            'excerpt' => 'Memahami perbedaan kafein, profil rasa, dan karakter keasaman antara Robusta Lampung dan Arabika untuk menentukan kopi harian kamu.',
            'content' => "Lampung dikenal sebagai penghasil kopi Robusta terbesar di Indonesia. Namun, apa yang membuat Kopi Robusta Lampung begitu istimewa jika dibandingkan dengan Arabika?\n\n- Kafein & Body: Robusta mengandung kadar kafein sekitar 2.2% - 2.7%, dua kali lipat lebih tinggi dari Arabika. Ini menghasilkan kekentalan (body) yang mantap dan dorongan energi ekstra.\n- Keasaman: Kopi Robusta memiliki keasaman yang sangat rendah, menjadikannya pilihan ideal bagi penikmat kopi yang sensitif terhadap asam lambung.\n- Cita Rasa: Arabika cenderung fruity dan berbunga, sementara Robusta Lampung kaya akan taste note dark chocolate, nutty, dan karamel manis.",
            'cover_image_url' => '/images/coffee_beans.png',
            'category' => 'Edukasi Kopi',
            'status' => 'published',
            'published_at' => now()->subDays(15),
        ]);
    }
}
