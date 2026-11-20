<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductGallerySeeder extends Seeder
{
    /**
     * Apply the swallow and pack imagery to the three swallow products.
     */
    public function run(): void
    {
        $galleries = [
            'elkris-oat-flour' => [
                '/media/products/swallow/oat-flour-swallow-1.jpg',
                '/media/products/swallow/oat-flour-swallow-2.jpg',
                '/media/products/swallow/oat-flour-swallow-3.jpg',
                '/media/products/swallow/oat-flour-pack-650g.jpg',
                '/media/products/swallow/oat-flour-pack-1kg.jpg',
                '/media/products/swallow/oat-flour-pack.jpg',
                '/media/products/swallow/oat-flour-pack-5kg.jpg',
            ],
            'elkris-oat-and-fibre-flour' => [
                '/media/products/swallow/oat-and-fibre-swallow-1.jpg',
                '/media/products/swallow/oat-and-fibre-swallow-2.jpg',
                '/media/products/swallow/oat-and-fibre-pack-2-5kg.jpg',
            ],
            'elkris-instant-plantain-flour' => [
                '/media/products/swallow/plantain-flour-swallow-1.jpg',
                '/media/products/swallow/plantain-flour-swallow-2.jpg',
                '/media/products/swallow/plantain-flour-pack-900g.jpg',
                '/media/products/swallow/plantain-flour-pack-1-8kg.jpg',
            ],
        ];

        foreach ($galleries as $slug => $gallery) {
            Product::query()
                ->where('slug', $slug)
                ->first()
                ?->update(['gallery' => $gallery]);
        }
    }
}
