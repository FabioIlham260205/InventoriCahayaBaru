<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('fruits', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('sku', 50)->nullable()->unique();
            $table->string('category', 80)->nullable();
            $table->string('unit', 20)->default('kg');
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->string('supplier', 120)->nullable();
            $table->string('storage_location', 120)->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fruit_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->string('reference', 120)->nullable();
            $table->string('handled_by', 120)->nullable();
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fruit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 40);
            $table->string('title', 160);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('team_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name', 120);
            $table->string('channel', 40);
            $table->string('subject', 160);
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_messages');
        Schema::dropIfExists('inventory_alerts');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('fruits');
    }
};
