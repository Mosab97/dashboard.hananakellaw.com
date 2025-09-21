<?php

namespace App\Http\Controllers;

use App\Models\SystemSmsNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static $data = [];

    public function editButton($route, $className)
    {
        return '<a href="'.$route.'" class="btn btn-icon btn-active-light-primary w-30px h-30px '.$className.'">
                <span class="svg-icon svg-icon-3">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="currentColor"/>
                <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="currentColor"/>
                <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="currentColor"/>
                </svg>
                </span>
                </a>';
    }

    public function deleteButton($route, $className, $attribute)
    {
        return '<a '.$attribute.' href='.$route.' class="btn btn-icon btn-active-light-primary w-30px h-30px '.$className.'"
                    >
                    <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                    <span class="svg-icon svg-icon-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                fill="currentColor" />
                            <path opacity="0.5"
                                d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                fill="currentColor" />
                            <path opacity="0.5"
                                d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </a>';
    }

    public function confirmButton($route, $className, $attribute)
    {
        return '<a '.$attribute.' href='.$route.' class="btn btn-icon btn-active-light-primary w-30px h-30px '.$className.'"
                    >
                    <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                    <span class="svg-icon svg-icon-3">
                      <i class="la la-whatsapp"></i>
                    </span>
                    <!--end::Svg Icon-->
                </a>';
    }

    public function refineMobile($mobile, $code = 0)
    {
        $mobile = str_replace(' ', '', $mobile);
        $mobile = str_replace('-', '', $mobile);
        $code = '972';
        if ($code == 0) {
            return $mobile;

            return substr($mobile, -8);
        }

        if (strlen($mobile) == 9) {
            $mobile = $code.$mobile;
        } elseif (strlen($mobile) == 10) {
            $mobile = $code.substr($mobile, 1);
        } elseif (strlen($mobile) == 14) {
            $mobile = substr($mobile, 2);
        } elseif (strlen($mobile) >= 12) {
            $mobile = $mobile;
        } else {
            $mobile = 0;
        }
        /*
                if (strlen($mobile) < 12)
                    $mobile = 0;*/

        $mobile = str_replace(' ', '', $mobile);
        $mobile = str_replace('-', '', $mobile);

        return $mobile;
    }

    public function getSelect(Request $request, $lang = 0)
    {

        $type = $request->type;
        $res[0] = ['id' => 0, 'text' => ''];

        $module = $request->module;
        $category_id = $request->category_id;

        $class = 'App\\Models\\'.$module;
        $data = $class::find($category_id);

        if ($data) {
            return response(['status' => true, 'message' => 'done', 'data' => $data], 200);
        } else {
            return response(['status' => false, 'message' => 'error', 'data' => ''], 401);
        }
    }

    public function sendSuperSMS($gateWay, $mobile, $msg, $module = 0, $module_id = 0, $type = 0, $note = 0, $tryWhats = 0, $link = 0)
    {
        $sent = 0;

        if ($tryWhats) {

            $this->sendWhatsapp($mobile, $msg, 'graph', $tryWhats, $tryWhats, 0, 2, $link);
            $sent = 1;
        }
        if ($sent == 0) {
            $modelname = $module;
            $class = app('App\\Models\\'.$modelname);
            $sms = new SystemSmsNotification;
            $sms->gateWay = $gateWay;
            $sms->message = $msg;
            $sms->sender_type = $class->getMorphClass();
            $sms->mobile = $mobile;
            $sms->sms_count = strlen($msg) / 52;
            $sms->sender_id = $module_id;
            $sms->type_id = 1;
            $sms->save();
            smsapi()->gateway($gateWay)->sendMessage($mobile, $msg);
        }
    }
}
