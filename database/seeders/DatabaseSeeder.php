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
            [$kopi, 'Cappuccino', 'Espresso dengan susu steamed yang lembut.', 28000, true, 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=900&q=85'],
            [$kopi, 'Iced Latte', 'Espresso dingin dengan susu segar.', 30000, false, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85'],
            [$non, 'Matcha Latte', 'Perpaduan matcha premium dan susu.', 32000, false, 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?auto=format&fit=crop&w=900&q=85'],
            [$food, 'Strawberry Cheesecake', 'Kelezatan cheesecake dengan saus stroberi.', 35000, false, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=900&q=85'],
            [$kopi, 'Espresso', 'Single shot espresso pekat.', 20000, false, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=85'],
            [$kopi, 'Americano', 'Espresso dengan air panas.', 22000, false, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85'],
            [$kopi, 'Caramel Macchiato', 'Espresso, susu, dan saus karamel.', 34000, false, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85'],
            [$non, 'Lemon Tea', 'Teh segar dengan perasan lemon.', 20000, false, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=900&q=85'],
            [$food, 'Chocolate Cake', 'Cake cokelat lembut dengan ganache.', 33000, false, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=85'],
            [$food, 'Croissant Butter', 'Croissant renyah berbahan butter.', 25000, false, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=900&q=85'],
            [$food, 'French Fries', 'Kentang goreng renyah.', 22000, false, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85'],
        ];
        foreach ($items as [$cat, $name, $desc, $price, $best, $image]) {
            Product::create([
                'category_id' => $cat->id, 'name' => $name, 'slug' => Slug::make(Product::class, $name),
                'description' => $desc, 'image' => $image, 'price' => $price, 'is_best_seller' => $best,
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
