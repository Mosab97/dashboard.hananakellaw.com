<?php

use App\Models\Constant;
use App\Models\Restaurant;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

/**
 * Base translation function that handles all translation groups
 *
 * @param  string  $group  Translation group (manager, web, api)
 * @param  string  $key  Translation key
 * @param  array  $placeholder  Replacement parameters
 * @param  string|null  $locale  Language code
 * @return string Translated text
 */
function translate_key($group, $key, $placeholder = [], $locale = null)
{
    if (is_null($locale)) {
        $locale = config('app.locale');
    }

    $key = trim($key);
    $word = $group.'.'.$key;

    // If in non-local environment, just return the translation if exists, or key if not
    if (! App::environment('local')) {
        return Lang::has($word) ? trans($word, $placeholder, $locale) : $key;
    }

    // Only proceed with dynamic translation handling in local environment
    if (Lang::has($word)) {
        return trans($word, $placeholder, $locale);
    }

    $messages = [
        $word => $key,
    ];

    app('translator')->addLines($messages, $locale);

    // Add new translations to files in local environment
    try {
        $langs = config('app.locales');
        foreach ($langs as $lang) {
            $translation_file = base_path().'/lang/'.$lang.'/'.$group.'.php';

            if (file_exists($translation_file)) {
                $contents = file_get_contents($translation_file);

                // Ensure the key is not already in the file
                if (strpos($contents, "'{$key}' =>") === false) {
                    $new_key = "  '{$key}' => '{$key}',\n];\n";
                    $contents = substr_replace($contents, $new_key, -3, 3);
                    file_put_contents($translation_file, $contents);
                }
            }
        }
    } catch (\Exception $e) {
        // Log error but don't crash the application
        Log::error('Translation file write error: '.$e->getMessage());
    }

    return trans($word, $placeholder, $locale);
}

/**
 * Custom translation helper for manager group
 */
function t($key, $placeholder = [], $locale = null)
{
    return translate_key('manager', $key, $placeholder, $locale);
}

/**
 * Custom translation helper for web group
 */
function w($key, $placeholder = [], $locale = null)
{
    return translate_key('web', $key, $placeholder, $locale);
}

/**
 * Custom translation helper for API group
 */
function api($key, $placeholder = [], $locale = null)
{
    return translate_key('api', $key, $placeholder, $locale);
}

function isRtl()
{
    return app()->getLocale() === 'ar';
}

function isRtlJS()
{
    return app()->getLocale() === 'ar' ? 'true' : 'false';
}

function direction()
{
    return isRtl() ? 'rtl' : 'ltr';
}

function MimeFile($extension)
{
    /*
     Video Type     Extension       MIME Type
    Flash           .flv            video/x-flv
    MPEG-4          .mp4            video/mp4
    iPhone Index    .m3u8           application/x-mpegURL
    iPhone Segment  .ts             video/MP2T
    3GP Mobile      .3gp            video/3gpp
    QuickTime       .mov            video/quicktime
    A/V Interleave  .avi            video/x-msvideo
    Windows Media   .wmv            video/x-ms-wmv
    */
    $ext_photos = ['png', 'jpg', 'jpeg', 'gif'];

    return in_array($extension, $ext_photos) ? 'photo' : 'video';
}

function splitAndUppercase($string)
{
    // Split the string by underscore
    $words = explode('_', $string);

    // Capitalize the first letter of each word
    $words = array_map('ucfirst', $words);

    // Join the words back together with a space
    $result = implode(' ', $words);

    return $result;
}
function split_string($string, $count = 2)
{

    // Using the explode method
    $arr_ph = explode(' ', $string, $count);

    if (! isset($arr_ph[1])) {
        $arr_ph[1] = '';
    }

    return $arr_ph;
}

function check_mobile($mobile)
{

    if (\Str::startsWith($mobile, '05')) {
        return '+966'.substr($mobile, 1, 9);
    }
    if (\Str::startsWith($mobile, '03')) {
        return '+966'.substr($mobile, 1, 9);
    }
    if (\Str::startsWith($mobile, '5')) {
        return '+966'.substr($mobile, 0, 9);
    }
    if (\Str::startsWith($mobile, '00966')) {
        return '+'.substr($mobile, 2, 13);
    }
    if (\Str::startsWith($mobile, '966')) {
        return '+'.$mobile;
    }

    return $mobile;

    //   $mobile = str_replace('05', '+9665', $mobile);

}

function assets($path = '', $relative = false)
{
    return $relative ? 'public/'.$path : url('public/'.$path);
}

function slug($string)
{
    return preg_replace('/\s+/u', '-', trim($string));
}

function generateRandomString($length = 20)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function generateInvoiceNumber($model)
{

    $year = date('Y');
    $expNum = 0;
    // get last record
    $record = $model::latest()->first();
    if ($record) {
        [$year, $expNum] = explode('-', $record->invoice_id);
    }

    // check first day in a year
    if (date('z') === '0') {
        $nextInvoiceNumber = date('Y').'-0001';
    } else {
        // increase 1 with last invoice number
        $nextInvoiceNumber = $year.'-'.((int) $expNum + 1);
    }

    return $nextInvoiceNumber;
    // now add into database $nextInvoiceNumber as a next number.
}

function work_hours()
{
    $days = [
        ['day' => 'Saturday', 'num' => 1, 'from' => '8', 'to' => '20'],
        ['day' => 'Sunday', 'num' => 2, 'from' => '8', 'to' => '20'],
        ['day' => 'Monday', 'num' => 3, 'from' => '8', 'to' => '20'],
        ['day' => 'Tuesday', 'num' => 4, 'from' => '8', 'to' => '20'],
        ['day' => 'Wednesday', 'num' => 5, 'from' => '8', 'to' => '20'],
        ['day' => 'Thursday', 'num' => 6, 'from' => '8', 'to' => '20'],
        ['day' => 'Friday', 'num' => 7, 'from' => '8', 'to' => '20'],
    ];

    return $days;
}

function e_days($index)
{
    $days = [
        '1' => 'Saturday',
        '2' => 'Sunday',
        '3' => 'Monday',
        '4' => 'Tuesday',
        '5' => 'Wednesday',
        '6' => 'Thursday',
        '7' => 'Friday',
    ];

    return $days[$index];
}

function ex_days($index)
{
    $days = [
        'Saturday' => '1',
        'Sunday' => '2',
        'Monday' => '3',
        'Tuesday' => '4',
        'Wednesday' => '5',
        'Thursday' => '6',
        'Friday' => '7',
    ];

    return $days[$index];
}

function days($index)
{
    $days = [
        '1' => 'Saturday',
        '2' => 'Sunday',
        '3' => 'Monday',
        '4' => 'Tuesday',
        '5' => 'Wednesday',
        '6' => 'Thursday',
        '7' => 'Friday',
    ];

    return t($days[$index]);
}

function defaultImage()
{
    return 'public/assets/img/default.png';
}

