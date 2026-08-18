<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('biteship_order_id')->nullable()->unique();
            $table->string('tracking_number')->nullable();
            $table->string('courier_code')->nullable();
            $table->string('courier_service')->nullable();
            $table->enum('status', ['pending', 'booked', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->string('label_url')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('biteship_order_id');
            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
