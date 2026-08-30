<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\Coupon;

class CostikyanSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ──────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@costikyancustomcarpet.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ── Demo customer ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'customer@costikyancustomcarpet.com'],
            [
                'name'              => 'Jane Smith',
                'password'          => Hash::make('password'),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // ── Categories ───────────────────────────────────────────────────
        $cats = [
            ['name' => 'Hand-Knotted',   'description' => 'Artisan hand-knotted rugs from master weavers.'],
            ['name' => 'Hand-Tufted',    'description' => 'Precision hand-tufted rugs with rich pile.'],
            ['name' => 'Machine-Loomed', 'description' => 'Consistent quality machine-loomed rugs.'],
            ['name' => 'Flat-Weave',     'description' => 'Lightweight and reversible flat-weave designs.'],
            ['name' => 'Outdoor',        'description' => 'Weather-resistant rugs for outdoor use.'],
            ['name' => 'Runners',        'description' => 'Long narrow runners for hallways and staircases.'],
        ];

        $categoryModels = [];
        foreach ($cats as $cat) {
            $categoryModels[$cat['name']] = Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug'        => Str::slug($cat['name']),
                    'description' => $cat['description'],
                    'is_active'   => true,
                    'sort_order'  => 0,
                ]
            );
        }

        // ── Products ─────────────────────────────────────────────────────
        $products = [
            [
                'name'           => 'Raye Striped Carpet',
                'description'    => 'A bold, modern stripe pattern in warm earth tones. Hand-knotted in India from 100% New Zealand wool.',
                'price'          => 1200,
                'material'       => 'Wool',
                'origin'         => 'India',
                'dimensions'     => "8' × 10'",
                'style'          => 'Modern',
                'category'       => 'Hand-Knotted',
                'featured'       => true,
                'is_bestseller'  => true,
                'is_new_arrival' => false,
                'colors'         => [['Ivory', '#f0ead6'], ['Navy', '#1e3a5f'], ['Rust', '#b5451b']],
            ],
            [
                'name'           => 'Sofia Rust',
                'description'    => 'Rich terracotta and rust tones woven into a classic Persian-inspired motif.',
                'price'          => 1850,
                'material'       => 'Wool & Silk',
                'origin'         => 'Turkey',
                'dimensions'     => "9' × 12'",
                'style'          => 'Traditional',
                'category'       => 'Hand-Knotted',
                'featured'       => true,
                'is_bestseller'  => false,
                'is_new_arrival' => false,
                'colors'         => [['Rust', '#b5451b'], ['Ivory', '#f0ead6'], ['Gold', '#c9a227']],
            ],
            [
                'name'           => 'Maison Ivory',
                'description'    => 'A soft, neutral ivory field with subtle organic patterning. Perfect for transitional spaces.',
                'price'          => 980,
                'material'       => 'Wool',
                'origin'         => 'India',
                'dimensions'     => "5' × 8'",
                'style'          => 'Transitional',
                'category'       => 'Hand-Tufted',
                'featured'       => true,
                'is_bestseller'  => true,
                'is_new_arrival' => true,
                'colors'         => [['Ivory', '#f5f0e8'], ['Cream', '#f0e6c8']],
            ],
            [
                'name'           => 'Atlas Geometric',
                'description'    => 'Inspired by North African tile patterns. Hand-knotted in Morocco from vegetable-dyed wool.',
                'price'          => 2200,
                'material'       => 'Wool',
                'origin'         => 'Morocco',
                'dimensions'     => "8' × 10'",
                'style'          => 'Geometric',
                'category'       => 'Hand-Knotted',
                'featured'       => false,
                'is_bestseller'  => false,
                'is_new_arrival' => true,
                'colors'         => [['Charcoal', '#404040'], ['Ivory', '#f5f0e8'], ['Terracotta', '#c07050']],
            ],
            [
                'name'           => 'Blossom Flat-Weave',
                'description'    => 'Lightweight and reversible, this dhurrie-style rug brings a casual elegance to any room.',
                'price'          => 650,
                'material'       => 'Cotton',
                'origin'         => 'India',
                'dimensions'     => "6' × 9'",
                'style'          => 'Casual',
                'category'       => 'Flat-Weave',
                'featured'       => false,
                'is_bestseller'  => true,
                'is_new_arrival' => false,
                'colors'         => [['Blue', '#4a7ba7'], ['Blush', '#e8b4a0'], ['White', '#f8f5f0']],
            ],
            [
                'name'           => 'Coastal Runner',
                'description'    => 'A UV-resistant, easy-care runner perfect for entryways and high-traffic halls.',
                'price'          => 420,
                'material'       => 'Polypropylene',
                'origin'         => 'Belgium',
                'dimensions'     => "2'6\" × 12'",
                'style'          => 'Coastal',
                'category'       => 'Runners',
                'featured'       => false,
                'is_bestseller'  => false,
                'is_new_arrival' => true,
                'colors'         => [['Sand', '#d4b896'], ['Navy', '#1e3a5f']],
            ],
            [
                'name'           => 'Verde Outdoor',
                'description'    => 'Power-loomed from solution-dyed acrylic, this rug handles rain, sun, and foot traffic with ease.',
                'price'          => 780,
                'material'       => 'Acrylic',
                'origin'         => 'USA',
                'dimensions'     => "8' × 10'",
                'style'          => 'Modern',
                'category'       => 'Outdoor',
                'featured'       => false,
                'is_bestseller'  => false,
                'is_new_arrival' => false,
                'colors'         => [['Sage', '#7a9e7e'], ['Stone', '#a89880']],
            ],
            [
                'name'           => 'Luxe Silk Touch',
                'description'    => 'A machine-loomed silk-touch rug with a subtle shimmer and extraordinarily soft hand.',
                'price'          => 1100,
                'material'       => 'Viscose',
                'origin'         => 'Belgium',
                'dimensions'     => "8' × 10'",
                'style'          => 'Glam',
                'category'       => 'Machine-Loomed',
                'featured'       => true,
                'is_bestseller'  => false,
                'is_new_arrival' => true,
                'colors'         => [['Silver', '#c0c0c0'], ['Champagne', '#f7e7c0'], ['Charcoal', '#404040']],
            ],
        ];

        foreach ($products as $data) {
            $slug = Str::slug($data['name']);
            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'            => $data['name'],
                    'description'     => $data['description'],
                    'price'           => $data['price'],
                    'stock'           => rand(5, 50),
                    'material'        => $data['material'],
                    'origin'          => $data['origin'],
                    'dimensions'      => $data['dimensions'],
                    'style'           => $data['style'],
                    'category_id'     => $categoryModels[$data['category']]->id,
                    'featured'        => $data['featured'],
                    'is_bestseller'   => $data['is_bestseller'],
                    'is_new_arrival'  => $data['is_new_arrival'],
                    'status'          => 'active',
                ]
            );

            // Colors
            foreach ($data['colors'] as [$colorName, $hex]) {
                ProductColor::firstOrCreate(
                    ['product_id' => $product->id, 'color_name' => $colorName],
                    ['color_hex' => $hex]
                );
            }

            // Placeholder image record (points to a generated placeholder)
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                [
                    'path'       => 'products/placeholder.jpg',
                    'sort_order' => 0,
                ]
            );
        }

        // ── Coupon ───────────────────────────────────────────────────────
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type'      => 'percentage',
                'value'     => 10,
                'min_order' => 500,
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'SAVE100'],
            [
                'type'      => 'fixed',
                'value'     => 100,
                'min_order' => 1000,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Costikyan seeder complete!');
        $this->command->info('   Admin:    admin@costikyancustomcarpet.com / password');
        $this->command->info('   Customer: customer@costikyancustomcarpet.com / password');
    }
}