function status($status, $type = '')
{
    $color = [
        '0' => 'danger',
        '1' => 'success',
        'pending' => 'warning',
        'active' => 'success',
        'accepted' => 'success',
        'delayed' => 'default',
        'rejected' => 'danger',
        'cancelled' => 'default',
        'inactive' => 'danger',
        'waiting' => 'warning',
        'acceptable' => 'info',
        'unacceptable' => 'danger',
        'winners' => 'success',
        'done' => 'success',
        'pass' => 'info',
        'shipping' => 'warning',
        'new' => 'warning',
        'completed' => 'success',

    ];

    $text = [
        '0' => t('admin.Inactive'),
        '1' => t('admin.Active'),
        'pending' => t('admin.Pending'),
        'active' => t('admin.Active'),
        'accepted' => 'مقبول',
        'delayed' => 'مؤجل',
        'rejected' => 'مرفوض',
        'cancelled' => t('admin.Cancelled'),
        'inactive' => t('admin.inactive'),
        'waiting' => t('admin.waiting'),
        'acceptable' => 'مقبول',
        'unacceptable' => 'مرفوض',
        'winners' => 'فائز',
        'done' => t('admin.Done'),
        'pass' => 'تم تمريرها',
        'shipping' => t('admin.Shipping'),
        'new' => t('admin.New'),
        'completed' => t('admin.completed'),
    ];

    if ($type == 't') {
        return $text[$status];
    }

    if ($type == 'c') {
        return $color[$status];
    }

    return "<label class='label label-mini label-{$color[$status]}'>{$text[$status]}<label>";
}

function pic($src, $class = 'full')
{
    $html = "<img class='  ".$class."' src='".asset($src)."'>";

    return $html;
}

function ext($filename, $style = false)
{

    // $ext = File::extension($filename);

    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (! $style) {
        return $ext;
    }

    return $html = "<img class='' src='".asset('public/assets/img/ext/'.$ext.'.png')."'>";
}

function IsLang($lang = 'ar')
{
    return session('lang') == $lang;
}

function rating($val, $max = 5)
{
    $html = '';
    for ($i = 1; $i <= $max; $i++) {

        if ($i <= $val) {
            $html .= "<span><i class='fa fa-star fa-lg active'></i></span>";
        } else {
            $html .= "<span><i class='fa fa-star-o fa-lg '></i></span>";
        }
    }

    return $html;
}

function isAPI()
{
    return request()->is('api/*');
}

function versions()
{
    return ['v1'];
}

function base64ToFile($data)
{

    $file_name = 'attach_'.time().'.'.getExtBase64($data);
    $path = 'uploads/user_attachments/'.$file_name;
    $uploadPath = public_path($path);
    if (! file_put_contents($uploadPath, base64_decode($data)));
    $path = '';

    return $path;
}

function getExtBase64($data)
{

    $pos = strpos($data, ';');
    $mimi = explode(':', substr($data, 0, $pos))[1];

    return $ext = explode('/', $mimi)[1];
}

