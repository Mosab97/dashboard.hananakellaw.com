<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            [
                'name' => [
                    'ar' => 'مطعم الضيافة الشامية',
                    'en' => 'Al Diafa Shamiya Restaurant',
                ],
                'description' => [
                    'ar' => 'مطعم تراثي يقدم أشهى الأطباق الشامية والعربية التقليدية في أجواء أصيلة ومريحة',
                    'en' => 'Traditional restaurant serving delicious Levantine and Arabic cuisine in an authentic and comfortable atmosphere',
                ],
                'slug' => 'al-diafa-shamiya',
                'active' => true,
                'address' => 'شارع الملك فهد، الرياض، المملكة العربية السعودية',
                'phone' => '+966 11 234 5678',
                'email' => 'info@aldiafashamiya.com',
                'website' => 'https://aldiafashamiya.com',
                'opening_hours' => [
                    'saturday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'sunday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'monday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'tuesday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'wednesday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'thursday' => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
                    'friday' => ['open' => '14:00', 'close' => '01:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'برجر هاوس',
                    'en' => 'Burger House',
                ],
                'description' => [
                    'ar' => 'سلسلة مطاعم الوجبات السريعة المتخصصة في تقديم أفضل أنواع البرجر والبطاطس المقرمشة',
                    'en' => 'Fast food chain specializing in serving the best burgers and crispy fries',
                ],
                'slug' => 'burger-house',
                'active' => true,
                'address' => 'العليا، الرياض، المملكة العربية السعودية',
                'phone' => '+966 11 345 6789',
                'email' => 'contact@burgerhouse.sa',
                'website' => 'https://burgerhouse.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'sunday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'monday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'tuesday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'wednesday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'thursday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'friday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'مطعم السمك الذهبي',
                    'en' => 'Golden Fish Restaurant',
                ],
                'description' => [
                    'ar' => 'مطعم متخصص في المأكولات البحرية الطازجة مع إطلالة رائعة على البحر',
                    'en' => 'Restaurant specializing in fresh seafood with a stunning sea view',
                ],
                'slug' => 'golden-fish',
                'active' => true,
                'address' => 'كورنيش جدة، جدة، المملكة العربية السعودية',
                'phone' => '+966 12 456 7890',
                'email' => 'reservations@goldenfish.sa',
                'website' => 'https://goldenfish.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                    'sunday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                    'monday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                    'tuesday' => ['closed' => true],
                    'wednesday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                    'thursday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                    'friday' => ['open' => '12:00', 'close' => '00:00', 'closed' => false],
                ],
                'delivery_available' => false,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'كافيه الصباح',
                    'en' => 'Morning Cafe',
                ],
                'description' => [
                    'ar' => 'كافيه عصري يقدم أجود أنواع القهوة والحلويات والإفطار الشهي',
                    'en' => 'Modern cafe serving premium coffee, pastries, and delicious breakfast',
                ],
                'slug' => 'morning-cafe',
                'active' => true,
                'address' => 'حي السلام، الدمام، المملكة العربية السعودية',
                'phone' => '+966 13 567 8901',
                'email' => 'hello@morningcafe.sa',
                'website' => null,
                'opening_hours' => [
                    'saturday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'sunday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'monday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'tuesday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'wednesday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'thursday' => ['open' => '06:00', 'close' => '22:00', 'closed' => false],
                    'friday' => ['open' => '07:00', 'close' => '23:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'مطعم المندي الأصيل',
                    'en' => 'Authentic Mandi Restaurant',
                ],
                'description' => [
                    'ar' => 'مطعم يقدم المندي اليمني الأصيل بأسلوب تقليدي وطعم لا يُقاوم',
                    'en' => 'Restaurant serving authentic Yemeni Mandi with traditional methods and irresistible taste',
                ],
                'slug' => 'authentic-mandi',
                'active' => true,
                'address' => 'طريق الملك عبدالعزيز، مكة المكرمة، المملكة العربية السعودية',
                'phone' => '+966 12 678 9012',
                'email' => 'info@authenticmandi.sa',
                'website' => 'https://authenticmandi.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'sunday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'monday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'tuesday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'wednesday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'thursday' => ['open' => '11:30', 'close' => '01:00', 'closed' => false],
                    'friday' => ['open' => '12:00', 'close' => '02:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'بيتزا ماستر',
                    'en' => 'Pizza Master',
                ],
                'description' => [
                    'ar' => 'مطعم إيطالي يقدم أشهى أنواع البيتزا والمعكرونة الإيطالية الأصيلة',
                    'en' => 'Italian restaurant serving delicious authentic Italian pizza and pasta',
                ],
                'slug' => 'pizza-master',
                'active' => true,
                'address' => 'شارع التحلية، الرياض، المملكة العربية السعودية',
                'phone' => '+966 11 789 0123',
                'email' => 'orders@pizzamaster.sa',
                'website' => 'https://pizzamaster.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'sunday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'monday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'tuesday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'wednesday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'thursday' => ['open' => '12:00', 'close' => '01:00', 'closed' => false],
                    'friday' => ['open' => '13:00', 'close' => '02:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'حلويات الجنة',
                    'en' => 'Paradise Sweets',
                ],
                'description' => [
                    'ar' => 'محل حلويات شرقية وغربية بأجود المكونات وأطيب الطعمات',
                    'en' => 'Eastern and Western sweets shop with the finest ingredients and best flavors',
                ],
                'slug' => 'paradise-sweets',
                'active' => false, // Temporarily closed
                'address' => 'حي الفيصلية، جدة، المملكة العربية السعودية',
                'phone' => '+966 12 890 1234',
                'email' => 'info@paradisesweets.sa',
                'website' => null,
                'opening_hours' => [
                    'saturday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'sunday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'monday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'tuesday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'wednesday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'thursday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                    'friday' => ['open' => '15:00', 'close' => '00:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => false, // Takeaway only
            ],
            [
                'name' => [
                    'ar' => 'مطعم البحر الأبيض',
                    'en' => 'Mediterranean Restaurant',
                ],
                'description' => [
                    'ar' => 'مطعم فاخر يقدم أطباق البحر الأبيض المتوسط بلمسة عصرية',
                    'en' => 'Upscale restaurant serving Mediterranean cuisine with a modern twist',
                ],
                'slug' => 'mediterranean-restaurant',
                'active' => true,
                'address' => 'برج المملكة، الرياض، المملكة العربية السعودية',
                'phone' => '+966 11 901 2345',
                'email' => 'reservations@mediterranean.sa',
                'website' => 'https://mediterranean-riyadh.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '18:00', 'close' => '01:00', 'closed' => false],
                    'sunday' => ['open' => '18:00', 'close' => '01:00', 'closed' => false],
                    'monday' => ['closed' => true], // Closed on Monday
                    'tuesday' => ['open' => '18:00', 'close' => '01:00', 'closed' => false],
                    'wednesday' => ['open' => '18:00', 'close' => '01:00', 'closed' => false],
                    'thursday' => ['open' => '18:00', 'close' => '01:00', 'closed' => false],
                    'friday' => ['open' => '18:00', 'close' => '02:00', 'closed' => false],
                ],
                'delivery_available' => false, // Fine dining, no delivery
                'pickup_available' => false,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'دجاج كنتاكي',
                    'en' => 'Kentucky Fried Chicken',
                ],
                'description' => [
                    'ar' => 'سلسلة مطاعم الدجاج المقلي الشهيرة عالمياً بوصفة الأعشاب والتوابل السرية',
                    'en' => 'World-famous fried chicken chain with secret herbs and spices recipe',
                ],
                'slug' => 'kfc-branch',
                'active' => true,
                'address' => 'مجمع الأندلس، الدمام، المملكة العربية السعودية',
                'phone' => '+966 13 012 3456',
                'email' => 'customer.service@kfc.sa',
                'website' => 'https://kfc.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'sunday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'monday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'tuesday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'wednesday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'thursday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                    'friday' => ['open' => '10:00', 'close' => '02:00', 'closed' => false],
                ],
                'delivery_available' => true,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
            [
                'name' => [
                    'ar' => 'مقهى الكتاب',
                    'en' => 'Book Cafe',
                ],
                'description' => [
                    'ar' => 'مقهى هادئ يجمع بين حب القراءة وتذوق القهوة المختصة',
                    'en' => 'Quiet cafe combining love for reading with specialty coffee tasting',
                ],
                'slug' => 'book-cafe',
                'active' => true,
                'address' => 'شارع العروبة، الخبر، المملكة العربية السعودية',
                'phone' => '+966 13 123 4567',
                'email' => 'info@bookcafe.sa',
                'website' => 'https://bookcafe.sa',
                'opening_hours' => [
                    'saturday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'sunday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'monday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'tuesday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'wednesday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'thursday' => ['open' => '07:00', 'close' => '22:00', 'closed' => false],
                    'friday' => ['open' => '08:00', 'close' => '23:00', 'closed' => false],
                ],
                'delivery_available' => false,
                'pickup_available' => true,
                'dine_in_available' => true,
            ],
        ];

        foreach ($restaurants as $restaurantData) {
            Restaurant::create($restaurantData);
        }

        $this->command->info('Restaurant seeder completed successfully!');
    }
}
