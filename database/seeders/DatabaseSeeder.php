<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use App\Support\Slug;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Admin Arana Space', 'email' => 'admin@aranaspace.id', 'password' => 'admin12345', 'role' => 'admin']);
        User::create(['name' => 'Kasir Arana Space', 'email' => 'kasir@aranaspace.id', 'password' => 'kasir12345', 'role' => 'cashier']);
        User::create(['name' => 'Barista Arana Space', 'email' => 'barista@aranaspace.id', 'password' => 'barista12345', 'role' => 'kitchen']);

        $kopi = Category::create(['name' => 'Kopi', 'description' => 'Berbagai olahan espresso']);
        $non = Category::create(['name' => 'Non-Kopi', 'description' => 'Minuman segar dan teh']);
        $food = Category::create(['name' => 'Makanan Ringan', 'description' => 'Cake dan camilan']);

        $items = [
            [$kopi, 'Cappuccino', 'Espresso dengan susu steamed yang lembut.', 28000, true],
            [$kopi, 'Iced Latte', 'Espresso dingin dengan susu segar.', 30000, false],
            [$non, 'Matcha Latte', 'Perpaduan matcha premium dan susu.', 32000, false],
            [$food, 'Strawberry Cheesecake', 'Kelezatan cheesecake dengan saus stroberi.', 35000, false],
            [$kopi, 'Espresso', 'Single shot espresso pekat.', 20000, false],
            [$kopi, 'Americano', 'Espresso dengan air panas.', 22000, false],
            [$kopi, 'Caramel Macchiato', 'Espresso, susu, dan saus karamel.', 34000, false],
            [$non, 'Lemon Tea', 'Teh segar dengan perasan lemon.', 20000, false],
            [$food, 'Chocolate Cake', 'Cake cokelat lembut dengan ganache.', 33000, false],
            [$food, 'Croissant Butter', 'Croissant renyah berbahan butter.', 25000, false],
            [$food, 'French Fries', 'Kentang goreng renyah.', 22000, false],
        ];
        foreach ($items as [$cat, $name, $desc, $price, $best]) {
            Product::create([
                'category_id' => $cat->id, 'name' => $name, 'slug' => Slug::make(Product::class, $name),
                'description' => $desc, 'price' => $price, 'is_best_seller' => $best,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            CafeTable::create(['table_number' => (string) $i, 'qr_token' => Str::random(40)]);
        }

        Promotion::create([
            'name' => 'Diskon 20% untuk Semua Menu',
            'slug' => 'diskon-20-semua-menu',
            'description' => 'Nikmati diskon 20% untuk semua menu. Hanya periode terbatas, jangan sampai kelewatan!',
            'discount_type' => 'percentage', 'discount_value' => 20,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(30),
            'terms' => 'Berlaku untuk pemesanan online dan dari meja. Tidak dapat digabung dengan promo lain.',
        ]);
    }
}