function paginate($object)
{
    return [
        'current_page' => $object->currentPage(),
        // 'items' => $object->items(),
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}

function paginate_message($object)
{

    $items = [];
    foreach ($object->items() as $key => $item) {
        foreach ($item['data'] as $k => $val) {
            $items[$key][$k] = $val;

            // $items[$key] = ['id' => $item->id,'title' => $item->data['title'],'body' => $item->data['body'],'created_at' => $item->created_at ];
            /* if(isset($item->data['title']))
              $items[$key]['title'] = $item->data['title']; */
        }
        $items[$key]['notification_id'] = $item->id;
        $items[$key]['created_at'] = $item->created_at->format('Y-m-d H:i:s');
    }

    return [
        'current_page' => $object->currentPage(),
        'items' => $items,
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}

function send_notification($topic, $data = null, $notification = null)
{
    $message = CloudMessage::withTarget('topic', $topic)
        ->withNotification($notification) // optional
        ->withData($data); // optional
    $message = CloudMessage::fromArray([
        'topic' => $topic,
        'notification' => [/* Notification data as array */], // optional
        'data' => [/* data array */], // optional
    ]);
    $message->send($message);
}

function getOnly($only, $array)
{
    $data = [];
    foreach ($only as $id) {
        if (isset($array[$id])) {
            $data[$id] = $array[$id];
        }
    }

    return $data;
}

function status_text($status)
{

    $title = ['pending' => 'المعلقة', 'accepted' => 'المقبولة', 'cancelled' => 'الملغية', 'rejected' => 'المرفوضة'];

    return $title[$status];
}

function cached($index = 'settings', $col = false)
{

    // Cache::forget('cities');
    $cache['settings'] = Cache::remember('settings', 60 * 48, function () {
        return \App\Models\Setting::first();
    });

    if (! isset($cache[$index])) {
        return $index;
    }
    if (! $col) {
        return $cache[$index];
    }

    return $cache[$index]->{$col};
}

function destroyFile($file)
{

    if (! empty($file) and File::exists(public_path($file))) {
        File::delete(public_path($file));
    }
}

function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $html = curl_exec($ch);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function arabic_date($datetime)
{

    $months = ['Jan' => 'يناير', 'Feb' => 'فبراير', 'Mar' => 'مارس', 'Apr' => 'أبريل', 'May' => 'مايو', 'Jun' => 'يونيو', 'Jul' => 'يوليو', 'Aug' => 'أغسطس', 'Sep' => 'سبتمبر', 'Oct' => 'أكتوبر', 'Nov' => 'نوفمبر', 'Dec' => 'ديسمبر'];
    $days = ['Sat' => 'السبت', 'Sun' => 'الأحد', 'Mon' => 'الإثنين', 'Tue' => 'الثلاثاء', 'Wed' => 'الأربعاء', 'Thu' => 'الخميس', 'Fri' => 'الجمعة'];
    $am_pm = ['AM' => 'ص', 'PM' => 'م'];

    $_month = $months[date('M', strtotime($datetime))];
    $_day_month = date('d', strtotime($datetime));
    $_year = date('Y', strtotime($datetime));
    //    dd($_day_month);
    $_day = $days[date('D', strtotime($datetime))];
    $_time = date('h:i', strtotime($datetime));
    $_am_pm = $am_pm[date('A', strtotime($datetime))];

    return $_day.' '.$_day_month.' '.$_month.' '.$_year.' , '.$_time;

    return $_am_pm.' '.\Carbon\Carbon::parse($datetime)->format('h:i  - d/m/Y');
}

function english_date($datetime)
{
    $_month = date('M', strtotime($datetime));
    $_day_month = date('d', strtotime($datetime));
    $_year = date('Y', strtotime($datetime));
    $_day = date('D', strtotime($datetime));
    $_time = date('h:i', strtotime($datetime));

    return $_day.' '.$_day_month.' '.$_month.' '.$_year.' , '.$_time;
}

function numhash($n)
{
    return ((0x0000FFFF & $n) << 16) + ((0xFFFF0000 & $n) >> 16);
}

function convertToUnicode($message)
{
    $chrArray[0] = '¡';
    $unicodeArray[0] = '060C';
    $chrArray[1] = 'º';
    $unicodeArray[1] = '061B';
    $chrArray[2] = '¿';
    $unicodeArray[2] = '061F';
    $chrArray[3] = 'Á';
    $unicodeArray[3] = '0621';
    $chrArray[4] = 'Â';
    $unicodeArray[4] = '0622';
    $chrArray[5] = 'Ã';
    $unicodeArray[5] = '0623';
    $chrArray[6] = 'Ä';
    $unicodeArray[6] = '0624';
    $chrArray[7] = 'Å';
    $unicodeArray[7] = '0625';
    $chrArray[8] = 'Æ';
    $unicodeArray[8] = '0626';
    $chrArray[9] = 'Ç';
    $unicodeArray[9] = '0627';
    $chrArray[10] = 'È';
    $unicodeArray[10] = '0628';
    $chrArray[11] = 'É';
    $unicodeArray[11] = '0629';
    $chrArray[12] = 'Ê';
    $unicodeArray[12] = '062A';
    $chrArray[13] = 'Ë';
    $unicodeArray[13] = '062B';
    $chrArray[14] = 'Ì';
    $unicodeArray[14] = '062C';
    $chrArray[15] = 'Í';
    $unicodeArray[15] = '062D';
    $chrArray[16] = 'Î';
    $unicodeArray[16] = '062E';
    $chrArray[17] = 'Ï';
    $unicodeArray[17] = '062F';
    $chrArray[18] = 'Ð';
    $unicodeArray[18] = '0630';
    $chrArray[19] = 'Ñ';
    $unicodeArray[19] = '0631';
    $chrArray[20] = 'Ò';
    $unicodeArray[20] = '0632';
    $chrArray[21] = 'Ó';
    $unicodeArray[21] = '0633';
    $chrArray[22] = 'Ô';
    $unicodeArray[22] = '0634';
    $chrArray[23] = 'Õ';
    $unicodeArray[23] = '0635';
    $chrArray[24] = 'Ö';
    $unicodeArray[24] = '0636';
    $chrArray[25] = 'Ø';
    $unicodeArray[25] = '0637';
    $chrArray[26] = 'Ù';
    $unicodeArray[26] = '0638';
    $chrArray[27] = 'Ú';
    $unicodeArray[27] = '0639';
    $chrArray[28] = 'Û';
    $unicodeArray[28] = '063A';
    $chrArray[29] = 'Ý';
    $unicodeArray[29] = '0641';
    $chrArray[30] = 'Þ';
    $unicodeArray[30] = '0642';
    $chrArray[31] = 'ß';
    $unicodeArray[31] = '0643';
    $chrArray[32] = 'á';
    $unicodeArray[32] = '0644';
    $chrArray[33] = 'ã';
    $unicodeArray[33] = '0645';
    $chrArray[34] = 'ä';
    $unicodeArray[34] = '0646';
    $chrArray[35] = 'å';
    $unicodeArray[35] = '0647';
    $chrArray[36] = 'æ';
    $unicodeArray[36] = '0648';
    $chrArray[37] = 'ì';
    $unicodeArray[37] = '0649';
    $chrArray[38] = 'í';
    $unicodeArray[38] = '064A';
    $chrArray[39] = 'Ü';
    $unicodeArray[39] = '0640';
    $chrArray[40] = 'ð';
    $unicodeArray[40] = '064B';
    $chrArray[41] = 'ñ';
    $unicodeArray[41] = '064C';
    $chrArray[42] = 'ò';
    $unicodeArray[42] = '064D';
    $chrArray[43] = 'ó';
    $unicodeArray[43] = '064E';
    $chrArray[44] = 'õ';
    $unicodeArray[44] = '064F';
    $chrArray[45] = 'ö';
    $unicodeArray[45] = '0650';
    $chrArray[46] = 'ø';
    $unicodeArray[46] = '0651';
    $chrArray[47] = 'ú';
    $unicodeArray[47] = '0652';
    $chrArray[48] = '!';
    $unicodeArray[48] = '0021';
    $chrArray[49] = '"';
    $unicodeArray[49] = '0022';
    $chrArray[50] = '#';
    $unicodeArray[50] = '0023';
    $chrArray[51] = '$';
    $unicodeArray[51] = '0024';
    $chrArray[52] = '%';
    $unicodeArray[52] = '0025';
    $chrArray[53] = '&';
    $unicodeArray[53] = '0026';
    $chrArray[54] = "'";
    $unicodeArray[54] = '0027';
    $chrArray[55] = '(';
    $unicodeArray[55] = '0028';
    $chrArray[56] = ')';
    $unicodeArray[56] = '0029';
    $chrArray[57] = '*';
    $unicodeArray[57] = '002A';
    $chrArray[58] = '+';
    $unicodeArray[58] = '002B';
    $chrArray[59] = ',';
    $unicodeArray[59] = '002C';
    $chrArray[60] = '-';
    $unicodeArray[60] = '002D';
    $chrArray[61] = '.';
    $unicodeArray[61] = '002E';
    $chrArray[62] = '/';
    $unicodeArray[62] = '002F';
    $chrArray[63] = '0';
    $unicodeArray[63] = '0030';
    $chrArray[64] = '1';
    $unicodeArray[64] = '0031';
    $chrArray[65] = '2';
    $unicodeArray[65] = '0032';
    $chrArray[66] = '3';
    $unicodeArray[66] = '0033';
    $chrArray[67] = '4';
    $unicodeArray[67] = '0034';
    $chrArray[68] = '5';
    $unicodeArray[68] = '0035';
    $chrArray[69] = '6';
    $unicodeArray[69] = '0036';
    $chrArray[70] = '7';
    $unicodeArray[70] = '0037';
    $chrArray[71] = '8';
    $unicodeArray[71] = '0038';
    $chrArray[72] = '9';
    $unicodeArray[72] = '0039';
    $chrArray[73] = ':';
    $unicodeArray[73] = '003A';
    $chrArray[74] = ';';
    $unicodeArray[74] = '003B';
    $chrArray[75] = '<';
    $unicodeArray[75] = '003C';
    $chrArray[76] = '=';
    $unicodeArray[76] = '003D';
    $chrArray[77] = '>';
    $unicodeArray[77] = '003E';
    $chrArray[78] = '?';
    $unicodeArray[78] = '003F';
    $chrArray[79] = '@';
    $unicodeArray[79] = '0040';
    $chrArray[80] = 'A';
    $unicodeArray[80] = '0041';
    $chrArray[81] = 'B';
    $unicodeArray[81] = '0042';
    $chrArray[82] = 'C';
    $unicodeArray[82] = '0043';
    $chrArray[83] = 'D';
    $unicodeArray[83] = '0044';
    $chrArray[84] = 'E';
    $unicodeArray[84] = '0045';
    $chrArray[85] = 'F';
    $unicodeArray[85] = '0046';
    $chrArray[86] = 'G';
    $unicodeArray[86] = '0047';
    $chrArray[87] = 'H';
    $unicodeArray[87] = '0048';
    $chrArray[88] = 'I';
    $unicodeArray[88] = '0049';
    $chrArray[89] = 'J';
    $unicodeArray[89] = '004A';
    $chrArray[90] = 'K';
    $unicodeArray[90] = '004B';
    $chrArray[91] = 'L';
    $unicodeArray[91] = '004C';
    $chrArray[92] = 'M';
    $unicodeArray[92] = '004D';
    $chrArray[93] = 'N';
    $unicodeArray[93] = '004E';
    $chrArray[94] = 'O';
    $unicodeArray[94] = '004F';
    $chrArray[95] = 'P';
    $unicodeArray[95] = '0050';
    $chrArray[96] = 'Q';
    $unicodeArray[96] = '0051';
    $chrArray[97] = 'R';
    $unicodeArray[97] = '0052';
    $chrArray[98] = 'S';
    $unicodeArray[98] = '0053';
    $chrArray[99] = 'T';
    $unicodeArray[99] = '0054';
    $chrArray[100] = 'U';
    $unicodeArray[100] = '0055';
    $chrArray[101] = 'V';
    $unicodeArray[101] = '0056';
    $chrArray[102] = 'W';
    $unicodeArray[102] = '0057';
    $chrArray[103] = 'X';
    $unicodeArray[103] = '0058';
    $chrArray[104] = 'Y';
    $unicodeArray[104] = '0059';
    $chrArray[105] = 'Z';
    $unicodeArray[105] = '005A';
    $chrArray[106] = '[';
    $unicodeArray[106] = '005B';
    $char = "\ ";
    $chrArray[107] = trim($char);
    $unicodeArray[107] = '005C';
    $chrArray[108] = ']';
    $unicodeArray[108] = '005D';
    $chrArray[109] = '^';
    $unicodeArray[109] = '005E';
    $chrArray[110] = '_';
    $unicodeArray[110] = '005F';
    $chrArray[111] = '`';
    $unicodeArray[111] = '0060';
    $chrArray[112] = 'a';
    $unicodeArray[112] = '0061';
    $chrArray[113] = 'b';
    $unicodeArray[113] = '0062';
    $chrArray[114] = 'c';
    $unicodeArray[114] = '0063';
    $chrArray[115] = 'd';
    $unicodeArray[115] = '0064';
    $chrArray[116] = 'e';
    $unicodeArray[116] = '0065';
    $chrArray[117] = 'f';
    $unicodeArray[117] = '0066';
    $chrArray[118] = 'g';
    $unicodeArray[118] = '0067';
    $chrArray[119] = 'h';
    $unicodeArray[119] = '0068';
    $chrArray[120] = 'i';
    $unicodeArray[120] = '0069';
    $chrArray[121] = 'j';
    $unicodeArray[121] = '006A';
    $chrArray[122] = 'k';
    $unicodeArray[122] = '006B';
    $chrArray[123] = 'l';
    $unicodeArray[123] = '006C';
    $chrArray[124] = 'm';
    $unicodeArray[124] = '006D';
    $chrArray[125] = 'n';
    $unicodeArray[125] = '006E';
    $chrArray[126] = 'o';
    $unicodeArray[126] = '006F';
    $chrArray[127] = 'p';
    $unicodeArray[127] = '0070';
    $chrArray[128] = 'q';
    $unicodeArray[128] = '0071';
    $chrArray[129] = 'r';
    $unicodeArray[129] = '0072';
    $chrArray[130] = 's';
    $unicodeArray[130] = '0073';
    $chrArray[131] = 't';
    $unicodeArray[131] = '0074';
    $chrArray[132] = 'u';
    $unicodeArray[132] = '0075';
    $chrArray[133] = 'v';
    $unicodeArray[133] = '0076';
    $chrArray[134] = 'w';
    $unicodeArray[134] = '0077';
    $chrArray[135] = 'x';
    $unicodeArray[135] = '0078';
    $chrArray[136] = 'y';
    $unicodeArray[136] = '0079';
    $chrArray[137] = 'z';
    $unicodeArray[137] = '007A';
    $chrArray[138] = '{';
    $unicodeArray[138] = '007B';
    $chrArray[139] = '|';
    $unicodeArray[139] = '007C';
    $chrArray[140] = '}';
    $unicodeArray[140] = '007D';
    $chrArray[141] = '~';
    $unicodeArray[141] = '007E';
    $chrArray[142] = '©';
    $unicodeArray[142] = '00A9';
    $chrArray[143] = '®';
    $unicodeArray[143] = '00AE';
    $chrArray[144] = '÷';
    $unicodeArray[144] = '00F7';
    $chrArray[145] = '×';
    $unicodeArray[145] = '00F7';
    $chrArray[146] = '§';
    $unicodeArray[146] = '00A7';
    $chrArray[147] = ' ';
    $unicodeArray[147] = '0020';
    $chrArray[148] = "\n";
    $unicodeArray[148] = '000D';
    $chrArray[149] = "\r";
    $unicodeArray[149] = '000A';

    $strResult = '';
    for ($i = 0; $i < strlen($message); $i++) {
        if (in_array(substr($message, $i, 1), $chrArray)) {
            $strResult .= $unicodeArray[array_search(substr($message, $i, 1), $chrArray)];
        }
    }

    return $strResult;
}

/***
 * new things
 */

if (! function_exists('getRandomPhoneNumber_8_digit')) {

    function getRandomPhoneNumber_8_digit()
    {
        return rand(100000000, 999999999);
    }
}

if (! function_exists('apiSuccess')) {
    function apiSuccess($data = null, $message = 'success', $status = 200)
    {
        $res = [
            'success' => true,
            'data' => $data,
            'status' => $status,
            'message' => is_null($message) ? 'Success' : $message,
        ];

        // Log the response data and message
        Log::info('API Success Response', ['response' => $res]);

        return response()->json($res, $status)->header('Content-Type', 'application/json');
    }
}

if (! function_exists('apiError')) {
    function apiError($message = 'error', $status = 422, $data = null, $errors = null)
    {
        $res = [
            'success' => false,
            'data' => $data, // Additional context data when available
            'status' => $status,
            'message' => $message,
        ];

        // Validation errors when available
        if ($errors !== null) {
            $res['errors'] = $errors;
        }

        // Log the error response
        Log::error('API Error Response', ['response' => $res]);

        return response()->json($res, $status)
            ->header('Content-Type', 'application/json');
    }
}

if (! function_exists('paginateResult')) {
    function paginateResult($query, $perPage = 10)
    {
        // Get paginated results
        $items = $query->paginate($perPage);

        // Extract pagination metadata
        $pagination = collect($items)->except('data')->toArray();

        return [
            'data' => $items->items(),
            'meta' => $pagination,
        ];
    }
}

if (! function_exists('generateCode')) {
    function generateCode($min = 0, $max = 9, $quantity = 4)
    {
        $numbers = range($min, $max);
        shuffle($numbers);

        return implode(array_slice($numbers, 0, $quantity));
    }
}

if (! function_exists('apiTrans')) {
    function apiTrans($error, $transParams = [])
    {
        return trans('api.'.$error, $transParams);
    }
}

if (! function_exists('getFirstError')) {
    function getFirstError($request, $validations, $messages = null)
    {
        $response = customeValidation($request, $validations, $messages);
        if ($response[IS_ERROR] == true) {
            $response[ERROR] = $response[ERRORS][0];

            return $response;
        }

        return $response;
    }
}

if (! function_exists('customeValidation')) {
    function customeValidation($request, $validations, $mssages = null)
    {
        $validator = Validator::make($request->all(), $validations, ($mssages) ? $mssages : []);
        if ($validator->fails()) {
            $err = [];
            foreach ($validator->errors()->toArray() as $index => $error) {
                foreach ($error as $index2 => $sub_error) {
                    array_push($err, $sub_error);
                }
            }

            return [
                IS_ERROR => true,
                ERRORS => $err,
            ];
        }

        return [
            IS_ERROR => false,
            ERRORS => [],
        ];
    }
}

function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $unit = 'K')
{
    // Google API key
    $apiKey = 'AIzaSyAU4Zxi-MPG9HSJJUX6bJCC0XPVgWKh1vs';

    // Calculate distance between latitude and longitude
    $theta = $longitudeFrom - $longitudeTo;
    $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    // Convert unit and return distance
    $unit = strtoupper($unit);
    if ($unit == 'K') {
        return round($miles * 1.609344, 2);
    }
}

if (! function_exists('apiUser')) {
    function apiUser()
    {
        return auth('api')->user();
    }
}

if (! function_exists('saveLog')) {
    function saveLog($message, $mobileNumber, $response_number)
    {
        $statusList = [
            403 => 'لا يوجد رصيد كاف في حسابك',
            500 => 'مزود خدمة الرسائل النصية لا يعمل',
            503 => 'لا يوجد رصيد كاف عند مزود الخدمة',
        ];
        $log = [];
        $log['message'] = $message;
        $log['type'] = 'sms';
        $log['fail_reason'] = '';
        $log['to_number'] = $mobileNumber;
        $log['send_date'] = date('Y-m-d H:i:s');
        $log['status'] = 'ok';
        $log['fail_reason'] = $response_number;
        $log['created_at'] = date('Y-m-d H:i:s');
        Log::info(json_encode($log));

        return true;
    }
}
if (! function_exists('send_sms_message')) {

    function send_sms_message($number, $message)
    {
        $number = str_replace('+', '', $number);

        if (empty($number) || empty($message)) {
            throw new \InvalidArgumentException('Number and message cannot be empty');
        }

        $link = "https://sms.htd.ps/API/SendSMS.aspx?id=5fb01ed94eab671a2888899f7025a17c&sender=Insurey&to={$number}&msg={$message}";
        $client = new Client;

        $response = $client->request('GET', $link);
        $responseBody = $response->getBody()->getContents();

        // Save the log details
        Log::info('SMS sent', ['message' => $message, 'number' => $number, 'responseBody' => $responseBody]);

        return ['status' => $response->getStatusCode(), 'body' => $responseBody];

        return $response;

        $res = [];
        $number = str_replace('+', '', $number);
        if (! empty($number) && ! empty($message)) {
            $curl = curl_init();
            // $username = "966502809029";
            // $password = "966502809029";
            // $sender = "Active-code";
            // $link = "https://www.hisms.ws/api.php?send_sms&username=" . $username . "&password=" . $password . "&numbers=" . $number . "&sender=" . $sender . "&message=" . $message;
            $link = 'https://sms.htd.ps/API/SendSMS.aspx?id=5fb01ed94eab671a2888899f7025a17c&sender=Insurey&to=972594409696&msg=MessageHere';
            $client = new Client;
            $res = $client->request('GET', $link, []);
            $log = saveLog($message, $number, $res);
            //            Log::info(json_encode($res));
        }

        return $res;
    }
}

if (! function_exists('logoutAllAuthUsers')) {
    function logoutAllAuthUsers()
    {
        foreach (\App\Models\User::get() as $index => $item) {
            $item->tokens()->delete();
            $item->update([
                'fcm_token' => null,
            ]);
        }

        return apiTrans('All_logged_out_successfully');
    }
}

if (! function_exists('logoutApiUser')) {
    function logoutApiUser()
    {
        $user = apiUser();
        if (! isset($user)) {
            return apiTrans('no_user_to_logged_out');
        }
        $user->tokens()->delete();
        $user->update([
            'fcm_token' => null,
        ]);

        return apiTrans('logged_out_successfully');
    }
}

if (! function_exists('getDayNumber')) {
    function getDayNumber($day = null)
    {

        $weekMap = [
            0 => 'SU',
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
        ];
        $dataWeekMap = [
            'SA' => 1,
            'SU' => 2,
            'MO' => 3,
            'TU' => 4,
            'WE' => 5,
            'TH' => 6,
            'FR' => 7,
        ];
        $dayOfTheWeek = (isset($day)) ? $day : Carbon::now()->dayOfWeek;
        $weekday = $weekMap[$dayOfTheWeek];

        return $dataWeekMap[$weekday];
    }
}

if (! function_exists('getDayHours')) {
    function getDayHours()
    {
        return [
            '08:00' => 8,
            '09:00' => 9,
            '10:00' => 10,
            '11:00' => 11,
            '12:00' => 12,
            '13:00' => 13,
            '14:00' => 14,
            '15:00' => 15,
            '16:00' => 16,
            '17:00' => 17,
            '18:00' => 18,
            '19:00' => 19,
            '20:00' => 20,
            '21:00' => 21,
            '22:00' => 22,
            '23:00' => 23,
            '24:00' => 24,
            '01:00' => 1,
            '02:00' => 2,
        ];
    }
}

if (! function_exists('getNewEncodedArray')) {
    function getNewEncodedArray($newObj, $status_time_line_encoded)
    {
        $arr = json_decode($status_time_line_encoded);
        if (in_array($newObj->key, collect($arr)->pluck('key')->toArray())) {
            return json_encode($arr);
        } else {
            $arr[] = $newObj;
        }

        return json_encode($arr);
    }
}

function arabicDate($dat)
{
    switch ($dat) {
        case 'Saturday':
            return 'السبت';
        case 'Sunday':
            return 'الأحد';
        case 'Monday':
            return 'الاثنين';
        case 'Tuesday':
            return 'الثلاثاء';
        case 'Wednesday':
            return 'الاربعاء';
        case 'Thursday':
            return 'الخميس';
        case 'Friday':
            return 'الجمعة';
    }
}

function notification_trans($key, $placeholder = [], $locale = null)
{
    // Log::info('Starting translation process', [
    //     'key' => $key,
    //     'placeholder' => $placeholder,
    //     'locale' => $locale
    // ]);

    $group = 'notifications';
    $locale = $locale ?: config('app.locale');
    $key = trim($key);
    $word = $group.'.'.$key;

    // Log::info('Generated translation word', [
    //     'word' => $word,
    //     'locale' => $locale
    // ]);

    if (Lang::has($word, $locale)) {
        // Log::info('Translation key exists', ['word' => $word, 'locale' => $locale, 'trans' => trans($word, $placeholder, $locale)]);
        return trans($word, $placeholder, $locale);
    }

    // Log::info('Translation key does not exist, adding new key', ['word' => $word]);

    $messages = [$word => $key];
    app('translator')->addLines($messages, $locale);

    $langs = config('app.locales');
    foreach ($langs as $lang) {
        $translation_file = base_path()."/lang/{$lang}/{$group}.php";

        // Log::info('Processing translation file', [
        //     'file' => $translation_file,
        //     'language' => $lang
        // ]);

        if (is_writable($translation_file)) {
            $fh = fopen($translation_file, 'r+');
            if ($fh) {
                $new_key = "  \n  '{$key}' => '{$key}',\n];\n";
                fseek($fh, -4, SEEK_END);
                fwrite($fh, $new_key);
                fclose($fh);
                // Log::info('Added new translation key to file', [
                //     'key' => $key,
                //     'file' => $translation_file
                // ]);
            } else {
                Log::error('Failed to open translation file', ['file' => $translation_file]);
            }
        } else {
            Log::error('Translation file is not writable', ['file' => $translation_file]);
        }
    }

    return trans($word, $placeholder, $locale);
}

if (! function_exists('password_rules')) {
    function password_rules($required = false, $min = '8', $confirmed = false)
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
            'min:'.$min,
        ];

        return $confirmed ? array_merge($rules, ['confirmed']) : $rules;
    }
}

