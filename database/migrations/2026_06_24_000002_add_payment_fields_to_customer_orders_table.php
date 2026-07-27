<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('unpaid')->after('status');
            $table->string('payment_provider', 40)->nullable()->after('payment_status');
            $table->string('payment_token')->nullable()->after('payment_provider');
            $table->text('payment_redirect_url')->nullable()->after('payment_token');
            $table->string('payment_type', 60)->nullable()->after('payment_redirect_url');
            $table->string('payment_transaction_id', 120)->nullable()->after('payment_type');
            $table->timestamp('paid_at')->nullable()->after('payment_transaction_id');
            $table->json('payment_payload')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_provider',
                'payment_token',
                'payment_redirect_url',
                'payment_type',
                'payment_transaction_id',
                'paid_at',
                'payment_payload',
            ]);
        });
    }
};
