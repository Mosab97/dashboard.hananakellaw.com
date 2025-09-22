<?php

namespace App\Http\Controllers\API\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ServiceResource;
use App\Http\Resources\API\SliderResource;
use App\Http\Resources\API\SucessStoryResource;

use App\Http\Resources\API\VideoResource;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Video;
use App\Models\SucessStory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {

        return apiSuccess([
            'sliders' => SliderResource::collection(Slider::where('active', true)->get()),
            'services' => ServiceResource::collection(Service::where('active', true)->get()),
            'videos' => VideoResource::collection(Video::where('active', true)->get()),
            'sucess_stories' => SucessStoryResource::collection(SucessStory::where('active', true)->get()),

        ]);
    }
}