if (! function_exists('user')) {
    function user($guard = 'manager')
    {
        return auth()->guard($guard)->user();
    }
}

if (! function_exists('getConstant')) {
    function getConstant($module, $field, $constant_name = null, $parent = null)
    {
        $constants = Constant::where([
            'module' => $module,
            'field' => $field,
        ])
            ->when($constant_name, function ($query) use ($constant_name) {
                $query->where('constant_name', $constant_name);
            })
            ->when($parent, function ($query) use ($parent) {
                $query->whereHas('parent', function ($q) use ($parent) {
                    $q->where('constant_name', $parent);
                });
            });
        if ($parent == null) {
            $constants->whereNull('parent_id');
        }

        return $constants->first();
    }
}
if (! function_exists('getConstants')) {
    function getConstants($module, $field, $parent = null, $constant_name = null)
    {
        $constants = Constant::where([
            'module' => $module,
            'field' => $field,
        ])
            ->when($constant_name, function ($query) use ($constant_name) {
                $query->where('constant_name', $constant_name);
            })
            ->when($parent, function ($query) use ($parent) {
                $query->whereHas('parent', function ($q) use ($parent) {
                    $q->where('constant_name', $parent);
                });
            });
        if ($parent == null) {
            $constants->whereNull('parent_id');
        }

        return $constants->get();
    }
}

