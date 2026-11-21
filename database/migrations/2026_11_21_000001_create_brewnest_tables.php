<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('cashier');
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->string('image')->nullable();
            $t->decimal('price', 12, 2);
            $t->boolean('is_available')->default(true);
            $t->boolean('is_active')->default(true);
            $t->boolean('is_best_seller')->default(false);
            $t->timestamps();
        });

        Schema::create('cafe_tables', function (Blueprint $t) {
            $t->id();
            $t->string('table_number')->unique();
            $t->string('qr_token')->unique();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('promotions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->string('discount_type')->default('percentage'); // percentage | fixed
            $t->decimal('discount_value', 12, 2)->default(0);
            $t->dateTime('starts_at');
            $t->dateTime('ends_at');
            $t->text('terms')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_number')->unique();
            $t->string('customer_name')->nullable();
            $t->string('customer_phone')->nullable();
            $t->string('order_type')->default('preorder'); // preorder | dine_in
            $t->unsignedBigInteger('cafe_table_id')->nullable();
            $t->string('order_status')->default('pending');
            $t->string('payment_status')->default('unpaid');
            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('discount_total', 12, 2)->default(0);
            $t->decimal('grand_total', 12, 2)->default(0);
            $t->unsignedBigInteger('promotion_id')->nullable();
            $t->string('promotion_name')->nullable();
            $t->text('customer_note')->nullable();
            $t->dateTime('pickup_at')->nullable();
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('product_id')->nullable();
            $t->string('product_name');
            $t->decimal('unit_price', 12, 2);
            $t->unsignedInteger('quantity');
            $t->decimal('line_total', 12, 2);
            $t->text('note')->nullable();
            $t->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('type')->default('status'); // status | payment
            $t->string('old_value')->nullable();
            $t->string('new_value');
            $t->timestamps();
        });

        Schema::create('reservations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone');
            $t->dateTime('reserved_at');
            $t->unsignedInteger('guests')->default(2);
            $t->text('note')->nullable();
            $t->string('status')->default('pending'); // pending|confirmed|cancelled|done
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['reservations', 'order_status_histories', 'order_items', 'orders', 'promotions', 'cafe_tables', 'products', 'categories'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn('role');
        });
    }
};
