<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get restaurants to assign categories to
        $restaurants = Restaurant::all();

        if ($restaurants->isEmpty()) {
            $this->command->warn('No restaurants found. Please run RestaurantSeeder first.');

            return;
        }

        $categories = [
            // Common categories for all restaurants
            [
                'name' => [
                    'ar' => 'المقبلات',
                    'en' => 'Appetizers',
                ],
                'active' => true,
                'order' => 1,
                'image' => 'media/products/1.png',
            ],
            [
                'name' => [
                    'ar' => 'الأطباق الرئيسية',
                    'en' => 'Main Dishes',
                ],
                'active' => true,
                'order' => 2,
                'image' => 'media/products/2.png',
            ],
            [
                'name' => [
                    'ar' => 'المشروبات',
                    'en' => 'Beverages',
                ],
                'active' => true,
                'order' => 3,
                'image' => 'media/products/3.png',
            ],
            [
                'name' => [
                    'ar' => 'الحلويات',
                    'en' => 'Desserts',
                ],
                'active' => true,
                'order' => 4,
                'image' => 'media/products/4.png',
            ],
            // // Specific categories based on restaurant type
            // [
            //     'name' => [
            //         'ar' => 'البرجر',
            //         'en' => 'Burgers'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //     'restaurant_types' => ['burger', 'fast_food'],
            //     'image' => 'media/products/5.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'البيتزا',
            //         'en' => 'Pizza'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //     'restaurant_types' => ['italian', 'pizza'],
            //     'image' => 'media/products/6.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'المأكولات البحرية',
            //         'en' => 'Seafood'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //     'restaurant_types' => ['seafood'],
            //     'image' => 'media/products/7.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'القهوة المختصة',
            //         'en' => 'Specialty Coffee'
            //     ],
            //     'active' => true,
            //     'order' => 1,
            //     'restaurant_types' => ['cafe'],
            //     'image' => 'media/products/8.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'الإفطار',
            //         'en' => 'Breakfast'
            //     ],
            //     'active' => true,
            //     'order' => 1,
            //     'restaurant_types' => ['cafe'],
            //     'image' => 'media/products/9.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'المندي والكبسة',
            //         'en' => 'Mandi & Kabsa'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //     'restaurant_types' => ['arabic', 'traditional'],
            //     'image' => 'media/products/10.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'المعجنات',
            //         'en' => 'Pastries'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //         'restaurant_types' => ['cafe', 'bakery'],
            //     'image' => 'media/products/11.png',
            // ],
            // [
            //     'name' => [
            //         'ar' => 'الدجاج المقلي',
            //         'en' => 'Fried Chicken'
            //     ],
            //     'active' => true,
            //     'order' => 2,
            //     'restaurant_types' => ['fast_food', 'chicken'],
            //     'image' => 'media/products/12.png',
            // ],
        ];

        // Restaurant type mapping (simplified based on names)
        $restaurantTypes = [
            'برجر هاوس' => ['burger', 'fast_food'],
            'Burger House' => ['burger', 'fast_food'],
            'مطعم السمك الذهبي' => ['seafood'],
            'Golden Fish Restaurant' => ['seafood'],
            'كافيه الصباح' => ['cafe'],
            'Morning Cafe' => ['cafe'],
            'بيتزا ماستر' => ['italian', 'pizza'],
            'Pizza Master' => ['italian', 'pizza'],
            'مطعم المندي الأصيل' => ['arabic', 'traditional'],
            'Authentic Mandi Restaurant' => ['arabic', 'traditional'],
            'حلويات الجنة' => ['bakery', 'sweets'],
            'Paradise Sweets' => ['bakery', 'sweets'],
            'دجاج كنتاكي' => ['fast_food', 'chicken'],
            'Kentucky Fried Chicken' => ['fast_food', 'chicken'],
            'مقهى الكتاب' => ['cafe'],
            'Book Cafe' => ['cafe'],
        ];

        foreach ($restaurants as $restaurant) {
            $restaurantName = $restaurant->name;
            $types = $restaurantTypes[$restaurantName] ?? ['general'];

            foreach ($categories as $categoryData) {
                // Check if this category should be added to this restaurant
                $shouldAdd = true;

                if (isset($categoryData['restaurant_types'])) {
                    $shouldAdd = ! empty(array_intersect($types, $categoryData['restaurant_types']));
                }

                if ($shouldAdd) {
                    $finalCategoryData = array_merge($categoryData, [
                        'restaurant_id' => $restaurant->id,
                    ]);

                    // Remove the restaurant_types key as it's not needed in the database
                    unset($finalCategoryData['restaurant_types']);

                    Category::create($finalCategoryData);
                }
            }

            // Add some common categories for all restaurants if they don't have specific ones
            if (in_array('general', $types)) {
                $commonCategories = [
                    [
                        'name' => [
                            'ar' => 'العروض الخاصة',
                            'en' => 'Special Offers',
                        ],
                        'active' => true,
                        'order' => 5,
                        'restaurant_id' => $restaurant->id,
                    ],
                    [
                        'name' => [
                            'ar' => 'وجبات الأطفال',
                            'en' => 'Kids Menu',
                        ],
                        'active' => true,
                        'order' => 6,
                        'restaurant_id' => $restaurant->id,
                    ],
                ];

                foreach ($commonCategories as $commonCategory) {
                    Category::create($commonCategory);
                }
            }
        }

        $this->command->info('Category seeder completed successfully!');
    }
}