if (! function_exists('generateRandomParagraph')) {
    function generateRandomParagraph($numSentences = 7)
    {
        $sentences = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Pellentesque vitae velit ex.',
            'Mauris dapibus risus quis suscipit vulputate.',
            'Eros diam egestas libero eu vulputate risus egestas.',
            'Aliquam id aliquam nisi, non interdum urna.',
            'Cras sit amet mi non ante fermentum suscipit.',
            'Etiam vel magna viverra, condimentum ex in, ullamcorper sem.',
            'Sed euismod nunc a ipsum sagittis facilisis.',
            'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.',
            'Nulla facilisi.',
            'Ut fringilla placerat turpis, vel tristique nisi posuere id.',
            'Praesent ullamcorper justo eget nisl volutpat, a fermentum risus fermentum.',
            'Integer pharetra nisl vel massa ultrices, at tempor nulla fermentum.',
            'Nunc bibendum ex a nisi egestas rhoncus.',
            'Vivamus a varius lorem.',
        ];

        shuffle($sentences);
        $selectedSentences = array_slice($sentences, 0, $numSentences);

        return implode(' ', $selectedSentences);
    }
}
if (! function_exists('filterArrayForNullValues')) {
    function filterArrayForNullValues($values): array
    {
        Log::info('Starting filterArrayForNullValues function.', ['inputValues' => $values]);

        // Step 1: Check if the input is null
        if (is_null($values)) {
            Log::info('Input values are null, returning an empty array.');

            return [];
        }

        // Step 2: Convert to an array if the input is a string
        if (! is_array($values)) {
            Log::info('Input is not an array. Converting input string to array.');
            $values = explode(',', $values);
            Log::info('Converted string to array:', ['values' => $values]);
        }

        // Step 3: Filter out null or empty values
        $filteredArray = array_filter($values, function ($value) {
            return ! is_null($value) && $value !== '';
        });
        Log::info('Filtered array after removing null and empty values:', ['filteredArray' => $filteredArray]);

        // Step 4: Remove duplicate values
        $uniqueFilteredArray = array_unique($filteredArray);
        Log::info('Filtered array after removing duplicates:', ['uniqueFilteredArray' => $uniqueFilteredArray]);

        // Step 5: Return the final filtered and unique array
        return $uniqueFilteredArray;
    }
}
if (! function_exists('logQuery')) {
    function logQuery($query, $label = '')
    {
        // Get SQL and bindings
        $sql = str_replace('?', "'%s'", $query->toSql());
        $bindings = collect($query->getBindings())
            ->map(function ($binding) {
                if (is_numeric($binding)) {
                    return $binding;
                }
                if (is_null($binding)) {
                    return 'NULL';
                }

                return str_replace("'", "\'", $binding);
            })->toArray();

        $finalQuery = vsprintf($sql, $bindings);

        // Format query for PHPMyAdmin with label
        $queryLabel = $label ? "/* $label */" : '/* Query logged at '.now().' */';

        $phpMyAdminQuery = "-- Copy from here\n"
            ."SELECT $queryLabel * FROM (\n"
            .$finalQuery
            ."\n) AS test_query;\n\n"
            ."-- Or use EXPLAIN to analyze the query:\n"
            .'EXPLAIN '.$finalQuery.";\n"
            .'-- End query';

        Log::info('PHPMyAdmin-ready SQL query:', [
            'label' => $label,
            'raw_query' => $finalQuery,
            'phpmyadmin_format' => $phpMyAdminQuery,
            'execution_time' => number_format((microtime(true) - LARAVEL_START) * 1000, 2).'ms',
        ]);

        return $finalQuery;
    }
}

