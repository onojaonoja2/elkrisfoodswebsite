<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\MealType;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Recipe;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedMealTypes();
        $this->seedCategoriesAndProducts();
        $this->seedRecipes();
        $this->seedArticles();
        $this->seedGalleryItems();
        $this->seedFaqs();
        $this->seedTestimonials();
        $this->seedStores();
    }

    private function seedSettings(): void
    {
        $settings = [
            'brand.primary_tagline' => 'Trusted for Healthy Foods',
            'brand.positioning_tagline' => 'Blood Sugar Friendlier Alternative Swallow',
            'brand.positioning_line' => 'Better Choices. Better Meals. Better Living.',
            'contact.email' => 'info@elkrisfoods.com',
            'contact.phone' => '',
            'contact.address' => 'No. 14 Philomena Street, Santos Estate, Akowonjo, Egbeda, Lagos State, Nigeria',
            'contact.whatsapp' => '07074711387',
            'delivery.info' => 'Orders are fulfilled across Nigeria. Delivery timelines and costs are confirmed on WhatsApp when you place your order.',
            'social.facebook' => 'https://www.facebook.com/Elkrisfoods',
            'social.instagram' => 'https://www.instagram.com/elkrisfoods1',
            'social.tiktok' => 'https://www.tiktok.com/@elkris.foods',
            'social.youtube' => 'https://www.youtube.com/@elkrisfoods',
            'social.x' => '',
            'analytics.ga4_id' => '',
            'analytics.meta_pixel_id' => '',
        ];

        foreach ($settings as $key => $value) {
            if (blank(Setting::get($key))) {
                Setting::set($key, $value);
            }
        }

        if (blank(Setting::get('about.content'))) {
            Setting::set('about.content', (string) json_encode(config('about.content'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }

    private function seedMealTypes(): void
    {
        $mealTypes = [
            ['name' => 'Swallow', 'slug' => 'swallow', 'sort_order' => 1],
            ['name' => 'Breakfast', 'slug' => 'breakfast', 'sort_order' => 2],
            ['name' => 'Baking', 'slug' => 'baking', 'sort_order' => 3],
            ['name' => 'Porridge', 'slug' => 'porridge', 'sort_order' => 4],
        ];

        foreach ($mealTypes as $mealType) {
            MealType::query()->firstOrCreate(['slug' => $mealType['slug']], $mealType);
        }
    }

    private function seedCategoriesAndProducts(): void
    {
        $soups = ['Egusi', 'Okro', 'Vegetable Soup', 'Ogbono', 'Afang', 'Efo Riro'];

        $oatCategory = Category::query()->firstOrCreate(
            ['slug' => 'oat-based-foods'],
            ['name' => 'Oat-Based Foods', 'sort_order' => 1, 'is_published' => true],
        );

        $plantainCategory = Category::query()->firstOrCreate(
            ['slug' => 'plantain-foods'],
            ['name' => 'Plantain Foods', 'sort_order' => 2, 'is_published' => true],
        );

        $breakfastCategory = Category::query()->firstOrCreate(
            ['slug' => 'breakfast-cereals'],
            ['name' => 'Breakfast & Cereals', 'sort_order' => 3, 'is_published' => true],
        );

        $oatFlour = Product::query()->firstOrCreate(
            ['slug' => 'elkris-oat-flour'],
            [
                'category_id' => $oatCategory->id,
                'name' => 'Elkris Oat Flour',
                'tagline' => 'Your versatile everyday oat flour.',
                'description' => 'From delicious swallows to nutritious breakfasts and baking, Elkris Oat Flour is a smarter way to enjoy everyday meals. A fibre-rich option for people making more conscious food choices.',
                'benefits' => [
                    'High in dietary fibre',
                    'Contains beta-glucan, a soluble fibre associated with heart-health benefits as part of a balanced diet',
                    'Good for swallow, breakfast and baking',
                    'Convenient for busy kitchens',
                    'A blood sugar friendly alternative in a balanced diet',
                ],
                'best_with' => $soups,
                'nutrition_notes' => 'Good source of dietary fibre and plant protein. Prepared as part of a balanced diet.',
                'whatsapp' => '07074868813',
                'hero_image' => '/media/products/oat-flour-1.3kg.jpg',
                'gallery' => [
                    '/media/products/swallow/oat-flour-swallow-1.jpg',
                    '/media/products/swallow/oat-flour-swallow-2.jpg',
                    '/media/products/swallow/oat-flour-swallow-3.jpg',
                    '/media/products/swallow/oat-flour-pack-650g.jpg',
                    '/media/products/swallow/oat-flour-pack-1kg.jpg',
                    '/media/products/swallow/oat-flour-pack.jpg',
                    '/media/products/swallow/oat-flour-pack-5kg.jpg',
                ],
                'sort_order' => 1,
                'is_published' => true,
                'is_featured' => true,
            ],
        );

        $oatFibre = Product::query()->firstOrCreate(
            ['slug' => 'elkris-oat-and-fibre-flour'],
            [
                'category_id' => $oatCategory->id,
                'name' => 'Elkris Oat & Fibre Flour',
                'tagline' => 'Extra fibre for fibre-conscious meals.',
                'description' => 'Elkris Oat & Fibre Flour blends oats with added fibre for families who want more fibre in their everyday meals. Perfect for swallow and hearty breakfasts.',
                'benefits' => [
                    'High in dietary fibre',
                    'Contains beta-glucan from oats',
                    'Great for swallow',
                    'A blood sugar friendly alternative in a balanced diet',
                ],
                'best_with' => $soups,
                'nutrition_notes' => 'Higher fibre blend. Enjoy as part of a balanced diet.',
                'whatsapp' => '07074892727',
                'hero_image' => '/media/products/oat-and-fiber-flour.png',
                'gallery' => [
                    '/media/products/swallow/oat-and-fibre-swallow-1.jpg',
                    '/media/products/swallow/oat-and-fibre-swallow-2.jpg',
                    '/media/products/swallow/oat-and-fibre-pack-2-5kg.jpg',
                ],
                'sort_order' => 2,
                'is_published' => true,
                'is_featured' => true,
            ],
        );

        $plantain = Product::query()->firstOrCreate(
            ['slug' => 'elkris-instant-plantain-flour'],
            [
                'category_id' => $plantainCategory->id,
                'name' => 'Elkris Instant Plantain Flour',
                'tagline' => 'Instant plantain swallow in minutes.',
                'description' => 'Make soft, satisfying plantain swallow without the wait. Elkris Instant Plantain Flour is convenient, naturally delicious and made for real Nigerian kitchens.',
                'benefits' => [
                    'Ready in minutes',
                    'No soaking or grinding needed',
                    'Great for swallow meals',
                    'A blood sugar friendly alternative in a balanced diet',
                ],
                'best_with' => $soups,
                'nutrition_notes' => 'Made from plantain. Enjoy as part of a balanced diet.',
                'whatsapp' => '09139584190',
                'hero_image' => '/media/products/plantain-flour-1.8kg.jpg',
                'gallery' => [
                    '/media/products/swallow/plantain-flour-swallow-1.jpg',
                    '/media/products/swallow/plantain-flour-swallow-2.jpg',
                    '/media/products/swallow/plantain-flour-pack-900g.jpg',
                    '/media/products/swallow/plantain-flour-pack-1-8kg.jpg',
                ],
                'sort_order' => 3,
                'is_published' => true,
                'is_featured' => true,
            ],
        );

        Product::query()->firstOrCreate(
            ['slug' => 'elkris-premium-flours'],
            [
                'category_id' => $breakfastCategory->id,
                'name' => 'Elkris Premium Flours',
                'tagline' => 'Everyday flour for baking and cooking.',
                'description' => 'Reliable everyday flour for your baking, cooking and family meal needs.',
                'benefits' => ['Consistent quality', 'Perfect for baking', 'Family-size packs'],
                'whatsapp' => '07074711387',
                'best_with' => [],
                'hero_image' => '/media/products/plain-flour-bowl.jpg',
                'sort_order' => 4,
                'is_published' => true,
                'is_featured' => false,
            ],
        );

        $variants = [
            [$oatFlour, '650g', 'single', 2500, 'ELK-OAT-650'],
            [$oatFlour, '1.3kg', 'single', 3980, 'ELK-OAT-1300'],
            [$oatFlour, '5kg', 'single', 15500, 'ELK-OAT-5000'],
            [$oatFlour, '650g', 'carton', 41500, 'ELK-OAT-650-CTN'],
            [$oatFibre, '1kg', 'single', 3200, 'ELK-FIB-1000'],
            [$oatFibre, '2.5kg', 'single', 7500, 'ELK-FIB-2500'],
            [$plantain, '900g', 'single', 2900, 'ELK-PLN-900'],
            [$plantain, '1.8kg', 'single', 5200, 'ELK-PLN-1800'],
        ];

        foreach ($variants as [$product, $size, $pack, $price, $sku]) {
            ProductVariant::query()->firstOrCreate(
                ['product_id' => $product->id, 'size_label' => $size, 'pack_type' => $pack],
                ['price' => $price, 'sku' => $sku, 'is_available' => true],
            );
        }

        $swallow = MealType::query()->where('slug', 'swallow')->first();
        $breakfast = MealType::query()->where('slug', 'breakfast')->first();
        $baking = MealType::query()->where('slug', 'baking')->first();
        $porridge = MealType::query()->where('slug', 'porridge')->first();

        $oatFlour->mealTypes()->syncWithoutDetaching([
            $swallow?->id,
            $breakfast?->id,
            $baking?->id,
            $porridge?->id,
        ]);
        $oatFibre->mealTypes()->syncWithoutDetaching([$swallow?->id, $breakfast?->id, $porridge?->id]);
        $plantain->mealTypes()->syncWithoutDetaching([$swallow?->id, $porridge?->id]);
    }

    private function seedRecipes(): void
    {
        $products = Product::query()
            ->whereIn('slug', [
                'elkris-oat-flour',
                'elkris-oat-and-fibre-flour',
                'elkris-instant-plantain-flour',
            ])
            ->get()
            ->keyBy('slug');

        $recipes = [
            [
                'product' => 'elkris-oat-flour',
                'image_path' => '/media/lifestyle/woman-with-products.png',
                'title' => 'Elkris Oat Swallow + Egusi Soup',
                'slug' => 'elkris-oat-swallow-egusi',
                'category' => 'Swallow Recipes',
                'prep_minutes' => 10,
                'servings' => 4,
                'difficulty' => 'Easy',
                'summary' => 'Soft oat swallow ready in 10 minutes. Perfect with rich egusi soup.',
                'ingredients' => ['1 cup Elkris Oat Flour', '2 cups water', 'Pinch of salt'],
                'steps' => [
                    'Bring 2 cups of water to a boil with a pinch of salt.',
                    'Reduce heat and gradually whisk in Elkris Oat Flour.',
                    'Stir continuously until you get a smooth, firm dough.',
                    'Scoop and serve hot with your favourite soup.',
                ],
                'tips' => 'Add the flour gradually and keep stirring. That is the secret to a lump-free swallow.',
            ],
            [
                'product' => 'elkris-oat-flour',
                'image_path' => '/media/products/oat-flour-650g.jpg',
                'title' => 'Quick Oat Breakfast Porridge',
                'slug' => 'quick-oat-breakfast-porridge',
                'category' => 'Breakfast Recipes',
                'prep_minutes' => 8,
                'servings' => 2,
                'difficulty' => 'Easy',
                'summary' => 'A warm, fibre-rich porridge to start the family day right.',
                'ingredients' => ['3 tbsp Elkris Oat Flour', '1 cup milk or water', 'Sweetener to taste', 'Pinch of cinnamon'],
                'steps' => [
                    'Heat milk or water in a pot.',
                    'Whisk in Elkris Oat Flour until smooth.',
                    'Cook for 5 minutes, stirring occasionally.',
                    'Sweeten to taste and serve warm.',
                ],
                'tips' => 'Pair with sliced banana or groundnuts for extra texture and staying power.',
            ],
            [
                'product' => 'elkris-instant-plantain-flour',
                'image_path' => '/media/products/plantain-flour-900g.jpg',
                'title' => 'Soft Plantain Swallow',
                'slug' => 'soft-plantain-swallow',
                'category' => 'Swallow Recipes',
                'prep_minutes' => 7,
                'servings' => 4,
                'difficulty' => 'Easy',
                'summary' => 'Smooth plantain swallow in under 10 minutes with Elkris Instant Plantain Flour.',
                'ingredients' => ['1 cup Elkris Instant Plantain Flour', '2 cups water'],
                'steps' => [
                    'Boil water in a non-stick pot.',
                    'Add flour gradually while stirring.',
                    'Mix until smooth and firm.',
                    'Mould and serve with soup.',
                ],
                'tips' => 'No soaking or grinding needed. The instant flour does the work for you.',
            ],
            [
                'product' => 'elkris-oat-and-fibre-flour',
                'image_path' => '/media/products/oat-and-fiber-flour.png',
                'title' => 'Oat & Fibre Swallow with Ogbono',
                'slug' => 'oat-fibre-swallow-ogbono',
                'category' => 'Swallow Recipes',
                'prep_minutes' => 12,
                'servings' => 4,
                'difficulty' => 'Easy',
                'summary' => 'A fibre-conscious swallow made with Elkris Oat & Fibre Flour, paired with ogbono soup.',
                'ingredients' => ['1 cup Elkris Oat & Fibre Flour', '2 cups water', 'Pinch of salt'],
                'steps' => [
                    'Boil the water with a pinch of salt.',
                    'Stream in the flour while stirring.',
                    'Cook until you have a smooth, firm dough.',
                    'Serve hot with ogbono soup.',
                ],
                'tips' => 'Let the water come to a full boil before adding the flour for the smoothest texture.',
            ],
            [
                'product' => 'elkris-oat-flour',
                'image_path' => '/media/lifestyle/elkrisProductBanner.png',
                'title' => 'Oat Banana Pancakes',
                'slug' => 'oat-banana-pancakes',
                'category' => 'Breakfast Recipes',
                'prep_minutes' => 15,
                'servings' => 3,
                'difficulty' => 'Easy',
                'summary' => 'Fluffy oat pancakes sweetened with ripe banana. A weekend favourite the whole family enjoys.',
                'ingredients' => ['1 cup Elkris Oat Flour', '1 ripe banana', '1 egg', '1 cup milk', 'Pinch of salt'],
                'steps' => [
                    'Mash the banana in a bowl.',
                    'Whisk in Elkris Oat Flour, egg, milk and salt to form a batter.',
                    'Heat a lightly greased pan over medium heat.',
                    'Pour small scoops of batter and cook until golden on both sides.',
                ],
                'tips' => 'Rest the batter for 5 minutes before cooking for fluffier pancakes.',
            ],
            [
                'product' => 'elkris-oat-flour',
                'image_path' => '/media/lifestyle/woman-oat-and-fiber.png',
                'title' => 'Crunchy Elkris Oat Cookies',
                'slug' => 'crunchy-elkris-oat-cookies',
                'category' => 'Baking',
                'prep_minutes' => 25,
                'servings' => 12,
                'difficulty' => 'Medium',
                'summary' => 'Simple, crunchy cookies baked with Elkris Oat Flour. Great with tea or as a lunchbox treat.',
                'ingredients' => ['1 cup Elkris Oat Flour', '1/2 cup butter or margarine', '1/3 cup sugar', '1 egg', 'Pinch of salt'],
                'steps' => [
                    'Cream the butter and sugar until light.',
                    'Beat in the egg, then fold in Elkris Oat Flour and salt.',
                    'Shape into small balls and flatten on a lined tray.',
                    'Bake at 180°C for 12 to 15 minutes until golden at the edges.',
                ],
                'tips' => 'Take them out while the centres still look slightly soft, as they firm up as they cool.',
            ],
        ];

        foreach ($recipes as $recipe) {
            $productSlug = $recipe['product'];
            unset($recipe['product']);

            Recipe::query()->firstOrCreate(
                ['slug' => $recipe['slug']],
                $recipe + ['product_id' => $products->get($productSlug)?->id, 'is_published' => true],
            );
        }
    }

    private function seedArticles(): void
    {
        $articles = [
            [
                'title' => 'Fibre-Rich Meals: Why Better Choices Start on Your Plate',
                'slug' => 'fibre-rich-meals-better-choices',
                'category' => 'Nutrition',
                'reading_minutes' => 4,
                'excerpt' => 'Fibre keeps you fuller for longer and supports a balanced diet. Here is how to get more of it into everyday Nigerian meals.',
                'body' => "Fibre is one of the simplest upgrades you can make to everyday eating. It supports digestion, helps you feel fuller for longer and forms part of a balanced diet.\n\nMany staple meals can carry more fibre with small changes: reach for whole-grain and oat-based flours instead of refined options, add beans or vegetables to your sauces, and keep fruit within reach for snacks.\n\nElkris Oat Flour and Elkris Oat & Fibre Flour are fibre-rich options for people making more conscious food choices. Use them for swallow, porridge and baking without changing how your family eats.",
                'author' => 'Elkris Foods',
            ],
            [
                'title' => 'Blood Sugar Friendly Eating: Practical Swaps for Nigerian Kitchens',
                'slug' => 'blood-sugar-friendly-eating-swaps',
                'category' => 'Blood Sugar Friendly Living',
                'reading_minutes' => 5,
                'excerpt' => 'You do not have to abandon the meals you love. A few thoughtful swaps can make everyday plates more blood sugar friendly.',
                'body' => "Eating in a way that is friendly to blood sugar does not mean giving up swallow, porridge or your favourite soups. It means making a few deliberate swaps.\n\nStart with your flour. Oat-based and plantain-based options are fibre-rich alternatives to refined flours, and fibre slows how quickly sugars are absorbed after a meal. Pair your swallow with vegetable-rich soups rather than heavy palm oil sauces, and keep portions of starchy staples steady while loading up on protein and vegetables.\n\nThe goal is consistency, not perfection. Better choices, better meals, better living, one plate at a time. If you are managing a medical condition, work with your healthcare provider on a plan that suits you.",
                'author' => 'Elkris Foods',
            ],
            [
                'title' => 'What Is Beta-Glucan, and Why Does It Matter?',
                'slug' => 'what-is-beta-glucan',
                'category' => 'Nutrition',
                'reading_minutes' => 3,
                'excerpt' => 'Beta-glucan is the soluble fibre found in oats. Here is what the science says, and what it does not say.',
                'body' => "Beta-glucan is a soluble fibre naturally present in oats. Soluble fibres form a gel-like substance in the digestive tract, which is one reason oat-based meals keep you satisfied for longer.\n\nResearch links beta-glucan with heart-health benefits as part of a balanced diet, and it is this soluble fibre that makes oats a popular choice for people who pay attention to how their meals are put together.\n\nIt is a food, not a medicine. Elkris products are nutritious staples to enjoy as part of a balanced diet, and they are not intended to diagnose, treat or cure any condition.",
                'author' => 'Elkris Foods',
            ],
            [
                'title' => 'How to Make Soft Swallow Every Single Time',
                'slug' => 'how-to-make-soft-swallow',
                'category' => 'Product Education',
                'reading_minutes' => 4,
                'excerpt' => 'Lumpy swallow is avoidable. Follow this simple method for smooth, firm swallow with Elkris flours.',
                'body' => "The difference between lumpy and silky swallow usually comes down to technique, not talent.\n\nFirst, bring your water to a full boil before the flour goes in. Second, reduce the heat and add the flour gradually while stirring. Never dump it in all at once. Third, keep stirring with your turning stick until the dough pulls away from the pot and feels firm but soft.\n\nElkris Instant Plantain Flour is ready in about 7 minutes, and Elkris Oat Flour takes about 10. Serve immediately with egusi, okro, ogbono, afang or efo riro.",
                'author' => 'Elkris Foods',
            ],
            [
                'title' => '5 Ways to Use Elkris Oat Flour This Week',
                'slug' => '5-ways-to-use-elkris-oat-flour',
                'category' => 'Product Education',
                'reading_minutes' => 3,
                'excerpt' => 'One pack, many possibilities, from swallow to cookies. Here are five ways to put your oat flour to work.',
                'body' => "Elkris Oat Flour earns its spot in the kitchen because it is genuinely versatile.\n\n1. Swallow: soft, smooth and ready in about 10 minutes with your favourite soup.\n2. Porridge: a warm breakfast with milk, a pinch of cinnamon and fruit.\n3. Pancakes: mix with egg, milk and banana for fluffy weekend pancakes.\n4. Baking: substitute part of the wheat flour in cookies, muffins and cakes.\n5. Thickener: whisk a spoonful into soups and stews for body and fibre.\n\nHowever you use it, you are choosing a fibre-rich option for more conscious food choices.",
                'author' => 'Elkris Foods',
            ],
        ];

        foreach ($articles as $article) {
            $article['body'] = $this->articleBodyToHtml($article['body']);

            Article::query()->firstOrCreate(
                ['slug' => $article['slug']],
                $article + [
                    'cover_path' => '/media/lifestyle/woman-with-products.png',
                    'meta_description' => $article['excerpt'],
                    'is_published' => true,
                    'published_at' => now()->subDays(count($articles) * 3),
                ],
            );
        }
    }

    private function articleBodyToHtml(string $body): string
    {
        $paragraphs = preg_split('/\R{2,}/', trim($body)) ?: [];

        return collect($paragraphs)
            ->map(fn (string $paragraph): string => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph): string => '<p>'.nl2br(e($paragraph), false).'</p>')
            ->implode('');
    }

    private function seedGalleryItems(): void
    {
        $items = [
            ['title' => 'Elkris Brand Story', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/brand-positioning.mp4', 'caption' => 'Who we are and what we stand for.', 'sort_order' => 1],
            ['title' => 'Blood Sugar Friendly Swallow', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/blood-sugar-friendly-swallow.mp4', 'caption' => 'Better choices, better meals, better living.', 'sort_order' => 2],
            ['title' => 'How to Prepare Elkris Swallow', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/preparation.mp4', 'caption' => 'Smooth swallow in minutes. Watch the method.', 'sort_order' => 3],
            ['title' => 'Elkris Oat Flour', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/oat-flour.mp4', 'caption' => 'Your versatile everyday oat flour.', 'sort_order' => 4],
            ['title' => 'Elkris Instant Plantain Flour', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/instant-plantain-flour.mp4', 'caption' => 'Instant plantain swallow in minutes.', 'sort_order' => 5],
            ['title' => 'Elkris TV Commercial', 'category' => 'Media', 'media_type' => 'video', 'media_path' => '/media/videos/voiceover.mp4', 'caption' => 'Trusted for healthy foods.', 'sort_order' => 6],
            ['title' => 'Elkris Product Range', 'category' => 'Products', 'media_path' => '/media/products/product-range.jpg', 'caption' => 'The full Elkris family of flours.', 'sort_order' => 7],
            ['title' => 'Elkris Oat Flour 1.3kg', 'category' => 'Products', 'media_path' => '/media/products/oat-flour-1.3kg.jpg', 'sort_order' => 8],
            ['title' => 'Elkris Oat & Fibre Flour', 'category' => 'Products', 'media_path' => '/media/products/oat-and-fiber-flour.png', 'sort_order' => 9],
            ['title' => 'Elkris Instant Plantain Flour', 'category' => 'Products', 'media_path' => '/media/products/plantain-flour-1.8kg.jpg', 'sort_order' => 10],
            ['title' => 'Everyday meals with Elkris', 'category' => 'Customers', 'media_path' => '/media/lifestyle/woman-with-products.png', 'caption' => 'Real kitchens, real families, better choices.', 'sort_order' => 11],
            ['title' => 'Oat & Fibre lifestyle', 'category' => 'Customers', 'media_path' => '/media/lifestyle/woman-oat-and-fiber.png', 'sort_order' => 12],
            ['title' => 'Elkris in the wild', 'category' => 'Activations', 'media_path' => '/media/lifestyle/elkrisProductBanner.png', 'sort_order' => 13],
        ];

        foreach ($items as $item) {
            GalleryItem::query()->firstOrCreate(
                ['title' => $item['title'], 'media_path' => $item['media_path']],
                $item + ['media_type' => GalleryItem::MEDIA_IMAGE, 'is_published' => true],
            );
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['question' => 'Is Elkris Oat Flour suitable for breakfast?', 'answer' => 'Yes. Use it for porridge, smoothies and other breakfast favourites. It is a fibre-rich option for people making more conscious food choices.', 'category' => 'Products', 'sort_order' => 1],
            ['question' => 'Can I use Elkris Oat Flour for swallow?', 'answer' => 'Absolutely. Elkris Oat Flour makes soft, satisfying swallow that pairs perfectly with egusi, okro, ogbono and your other favourite soups.', 'category' => 'Products', 'sort_order' => 2],
            ['question' => 'Can I bake with Elkris products?', 'answer' => 'Yes. Elkris Oat Flour works beautifully in cookies, cakes, biscuits and other baked goods.', 'category' => 'Products', 'sort_order' => 3],
            ['question' => 'Where can I buy Elkris?', 'answer' => 'Elkris is available in stores and supermarkets across Nigeria. Visit our Where to Buy page to find a retailer near you, or order via WhatsApp.', 'category' => 'Stores', 'sort_order' => 4],
            ['question' => 'What sizes are available?', 'answer' => 'Sizes vary by product, from handy 650g packs to family 5kg packs and cartons. Check each product page for available sizes.', 'category' => 'Products', 'sort_order' => 5],
            ['question' => 'How do I become a distributor?', 'answer' => 'We would love to have you on the team. Fill out the Become a Distributor form and our team will contact you.', 'category' => 'Orders', 'sort_order' => 6],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->firstOrCreate(['question' => $faq['question']], $faq + ['is_published' => true]);
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'Adaeze O.', 'location' => 'Lagos', 'quote' => 'Elkris Oat Flour changed our breakfast routine. The porridge is smooth and the kids love it.', 'is_featured' => true],
            ['name' => 'Tunde K.', 'location' => 'Abuja', 'quote' => 'I switched to Elkris for my swallow and I am not going back. Easy to make and tastes great with ogbono.', 'is_featured' => true],
            ['name' => 'Ngozi E.', 'location' => 'Port Harcourt', 'quote' => 'Finally a blood sugar friendly alternative that still feels like real Nigerian food.', 'is_featured' => true],
            ['name' => 'Funke A.', 'location' => 'Ibadan', 'quote' => 'My children ask for Elkris pancakes every Saturday. Quick to make and I feel good serving it.', 'is_featured' => true],
            ['name' => 'Chidi O.', 'location' => 'Enugu', 'quote' => 'Elkris Plantain Flour makes a smooth, tasty swallow. It pairs so well with vegetable soup.', 'is_featured' => true],
            ['name' => 'Amina S.', 'location' => 'Kano', 'quote' => 'Baking with Elkris Oat Flour is now part of my routine. Soft muffins with real goodness inside.', 'is_featured' => true],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::query()->firstOrCreate(
                ['name' => $testimonial['name'], 'quote' => $testimonial['quote']],
                $testimonial + ['is_published' => true],
            );
        }
    }

    private function seedStores(): void
    {
        $stores = [
            ['name' => 'Shoprite, Jabi Lake Mall', 'address' => 'Jabi Lake Mall, Jabi', 'city' => 'Abuja', 'state' => 'Abuja FCT', 'phone' => '', 'opening_hours' => 'Mon - Sun: 8am - 9pm'],
            ['name' => 'Shoprite, Ikeja City Mall', 'address' => 'Ikeja City Mall, Alausa', 'city' => 'Ikeja', 'state' => 'Lagos', 'phone' => '', 'opening_hours' => 'Mon - Sun: 8am - 9pm'],
            ['name' => 'Ebeano, Lekki', 'address' => '10 Chevron Drive, Lekki', 'city' => 'Lekki', 'state' => 'Lagos', 'phone' => '', 'opening_hours' => 'Mon - Sun: 8am - 10pm'],
            ['name' => 'Shoprite, Port Harcourt Mall', 'address' => 'Port Harcourt Mall, Azikiwe Street', 'city' => 'Port Harcourt', 'state' => 'Rivers', 'phone' => '', 'opening_hours' => 'Mon - Sun: 8am - 9pm'],
        ];

        foreach ($stores as $store) {
            Store::query()->firstOrCreate(
                ['address' => $store['address']],
                $store + ['is_published' => true],
            );
        }

        // Clean up legacy duplicates seeded before the em dash cleanup,
        // when the lookup key included the store name.
        Store::query()
            ->where('name', 'like', '%—%')
            ->whereIn('address', array_column($stores, 'address'))
            ->delete();
    }
}
