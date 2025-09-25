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
use App\Models\AppointmentType;
use App\Http\Resources\API\AppointmentTypeResource;
use App\Http\Requests\Api\BookAppointmentRequest;
use App\Http\Resources\API\BookAppointmentResource;
use App\Models\BookAppointment;
use Illuminate\Http\Request;
use App\Http\Resources\API\WhyChooseUsResource;
use App\Models\WhyChooseUs;
class HomeController extends Controller
{
    public function home(Request $request)
    {

        return apiSuccess([
            'sliders' => SliderResource::collection(Slider::where('active', true)->get()),
            'services' => ServiceResource::collection(Service::where('active', true)->get()),
            'videos' => VideoResource::collection(Video::where('active', true)->get()),
            'sucess_stories' => SucessStoryResource::collection(SucessStory::where('active', true)->get()),
            'appointment_types' => AppointmentTypeResource::collection(AppointmentType::where('active', true)->get()),
            'about_office' => [
                'title' => setting('about_office.title.' . app()->getLocale()),
                'description' => setting('about_office.description.' . app()->getLocale()),
                'features' => setting('about_office.features'  ),
                'image' => asset('storage/' . setting('about_office.image')),
                ],
                'why_choose_us' => WhyChooseUsResource::collection(WhyChooseUs::where('active', true)->get()),
        ]);
    }

    public function appointmentTypes(Request $request)
    {
        return apiSuccess(AppointmentTypeResource::collection(AppointmentType::where('active', true)->get()));
    }

    public function bookAppointment(BookAppointmentRequest $request)
    {

        return apiSuccess(new BookAppointmentResource(BookAppointment::create($request->validated())), api('Appointment booked successfully'));
    }
}