if (! function_exists('lang')) {
    function lang()
    {
        // Get the current application locale
        $locale = app()->getLocale();

        // Log the locale being used
        // Log::info('Fetching current locale:', ['locale' => $locale]);

        // Return the locale
        return $locale;
    }
}

if (! function_exists('calculateAge')) {
    function calculateAge($dateOfBirth)
    {
        Log::info('Calculating age for date of birth: '.$dateOfBirth);
        $age = \Carbon\Carbon::parse($dateOfBirth)->age;
        Log::info('Calculated age: '.$age);

        return $age;
    }
}

if (! function_exists('getContrastColor')) {
    function getContrastColor($hexcolor)
    {
        // If the color is not in hex format or is empty, return a default color
        if (! $hexcolor || strlen($hexcolor) != 7) {
            return '#000000';  // Default to black text
        }

        // Remove the # if it's there
        $hexcolor = ltrim($hexcolor, '#');

        // Convert hex to RGB
        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));

        // Calculate the brightness
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        // If the color is light, return dark text, otherwise return light text
        return ($brightness > 155) ? '#000000' : '#FFFFFF';
    }
}

if (! function_exists('getAttachmentsMenuItem')) {
    function getAttachmentsMenuItem($model)
    {
        $route = route('offers.view_attachments', ['offer' => $model->id]).'?type=attachments';
        $title = "Show Attachments ({$model->attachments_count})";

        return "
            <div class='menu-item px-3'>
                <a href='{$route}' title='attachments' class='menu-link px-3 viewCalls'>{$title}</a>
            </div>
        ";
    }
}
if (! function_exists('generateButton')) {
    function generateButton($route, $title, $class, $icon, $attributes = '')
    {
        return "
            <a href='{$route}' title='{$title}' class='btn btn-icon btn-active-light-primary w-30px h-30px {$class}' {$attributes}>
                <span class='svg-icon svg-icon-3'>{$icon}</span>
            </a>
        ";
    }
}
if (! function_exists('wrapInMenuContainer')) {

    function wrapInMenuContainer($content)
    {
        return "
            <div class='menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-175px py-4' data-kt-menu='true'>
                {$content}
            </div>
        ";
    }
}
if (! function_exists('getSvgIcon')) {

    function getSvgIcon($type, $title = '')
    {
        $icons = [
            'add' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"/>
                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"/>
                     </svg>',
            'view' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6C5.5 6 2 12 2 12C2 12 5.5 18 12 18C18.5 18 22 12 22 12C22 12 18.5 6 12 6ZM12 16C9.24 16 7 13.76 7 11C7 8.24 9.24 6 12 6C14.76 6 17 8.24 17 11C17 13.76 14.76 16 12 16Z" fill="currentColor"/>
                            <path d="M12 8C10.34 8 9 9.34 9 11C9 12.66 10.34 14 12 14C13.66 14 15 12.66 15 11C15 9.34 13.66 8 12 8Z" fill="currentColor"/>
                        </svg>',
            'edit' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="currentColor"/>
                        <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="currentColor"/>
                        <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="currentColor"/>
                    </svg>',
            'delete' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor" />
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor" />
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor" />
                            </svg>',
            'email' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <title>'.$title.'</title>

                                                        <defs/>
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M5,9 L19,9 C20.1045695,9 21,9.8954305 21,11 L21,20 C21,21.1045695 20.1045695,22 19,22 L5,22 C3.8954305,22 3,21.1045695 3,20 L3,11 C3,9.8954305 3.8954305,9 5,9 Z M18.1444251,10.8396467 L12,14.1481833 L5.85557487,10.8396467 C5.4908718,10.6432681 5.03602525,10.7797221 4.83964668,11.1444251 C4.6432681,11.5091282 4.77972206,11.9639747 5.14442513,12.1603533 L11.6444251,15.6603533 C11.8664074,15.7798822 12.1335926,15.7798822 12.3555749,15.6603533 L18.8555749,12.1603533 C19.2202779,11.9639747 19.3567319,11.5091282 19.1603533,11.1444251 C18.9639747,10.7797221 18.5091282,10.6432681 18.1444251,10.8396467 Z" fill="#999"/>
                                                            <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#999" fill-rule="nonzero" opacity="0.3" transform="translate(11.959697, 3.661508) rotate(-90.000000) translate(-11.959697, -3.661508) "/>
                                                        </g>
                                                    </svg>',
            'print' => '                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <title>'.$title.'</title>

                                                                        <defs/>
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24"/>
                                                                            <path d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z" fill="#999"/>
                                                                            <rect fill="#999" opacity="0.3" x="8" y="2" width="8" height="2" rx="1"/>
                                                                        </g>
                                                                    </svg>',
            'convert/transorm' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <title>'.$title.'</title>
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M4.56066017,7.93933983 C5.34645669,7.15354331 6.61338647,7.15354331 7.39918299,7.93933983 L15.4991834,16.0393398 C16.2849799,16.8251363 16.2849799,18.0920661 15.4991834,18.8778626 C14.7133869,19.6636591 13.4464571,19.6636591 12.6606606,18.8778626 L4.56066017,10.7778626 C3.77486366,9.99206611 3.77486366,8.72513633 4.56066017,7.93933983 Z" fill="#999" transform="translate(10.030330, 13.409010) rotate(-45.000000) translate(-10.030330, -13.409010)"/>
                                            <path d="M19.4393398,7.93933983 C20.2251363,7.15354331 21.4920661,7.15354331 22.2778626,7.93933983 C23.0636591,8.72513633 23.0636591,9.99206611 22.2778626,10.7778626 L14.1778626,18.8778626 C13.3920661,19.6636591 12.1251363,19.6636591 11.3393398,18.8778626 C10.5535433,18.0920661 10.5535433,16.8251363 11.3393398,16.0393398 L19.4393398,7.93933983 Z" fill="#999" opacity="0.3" transform="translate(16.838670, 13.409010) scale(-1, 1) rotate(-45.000000) translate(-16.838670, -13.409010)"/>
                                        </g>
                                        </svg>',

            'menu' => '<svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect y="6" width="16" height="3" rx="1.5" fill="currentColor"/>
                        <rect opacity="0.3" y="12" width="8" height="3" rx="1.5" fill="currentColor"/>
                        <rect opacity="0.3" width="12" height="3" rx="1.5" fill="currentColor"/>
                       </svg>',
            'price-offer' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12.4 6L20.7 14.3C21.1 14.7 21.1 15.3 20.7 15.7L15.7 20.7C15.3 21.1 14.7 21.1 14.3 20.7L6 12.4L6 7.5C6 6.7 6.7 6 7.5 6L12.4 6Z" fill="currentColor"/>
                                    <path d="M12.4 6L20.7 14.3C21.1 14.7 21.1 15.3 20.7 15.7L15.7 20.7C15.3 21.1 14.7 21.1 14.3 20.7L6 12.4L6 7.5C6 6.7 6.7 6 7.5 6L12.4 6Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M9 9H9.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M6.5 3.5L4 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M3.5 4.5L6 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path opacity="0.3" d="M11 13L17 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>',
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor"></rect>
                                <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor"></rect>
                                <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor"></rect>
                                <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor"></rect>
                            </svg>',
            'user_managment' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="currentColor"></path>
                                <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="currentColor"></rect>
                            </svg>',
            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M5 8.04999L11.8 11.95V19.85L5 15.85V8.04999Z" fill="currentColor"></path>
                                <path d="M20.1 6.65L12.3 2.15C12 1.95 11.6 1.95 11.3 2.15L3.5 6.65C3.2 6.85 3 7.15 3 7.45V16.45C3 16.75 3.2 17.15 3.5 17.25L11.3 21.75C11.5 21.85 11.6 21.85 11.8 21.85C12 21.85 12.1 21.85 12.3 21.75L20.1 17.25C20.4 17.05 20.6 16.75 20.6 16.45V7.45C20.6 7.15 20.4 6.75 20.1 6.65ZM5 15.85V7.95L11.8 4.05L18.6 7.95L11.8 11.95V19.85L5 15.85Z" fill="currentColor"></path>
                                </svg>',
            'service' => '<!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Smile.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 24 24" version="1.1">
                            <title>Stockholm-icons / General / Smile</title>
                            <desc>Created with Sketch.</desc>
                            <defs/>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <rect fill="currentColor"  x="2" y="2" width="20" height="20" rx="10"/>
                                <path d="M6.16794971,14.5547002 C5.86159725,14.0951715 5.98577112,13.4743022 6.4452998,13.1679497 C6.90482849,12.8615972 7.52569784,12.9857711 7.83205029,13.4452998 C8.9890854,15.1808525 10.3543313,16 12,16 C13.6456687,16 15.0109146,15.1808525 16.1679497,13.4452998 C16.4743022,12.9857711 17.0951715,12.8615972 17.5547002,13.1679497 C18.0142289,13.4743022 18.1384028,14.0951715 17.8320503,14.5547002 C16.3224187,16.8191475 14.3543313,18 12,18 C9.64566871,18 7.67758127,16.8191475 6.16794971,14.5547002 Z" fill="#000"/>
                            </g>
                        </svg>',
            'menu_webiste' => '<!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Layout/Layout-horizontal.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <rect fill="#fff" opacity="0.3" x="4" y="5" width="16" height="6" rx="1.5"/>
                                    <rect fill="#fff" x="4" y="13" width="16" height="6" rx="1.5"/>
                                </g>
                            </svg>',
            'feature_managment' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect id="bound" x="0" y="0" width="24" height="24"/>
                                        <path d="M13.5,6 C13.33743,8.28571429 12.7799545,9.78571429 11.8275735,10.5 C11.8275735,10.5 12.5,4 10.5734853,2 C10.5734853,2 10.5734853,5.92857143 8.78777106,9.14285714 C7.95071887,10.6495511 7.00205677,12.1418252 7.00205677,14.1428571 C7.00205677,17 10.4697177,18 12.0049375,18 C13.8025422,18 17,17 17,13.5 C17,12.0608202 15.8333333,9.56082016 13.5,6 Z" id="Path-17" fill="#fff"/>
                                        <path d="M9.72075922,20 L14.2792408,20 C14.7096712,20 15.09181,20.2754301 15.2279241,20.6837722 L16,23 L8,23 L8.77207592,20.6837722 C8.90818997,20.2754301 9.29032881,20 9.72075922,20 Z" id="Rectangle-49" fill="#fff" opacity="0.3"/>
                                    </g>
                                </svg>',
            'slider_managment' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect id="bound" x="0" y="0" width="24" height="24"/>
                                        <circle id="Combined-Shape" fill="#fff" cx="9" cy="15" r="6"/>
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" id="Combined-Shape" fill="#fff" opacity="0.3"/>
                                    </g>
                                </svg>',
            'review_management' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect id="bound" x="0" y="0" width="24" height="24"/>
                                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" id="Path-2" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" id="Path" fill="#fff" fill-rule="nonzero"/>
                                        <rect id="Combined-Shape" fill="#fff" opacity="0.3" x="9" y="10.5" width="4" height="1" rx="0.5"/>
                                    </g>
                                </svg>',
            'client_management' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <title>Stockholm-icons / Communication / Clipboard-check</title>
                    <desc>Created with Sketch.</desc>
                    <defs/>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="currentColor" />
                        <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                    </g>
                </svg>',
            'building_management' => '<!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Files/Compiled-file.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <title>Stockholm-icons / Files / Compiled-file</title>
                                <desc></desc>
                                <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#fff" fill-rule="nonzero" opacity="0.3"/>
                                    <rect fill="#fff" opacity="0.3" transform="translate(8.984240, 12.127098) rotate(-45.000000) translate(-8.984240, -12.127098) " x="7.41281179" y="10.5556689" width="3.14285714" height="3.14285714" rx="0.75"/>
                                    <rect fill="#fff" opacity="0.3" transform="translate(15.269955, 12.127098) rotate(-45.000000) translate(-15.269955, -12.127098) " x="13.6985261" y="10.5556689" width="3.14285714" height="3.14285714" rx="0.75"/>
                                    <rect fill="#fff" transform="translate(12.127098, 15.269955) rotate(-45.000000) translate(-12.127098, -15.269955) " x="10.5556689" y="13.6985261" width="3.14285714" height="3.14285714" rx="0.75"/>
                                    <rect fill="#fff" transform="translate(12.127098, 8.984240) rotate(-45.000000) translate(-12.127098, -8.984240) " x="10.5556689" y="7.41281179" width="3.14285714" height="3.14285714" rx="0.75"/>
                                </g>
                            </svg>',
            'lead_management' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <title>Stockholm-icons / Communication / Clipboard-check</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="currentColor" />
                                        <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                    </g>
                                </svg>',
            'Conversations' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M14 3V20H2V3C2 2.4 2.4 2 3 2H13C13.6 2 14 2.4 14 3ZM11 13V11C11 9.7 10.2 8.59995 9 8.19995V7C9 6.4 8.6 6 8 6C7.4 6 7 6.4 7 7V8.19995C5.8 8.59995 5 9.7 5 11V13C5 13.6 4.6 14 4 14V15C4 15.6 4.4 16 5 16H11C11.6 16 12 15.6 12 15V14C11.4 14 11 13.6 11 13Z" fill="currentColor"/>
                                <path d="M2 20H14V21C14 21.6 13.6 22 13 22H3C2.4 22 2 21.6 2 21V20ZM9 3V2H7V3C7 3.6 7.4 4 8 4C8.6 4 9 3.6 9 3ZM6.5 16C6.5 16.8 7.2 17.5 8 17.5C8.8 17.5 9.5 16.8 9.5 16H6.5ZM21.7 12C21.7 11.4 21.3 11 20.7 11H17.6C17 11 16.6 11.4 16.6 12C16.6 12.6 17 13 17.6 13H20.7C21.2 13 21.7 12.6 21.7 12ZM17 8C16.6 8 16.2 7.80002 16.1 7.40002C15.9 6.90002 16.1 6.29998 16.6 6.09998L19.1 5C19.6 4.8 20.2 5 20.4 5.5C20.6 6 20.4 6.60005 19.9 6.80005L17.4 7.90002C17.3 8.00002 17.1 8 17 8ZM19.5 19.1C19.4 19.1 19.2 19.1 19.1 19L16.6 17.9C16.1 17.7 15.9 17.1 16.1 16.6C16.3 16.1 16.9 15.9 17.4 16.1L19.9 17.2C20.4 17.4 20.6 18 20.4 18.5C20.2 18.9 19.9 19.1 19.5 19.1Z" fill="currentColor"/>
                                </svg>',
            'CDR' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M5 15C3.3 15 2 13.7 2 12C2 10.3 3.3 9 5 9H5.10001C5.00001 8.7 5 8.3 5 8C5 5.2 7.2 3 10 3C11.9 3 13.5 4 14.3 5.5C14.8 5.2 15.4 5 16 5C17.7 5 19 6.3 19 8C19 8.4 18.9 8.7 18.8 9C18.9 9 18.9 9 19 9C20.7 9 22 10.3 22 12C22 13.7 20.7 15 19 15H5ZM5 12.6H13L9.7 9.29999C9.3 8.89999 8.7 8.89999 8.3 9.29999L5 12.6Z" fill="currentColor"/>
                                <path d="M17 17.4V12C17 11.4 16.6 11 16 11C15.4 11 15 11.4 15 12V17.4H17Z" fill="currentColor"/>
                                <path opacity="0.3" d="M12 17.4H20L16.7 20.7C16.3 21.1 15.7 21.1 15.3 20.7L12 17.4Z" fill="currentColor"/>
                                <path d="M8 12.6V18C8 18.6 8.4 19 9 19C9.6 19 10 18.6 10 18V12.6H8Z" fill="currentColor"/>
                                </svg>',
        ];

        return $icons[$type] ?? '';
    }
}
if (! function_exists('jsonCRMResponse')) {

    function jsonCRMResponse(bool $status, string $message, int $statusCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
        ], $statusCode);
    }
}

