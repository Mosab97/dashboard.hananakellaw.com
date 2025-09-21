<?php

namespace App\Http\Controllers\API\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CategoryResource;
use App\Http\Resources\API\ProductResource;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\API\RestaurantResource;
use App\Http\Resources\API\SucessStoryResource;
use App\Http\Resources\API\SliderResource;
use App\Http\Resources\API\VideoResource;
use App\Models\Category;
use App\Models\Video;
use App\Models\SucessStory;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Slider;
use App\Services\API\Home\ConstantService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $filterService;

    private $_model;

    public function __construct(
        private ConstantService $constantService,
    ) {}

    public function getConstants(Request $request)
    {
        $params = array_filter([
            'module' => $request->get('module', false),
            'field' => $request->get('field', false),
            'constant_name' => $request->get('constant_name', false),
            'parent_id' => trim($request->get('parent_id', false)),
            'constant_id' => trim($request->get('constant_id', false)),
        ], fn($value) => $value !== false && $value !== '');

        $results = $this->constantService->getConstants($params);

        return apiSuccess($results);
    }

    public function home(Request $request)
    {
        $category_id = $request->get('category_id', null);
        $sliders = Slider::where('active', true)->get();
        $sucess_stories = SucessStory::where('active', true)->get();
        $videos = Video::where('active', true)->get();
        // $restaurants = Restaurant::where('active', true)->get();
        $categories = Category::where(['active' => true, 'restaurant_id' => getFirstRestaurant()->id])->get();
        $products = Product::where(['active' => true, 'restaurant_id' => getFirstRestaurant()->id])->when(isset($category_id), function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })->get();

        return apiSuccess([
            'sliders' => SliderResource::collection($sliders),
            'sucess_stories' => SucessStoryResource::collection($sucess_stories),
            'videos' => VideoResource::collection($videos),
            // 'restaurants' => RestaurantResource::collection($restaurants),
            'categories' => CategoryResource::collection($categories),
            'products' => ProductResource::collection($products),
            'site_settings' => $this->getSiteSettings(),

        ]);
    }


    /**
     * Get all public settings with current language content
     */
    public function getSiteSettings()
    {
        $locale = app()->getLocale();
        // $cacheKey = "public_settings_{$locale}";

        // return Cache::remember($cacheKey, 60 * 48, function () use ($locale) {
        return [
            'phone' => Setting::get('site_phone', null),
            'email' => Setting::get('site_email', null),
            'address' => Setting::get('site_address.' . $locale, null),
            // 'terms_conditions' => Setting::get('terms_conditions.' . $locale, null),
            // 'privacy_policy' => Setting::get('privacy_policy.' . $locale, null),
            'social_media' => [
                'facebook' => Setting::get('social_facebook'),
                'instagram' => Setting::get('social_instagram'),
                'twitter' => Setting::get('social_twitter'),
            ],
        ];
        // });
    }
}