if (! function_exists('shortURL')) {

    function shortURL($link)
    {
        try {
            if ($link) {
                $client = new Client;

                $response = $client->get(
                    'https://is.gd/create.php?format=json&url='.$link,
                    []
                );
                $d = json_decode($response->getBody(), true);

                if ($d['shorturl']) {
                    $slink = $d['shorturl'];
                }
            }

            return $slink;
        } catch (Exception $ex) {
            return $link;
        }
    }
}

if (! function_exists('uploadImage')) {
    function uploadImage($file, $path = '')
    {
        $fileName = $file->getClientOriginalName();
        $file_exe = $file->getClientOriginalExtension();
        $new_name = uniqid().'.'.$file_exe;
        $directory = 'uploads'.'/'.$path; // .'/'.date("Y").'/'.date("m").'/'.date("d");
        $destienation = public_path($directory);
        $file->move($destienation, $new_name);

        return $directory.'/'.$new_name;
    }
}

if (! function_exists('uploadExcelFile')) {
    /**
     * Upload an Excel file for student import
     *
     * @return string Full path to the uploaded file
     */
    function uploadExcelFile(UploadedFile $file, string $importType, int $jobId): string
    {
        try {
            // Create a unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = $importType.'_'.$jobId.'_'.time().'.'.$extension;

            // Use the same directory structure as uploadImage
            $directory = 'uploads/excel_imports';
            $destinationPath = public_path($directory);

            // Create directory if it doesn't exist
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move the file
            $file->move($destinationPath, $filename);
            $relativePath = $directory.'/'.$filename;

            Log::info("Excel file uploaded: {$relativePath}");

            // Return the full server path
            return public_path($relativePath);
        } catch (\Exception $e) {
            Log::error('Failed to upload Excel file: '.$e->getMessage());
            throw $e;
        }
    }
}

if (! function_exists('deleteFile')) {
    function deleteFile($oldPath, $oldName = '')
    {
        Log::info('Starting file deletion process', [
            'path' => $oldPath,
            'name' => $oldName,
        ]);

        if ($oldName == '') {
            $oldName = basename($oldPath);
            Log::info('No name provided, using basename', ['name' => $oldName]);
        }

        try {
            $message = '';

            Log::info('Checking if file exists', ['path' => $oldPath]);

            if (file_exists($oldPath)) {
                Log::info('File exists, attempting to delete');

                if (unlink($oldPath)) {
                    $message = "File '$oldName' has been deleted successfully.";
                    Log::info('File deleted successfully', [
                        'path' => $oldPath,
                        'name' => $oldName,
                    ]);
                } else {
                    $message = "There was an error deleting the file '$oldName'.";
                    Log::warning('Failed to delete file', [
                        'path' => $oldPath,
                        'name' => $oldName,
                    ]);
                }
            } else {
                $message = "File '$oldName' does not exist.";
                Log::warning('File does not exist', [
                    'path' => $oldPath,
                    'name' => $oldName,
                ]);
            }
        } catch (\Exception $e) {
            $message = 'Exception occurred while deleting file: '.$e->getMessage();
            Log::error('Error deleting file', [
                'path' => $oldPath,
                'name' => $oldName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        Log::info('File deletion process completed', [
            'result' => $message,
        ]);

        return $message;
    }
}
if (! function_exists('formatFileSize')) {

    function formatFileSize($size)
    {
        if ($size < 1024) {
            return $size.' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2).' KB';
        } elseif ($size < 1073741824) {
            return round($size / 1048576, 2).' MB';
        } else {
            return round($size / 1073741824, 2).' GB';
        }
    }
}


