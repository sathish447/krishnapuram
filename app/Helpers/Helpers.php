<?php

/**
 * Function to clean quotes from a value
 */


use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

// use PragmaRX\Google2FA\Google2FA;

Use PragmaRX\Google2FAQRCode\Google2FA;

error_reporting(0);


// Get User name
function userName($id)
{
    $details = \App\Data\Models\User::where('id', $id)->first();
    if(is_object($details)){
        return $details->name;
    }
    else{
        return '';
    }
}


/**
 * Function to clean a value
 */
if (!function_exists('clean')) {
    function clean($value)
    {
        $value = cleanQuotes($value);

        $text = preg_replace(array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), array('', '', '', '', '', '', '', ''), $value);
        $value = strip_tags($text);

        $value = trim($value);
        $value = ($value == "") ? null : $value;

        return $value;
    }
}

/**
 *Function to delete old image from text editor content
 */
if (!function_exists('deleteOldImageForTextEditorContent')) {
    function deleteOldImageForTextEditorContent($old_content, $content)
    {
        /**
         *  Delete old image files
         */
        $existing_image_list = [];
        $new_image_list = [];

        // Getting old image links
        preg_match_all('/(?<=src=")[^"]+(?=")/', $old_content, $srcs, PREG_PATTERN_ORDER);
        foreach ($srcs[0] as $src) {
            if (strpos($src, $_SERVER['HTTP_HOST']) == true) {
                $existing_image_list[] = $src;
            }
        }

        // Getting new image links
        preg_match_all('/(?<=src=")[^"]+(?=")/', $content, $srcs, PREG_PATTERN_ORDER);
        foreach ($srcs[0] as $src) {
            if (strpos($src, $_SERVER['HTTP_HOST']) == true) {
                $new_image_list[] = $src;
            }
        }

        // Deleting old image files
        foreach ($existing_image_list as $image) {
            if (!in_array($image, $new_image_list)) {
                $file = explode("pageContent/", $image);
                Storage::delete("pageContent/{$file[1]}");
            }
        }
    }
}

/**
 * Function to clean values in an array
 */
if (!function_exists('cleanArray')) {
    function cleanArray($data, $allowed_keys = [], $skip = [])
    {
        if (count($allowed_keys) > 0) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $allowed_keys)) {
                    unset($data[$key]);
                }
            }
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                if (is_array($data[$key])) {
                    $data[$key] = cleanArray($data[$key]);
                } else {
                    $data[$key] = clean($value);
                }
            }
        }

        return $data;
    }
}

/**
 * Function to clean values in an array
 */
if (!function_exists('cleanRequest')) {
    function cleanRequest($rules, $keys, $html_key = [], $skip = [])
    {
        $data = [];
        foreach ($rules as $k => $v) {
            if (!is_array($v)) {
                $v = explode("|", $v);
            }

            // Skip Files
            if (in_array("file", $v) || in_array($k, $skip)) {
                // Skip Null
                if (!is_null($keys->$k)) {
                    $data[$k] = $keys->$k;
                }

                continue;
            }

            if (is_array($keys->$k)) {
                $data[$k] = cleanArray($keys->$k);
            } else {
                if (!in_array($k, $html_key)) {
                    $data[$k] = clean($keys->$k);
                } else {
                    $data[$k] = $keys->$k;
                }
            }
        }

        return $data;
    }
}

/**
 * Function to AES Encrypt text
 */
if (!function_exists('lock')) {
    function lock($string, $key = "YOU-CAN-NEVER-UNLOCK-AGRIAPP")
    {
        // AES-128-CBC needs IV with length 16
        $iv = str_pad(substr($key, 0, 16), 16, "0", STR_PAD_LEFT);

        $res = openssl_encrypt($string, 'aes-128-cbc', $key, 0, $iv);
        $res = bin2hex($res);

        return $res;
    }
}

/**
 ** Function to AES Decrypt text
 */
if (!function_exists('unlock')) {
    function unlock($encrypted, $key = "YOU-CAN-NEVER-UNLOCK-AGRIAPP")
    {
        try {
            $encrypted = pack("H*", $encrypted);

            // AES-128-CBC needs IV with length 16
            $iv = str_pad(substr($key, 0, 16), 16, "0", STR_PAD_LEFT);

            $res = openssl_decrypt($encrypted, 'aes-128-cbc', $key, 0, $iv);
            if ($res == false) {
                return null;
            }
            return $res;
        } catch (\Exception $ex) {
            return null;
        }
    }
}

/**
 ** Function to return constants
 */
if (!function_exists('conf')) {
    function conf($key)
    {
        return config("constant.{$key}");
    }
}

/**
 ** Function to return version for JS & CSS
 */
if (!function_exists('ver')) {
    function ver()
    {
        return config("constant.version");
    }
}

/**
 * Function to return asset for themes
 */
if (!function_exists('btheme')) {
    function btheme()
    {
        return asset("themes/admin");
    }
}
/**
 * Function to return asset for themes
 */
if (!function_exists('ftheme')) {
    function ftheme()
    {
        return asset("themes/web");
    }
}

/**
 * Function to image preference
 */
if (!function_exists('imgPref')) {
    function imgPref($key)
    {
        return "Preferred dimension is " . config("constant.{$key}.width") . "px x " . config("constant.{$key}.height") . "px";
    }
}

/**
 * Function Action Buttons
 */
if (!function_exists('actionButtons')) {
    /**
     * PARAMETER SAMPLE
    // "edit" => route('role.edit', [$result->id]),
    // "editAjax" => [
    //     "id" => $result->id,
    //     "function" => "addEdit",
    // ],
    // "status" => [
    //     "id" => $result->id,
    //     "status" => $result->is_active,
    //     "datatable_id" => "datatable",
    // ],
    // "delete" => [
    //     "id" => $result->id,
    //     "datatable_id" => "datatable",
    // ],
    // "reorder" => $result->id,
    // "link" => [
    //     "url" =>  route('role.edit', [$result->id]),
    //     "icon" => "fa-action fas fa-check-circle",
    // ],
    // "languageModal" => [
    //     "id" => $result->id,
    //     "function" => "addEdit",
    // ],
    // "custom" => [
    //     "id" => $result->id,
    //     "title" => "Check Details",
    //     "icon" => "fa-info-circle",
    //     "function" => "checkPaymentDetails",
    // ],
     */
    function actionButtons($data)
    {
        $html = [];
        foreach ($data as $k => $v) {
            // Edit Button
            if ($k == "edit") {
                $html[] = "<a href='{$v}' class='text-primary m-1' title='Edit'><i class='fa-action fa fa-edit'></i></a>";
            }

            // Edit Button Ajax
            elseif ($k == "editAjax") {
                $html[] = "<a href='javascript:' class='text-primary m-1' title='Edit' onclick='{$v["function"]}({$v["id"]})'>
                <i class='fa-action fa ".($v['icon'] ?? 'fa-edit')."'></i></a>";
            }

            // Enable/Disable Button
            elseif ($k == "status") {
                $new_status = ($v["status"] == 0) ? 1 : 0;
                $temp = "<a href='javascript: confirmStatusModal({$v["id"]}, {$new_status}, \"{$v["datatable_id"]}\")'";
                if ($v["status"] == 0) {
                    $temp .= "class=' m-1' title='Enable'><i class='fa-action fas fa-toggle-off'></i>";
                } else {
                    $temp .= "class='text-success m-1' title='Disable'><i class='fa-action fas fa-toggle-on'></i>";
                }
                $temp .= "</a>";

                $html[] = $temp;
            }

            // Delete Button
            elseif ($k == "delete") {
                $html[] = "<a href='#' onclick='return confirmStatusModal(\"{$v["id"]}\", 2, \"{$v["datatable_id"]}\")'
                                class='text-danger m-1' title='Delete'><i class='fa-action fa fa-trash'></i></a>";
            }

            // Delete Button
            elseif ($k == "search_keys") {
                $html[] = "<a href='javascript:void(0);' onclick='searchKeys({$v["id"]});' class='text-secondary m-1' title='search Keys'><i class='fa-action fa fa-search'></i></a>";
            }

            // Reorder
            elseif ($k == "reorder") {
                $html[] = "<i class='fas fa-arrows-alt reorder'></i>";
            }

            // Link
            elseif ($k == "link") {
                $html[] = "<a href='{$v["url"]}' class='text-primary m-1'><i class='{$v["icon"]}'></i></a>";
            }

            elseif ($k == "link-text-another-tab") {
                $html[] = "<a href='{$v["url"]}' class='text-primary m-1' target='_blank'><i class='{$v["icon"]}'></i></a>";;
            }

            // Language Box
            elseif ($k == "languageModal") {
                $html[] = "<a href='javascript:' class='text-primary m-1' title='Edit' onclick='{$v["function"]}({$v["id"]})'>
                <i class='fa-action fa fa-edit'></i></a>";
            }

            // Edit Button Ajax
            elseif ($k == "custom") {
                $html[] = "<a href='javascript:' class='text-primary m-1' title='{$v["title"]}' onclick='{$v["function"]}({$v["id"]})'>
                <i class='fa-action fa {$v["icon"]}'></i></a>";
            }
            elseif ($k == "link-icon") {
                $html[] = "<a href='{$v["link"]}' class='text-primary m-1' title='{$v["title"]}' >
                <i class='fa-action fa {$v["icon"]}'></i></a>";
            }
            elseif ($k == "link-icon-danger") {
                $html[] = "<a href='{$v["link"]}' class='text-primary btn btn-danger btn-sm m-1' title='{$v["title"]}' >{$v['text']}</a>";
            }
            elseif ($k == "link-icon-2") {
                $html[] = "<a href='{$v["link"]}' class='text-primary m-1' title='{$v["title"]}' >
                <i class='fa-action fa {$v["icon"]}'></i></a>";
            }
            elseif ($k == "link-icon-3") {
                $html[] = "<a href='{$v["link"]}' class='text-primary m-1' title='{$v["title"]}' >
                <i class='{$v["icon"]}'></i></a>";
            }
            elseif ($k == "link-text") {
                $html[] = "<a href='{$v["link"]}' class='btn btn-".($v["btn"] ?? 'primary')." btn-sm' title='".($v["title"] ?? '')."' ".($v['extra'] ?? '') ." >
                {$v['text']}</a>";
            }
            elseif ($k == "link-text-2") {
                $html[] = "<a href='{$v["link"]}' class='btn btn-".($v["btn"] ?? 'primary')." btn-sm' title='".($v["title"] ?? '')."' >{$v['text']}</a>";
            }
            elseif ($k == "link-text-3") {
                $html[] = "<a href='{$v["link"]}' class='btn btn-".($v["btn"] ?? 'primary')." btn-sm' title='".($v["title"] ?? '')."' >
                {$v['text']}</a>";
            }

        }

        return implode("", $html);
    }
}


/**
 * Round Number
 */
if (!function_exists('roundNumber')) {
    function roundNumber($format, $value)
    {
        $round = (int) preg_replace('/[^0-9]/', '', $format);
        return sprintf($format, round($value, $round));
    }
}


/**
 * Parse youtube url
 */
if (!function_exists('getYoutubeVideoID')) {
    function getYoutubeVideoID($url)
    {
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
        if (isset($matches[1])) {
            return "<img src='https://img.youtube.com/vi/{$matches[1]}/mqdefault.jpg' width='50px'/>";
        }
        return "Invalid URL";
    }
}

/**
 * Get Column Names
 */
if (!function_exists('getColumnName')) {
    function getColumnName($table)
    {
        $db = config("database.connections.mysql.database");
        $result = \DB::select(\DB::raw("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `TABLE_SCHEMA`='{$db}' AND TABLE_NAME = '{$table}' ORDER BY ORDINAL_POSITION"));

        $columns = [];
        foreach ($result as $r) {
            $columns[$r->COLUMN_NAME] = $r->COLUMN_NAME;
        }

        return $columns;
    }
}

/**
 * Genarate Slug
 */
if (!function_exists('slug')) {
    function slug($slug)
    {
        return strtolower(preg_replace(conf("slug_format"), '', $slug));
    }
}


/**
 * Some Server Image uploading not working so we need to use this function
 */
if (!function_exists('uploadImageOld')) {
    function uploadImageOld($file, $dimension, $path, $name = "", $no_conversion = true)
    {
        $path = $path . "/" . date('ymd');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename_ori = conf("prefix") . uniqid();
        if ($name != "") {
            $filename_ori = slug($name) . "-" . $filename_ori;
        }
        $filename = $filename_ori . ".{$extension}";

        // save to temp
        $file->storeAs('temp', $filename, "public");
        $temp_path = "storage/temp/public/{$filename}";
        // Resize preferred dimension
        $img = \Image::make($temp_path);
//        $img = \Image::make($file);

        $height = $img->height();
        $width = $img->width();

        return $img;
        $image = "{$path}/{$filename}";

        if ($no_conversion) {
            \Storage::put($image, (string) $img->encode());
        } else {
            $height = conf("{$dimension}.height");
            $width = conf("{$dimension}.width");

            if ($img->width() != $width || $img->height() != $height) {
                $img->resize($width, $height);
                $img->save($temp_path, 100);
            }

            \Storage::put($image, (string) $img->encode());
        }

        $height = ceil($height / 2);
        $width = ceil($width / 2);

        $img->resize($width, $height);
        \Storage::put("thumb/{$image}", (string) $img->encode());

        \File::delete($temp_path);
        return $image;
    }
}

/**
 * Function to upload image
 */
if (!function_exists('uploadImage')) {
    function uploadImage($file, $dimension, $path, $name = "", $no_conversion = true)
    {
        $path = $path . "/" . date('ymd');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename_ori = conf("prefix") . uniqid();
        if ($name != "") {
            $filename_ori = slug($name) . "-" . $filename_ori;
        }
        $filename = $filename_ori . ".{$extension}";

        // save to temp
        $file->storeAs('temp', $filename, "public");
//        $temp_path = "storage/temp/{$filename}"; // old
        // added by me
        $temp_path = "app/public/temp/{$filename}";
        $temp_path = storage_path($temp_path);
        //end eadded by me

        // Resize preferred dimension
//        $img = \Image::make($temp_path);

        $img = \Image::make($file);

        $height = $img->height();
        $width = $img->width();

        $image = "{$path}/{$filename}";

        if ($no_conversion) {
            \Storage::put($image, (string) $img->encode());
        } else {
            $height = conf("{$dimension}.height");
            $width = conf("{$dimension}.width");

            if ($img->width() != $width || $img->height() != $height) {
                $img->resize($width, $height);
                $img->save($temp_path, 100);
            }

            \Storage::put($image, (string) $img->encode());
        }

        $height = ceil($height / 2);
        $width = ceil($width / 2);

        $img->resize($width, $height);
        \Storage::put("thumb/{$image}", (string) $img->encode());

        \File::delete($temp_path);
        return $image;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($file, $path, $name = "")
    {
        $path = $path . "/" . date('ymd');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename_ori = conf("prefix") . uniqid();
        if ($name != "") {
            $filename_ori = slug($name) . "-" . $filename_ori;
        }
        $filename = $filename_ori . ".{$extension}";

        $saved_file = "{$path}/{$filename}";
        $file->storeAs($path, $filename);
        return $saved_file;
    }
}

/**
 * Function to download image
 */
if (!function_exists('downloadImage')) {
    function downloadImage($file, $dimension, $path, $name = "", $no_conversion = true)
    {
        $path = $path . "/" . date('ymd');
        $extension = explode(".", $file);
        $extension = strtolower(end($extension));
        $filename_ori = conf("prefix") . uniqid();
        if ($name != "") {
            $filename_ori = slug($name) . "-" . $filename_ori;
        }
        $filename = $filename_ori . ".{$extension}";
        try {
            // save to temp
            $contents = file_get_contents($file);
            \Storage::disk('public')->put("temp/{$filename}", $contents);
            $temp_path = "app/public/temp/{$filename}";
            $temp_path = storage_path($temp_path);
            //end eadded by me
            $img = \Image::make($temp_path);

            $height = $img->height();
            $width = $img->width();

            $image = "{$path}/{$filename}";

            if ($no_conversion) {
                \Storage::put($image, (string) $img->encode());
            } else {
                $height = conf("{$dimension}.height");
                $width = conf("{$dimension}.width");

                if ($img->width() != $width || $img->height() != $height) {
                    $img->resize($width, $height);
                    $img->save($temp_path, 100);
                }

                \Storage::put($image, (string) $img->encode());
            }

            $height = ceil($height / 2);
            $width = ceil($width / 2);

            $img->resize($width, $height);
            \Storage::put("thumb/{$image}", (string) $img->encode());

            \File::delete($temp_path);
            return $image;
        } catch (\Exception $ex) {
            return null;
        }
    }
}

/**
 * Function to upload image
 */
if (!function_exists('deleteImage')) {
    function deleteImage($file)
    {
        try {
            \Storage::delete("{$file}");
            \Storage::delete("thumb/{$file}");
        } catch (\Exception $ex) {
        }
    }
}

/**
 * Return Message
 */
if (!function_exists('returnMsg')) {
    function returnMsg($msg_code = '200')
    {
        switch ($msg_code) {
            case '201':
                return "Resource created successfully!";
                break;
            case '404':
                return "Resource not found!";
                break;
            case '500':
                return "Something went wrong!";
                break;
            default:
                return "Resource updated successfully!";
                break;
        }
    }
}

/**
 * Google Translator
 */
if (!function_exists('googleTranslate')) {
    function googleTranslate($text, $target)
    {
        if ($target == 'en' || empty($text)) {
            return $text;
        }

        $url = 'https://www.googleapis.com/language/translate/v2?key=' . conf('google_language_api_key') . '&q=' . rawurlencode($text) . '&source=en&target=' . $target;

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($http_code == 200) {
            $responseDecoded = json_decode($response, true);
            if (isset($responseDecoded["data"]["translations"][0]["translatedText"])) {
                return $responseDecoded["data"]["translations"][0]["translatedText"];
            }
        }

        return null;
    }
}

/**
 * Language Converter
 */
if (!function_exists('languageConverter')) {
    function languageConverter($language_fields, $data)
    {
        $activeLanguages = activeLanguages();
        $data["language"] = [];
        foreach ($activeLanguages as $language) {
            if (!isset($data["language"][$language->id])) {
                $data["language"][$language->id] = $language_fields;
            }

            $all_field_null = true;
            foreach ($language_fields as $key => $value) {
                $data["language"][$language->id][$key] = googleTranslate($data[$key], $language->language_code);

                if (!is_null($data["language"][$language->id][$key])) {
                    $all_field_null = false;
                }
            }

            if ($all_field_null) {
                unset($data["language"][$language->id]);
            }
        }
        return $data;
    }
}

if (!function_exists('activeLanguages')) {
    function activeLanguages()
    {
        return \Cache::rememberForever('activeLanguages', function () {
            $language_elq = new \App\Data\Repositories\Language\LanguageEloquent;
            $activeLanguages = $language_elq->all([
                ["is_active", 1]
            ], false);
            return $activeLanguages;
        });
    }
}

if (!function_exists('languageList')) {
    function languageList()
    {
        return \Cache::rememberForever('languageList', function () {
            $language_elq = new \App\Data\Repositories\Language\LanguageEloquent;
            $languages = $language_elq->all([], false);
            return $languages;
        });
    }
}

if (!function_exists('defaultLanguageId')) {
    function defaultLanguageId()
    {
        return \Cache::rememberForever('defaultLanguageId', function () {
            $language_elq = new \App\Data\Repositories\Language\LanguageEloquent;
            $lan = $language_elq->all([
                ["is_default", 1]
            ]);
            return $lan->id;
        });
    }
}

if (!function_exists('getLanguageId')) {
    function getLanguageId($id)
    {
        return \Cache::rememberForever('getLanguageId_' . $id, function () use ($id) {
            $language_elq = new \App\Data\Repositories\Language\LanguageEloquent;
            $lan = $language_elq->all([
                ["id", $id], ["is_active", 1]
            ]);
            if (is_null($lan)) {
                return defaultLanguageId();
            }
            return $lan->id;
        });
    }
}

if (!function_exists('onlyNumber')) {
    function onlyNumber($text)
    {
        return preg_replace("/[^0-9]/", "", $text);
    }
}

if (!function_exists('amountWithoutTax')) {
    function amountWithoutTax($price, $tax)
    {
        $price_without_tax = ($price * 100) / (100 + $tax);

        $tax = roundNumber("%.2f", ($price_without_tax * $tax) / 100);

        $price_without_tax = roundNumber("%.2f", ($price - $tax));

        return [
            "price_without_tax" => $price_without_tax,
            "tax" => $tax
        ];
    }
}

if (!function_exists('otp')) {
    function otp($data)
    {
        $data["phone_code"] = onlyNumber($data["phone_code"]);
        $data["phone"] = onlyNumber($data["phone"]);
        $username = $data["phone_code"] . $data["phone"];
        $OtpRepository = new \App\Data\Repositories\Otp\OtpEloquent;
        $UserRepository = new \App\Data\Repositories\User\UserEloquent;

        $user = $UserRepository->all([
            ["phone", $data["phone"]],
            ["wherein","role_id", ['13','23','48'], ''] // 13 = Admin, 23 = Catalogue, 48 = Qa Team
        ]);

        if (!is_null($user) && $user->is_active == 1) {
            $sms_otp = rand(1000, 9999);
            $otp = $OtpRepository->all([
                ["phone", $username]
            ]);
            if (is_null($otp)) {
                $OtpRepository->create([
                    "phone" => $username,
                    "otp" => $sms_otp
                ]);
            } elseif (strtotime($otp->updated_at) + conf("opt_valid_for_sec") < time()) {
                $OtpRepository->update([
                    "otp" => $sms_otp
                ], [
                    ["phone", $username]
                ]);
            } else {
                $sms_otp = $otp->otp;
            }
            sendOTP($username, $sms_otp);
        }
    }
}

if (!function_exists('validateOTP')) {
 function validateOTP($data)
 {
     $data["phone_code"] = onlyNumber($data["phone_code"]);
     $data["phone"] = onlyNumber($data["phone"]);
     $username = $data["phone_code"] . $data["phone"];
     $OtpRepository = new \App\Data\Repositories\Otp\OtpEloquent;

     // Change OTP so that it cannot be reused
     $sms_otp = rand(1000, 9999);
     $OtpRepository->update([
         "otp" => $sms_otp
     ], [
         ["phone", $username]
     ]);

     return true;
 }
}

function sendOtpNew($smsOtp, $username, $var) {
    $curl = curl_init();
    $url = 'https://control.msg91.com/api/v5/otp';
    $templateId = '64f03275d6fc056faa7fd1d3';
    $authKey = '74544Al7qQoN5Oq54620e81';
    $data = [
        'otp' => $smsOtp,
        'template_id' => $templateId,
        'invisible' => 0,
        'mobile' => $username,
        'var1' => $var,
    ];

    $jsonData = json_encode($data);

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'authkey: ' . $authKey,
            'Content-Type: application/json',
            'Cookie: PHPSESSID=4d6viamt0vhhht2hgqsnarnbm7',
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


if (!function_exists('resendNewOTP')) {
    function resendOtpNew($phone, $type) {
        $type = $type == 1 ? "text" : "voice";
        $authKey = '74544Al7qQoN5Oq54620e81';
        $url = "https://api.msg91.com/api/v5/otp/retry";

        $data = [
            'mobile' => $phone,
            'authkey' => $authKey,
            'retrytype' => $type,
        ];

        try {
            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => http_build_query($data), // Send data as POST fields
            ]);

            // Execute cURL session
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }

            // Close cURL session
            curl_close($ch);

            return response()->json([
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500); // You can adjust the HTTP status code as needed
        }
    }

}

if (!function_exists('sendOTP')) {
    function sendOTP($phone, $otp)
    {
        try {
            $template_id = conf("msg91.template_id");
            if ($template_id) {
                $auth_key = conf("msg91.auth_key");

                $url = "https://api.msg91.com/api/v5/otp?template_id={$template_id}&mobile={$phone}&authkey={$auth_key}&otp={$otp}&invisible=";
                Http::get($url);
            }

        } catch (\Excerption $ex) {
        }
    }
}

if (!function_exists('resendOTP')) {
    function resendOTP($phone, $type)
    {
        $type = $type == 1 ? "text" : "voice";
        try {
            $template_id = conf("msg91.template_id");
            $auth_key = conf("msg91.auth_key");

            $url = "https://api.msg91.com/api/v5/otp/retry?mobile={$phone}&authkey={$auth_key}&retrytype={$type}";
            Http::get($url);
        } catch (\Excerption $ex) {
        }
    }
}

if (!function_exists('clearLanguageCache')) {
    function clearLanguageCache($id)
    {
        \Cache::forget("activeLanguages");
        \Cache::forget("languageList");
        \Cache::forget("defaultLanguageId");
        \Cache::forget("getLanguageId_{$id}");
    }
}


if (!function_exists('clearBannerListCache')) {
    function clearBannerListCache()
    {
        \Cache::forget("bannerList");
    }
}

if (!function_exists('clearB2COffersListCache')) {
    function clearB2COffersListCache()
    {
        \Cache::forget("b2cOffersList");
    }
}



if (!function_exists('duplicateRequestValidator')) {
    function duplicateRequestValidatorStart($function, $user_id)
    {
        if (config("app.env") == "local") {
         return false;
        }
        $cache_name = $function . "_" . $user_id;
        if (\Cache::has($cache_name)) {
            return ['response' => 'error', 'message' => conf("prevent_duplicate_message"), 'httpCode' => 406];
        }

        \Cache::put($cache_name, 1, now()->addMinutes(conf("prevent_duplicate_for_x_min")));
        return $cache_name;
    }
}

if (!function_exists('duplicateRequestValidatorEnd')) {
    function duplicateRequestValidatorEnd($cache_name)
    {
        \Cache::forget($cache_name);
    }
}

if (!function_exists('apiResponse')) {
    function apiResponse($data)
    {
        $httpCode = $data["httpCode"] ?? 200;

        $response = [
            'error_code' => ($data["response"]) == "error" ? 1 : 0,
        ];

        if (!empty($data["message"])) {
            $response["message"] = $data["message"];
        }

        unset($data["message"], $data["response"], $data["httpCode"]);

        if (count($data) > 0 && $response["error_code"] == 0) {
            $response["data"] = $data;
        }

        if(env('APP_ENV') == 'production'){
            $alloOrignUrl = 'https://agriapp.com';
        }
        else{
            // $alloOrignUrl = 'http://localhost:4200';
            $alloOrignUrl = 'http://52.66.135.48';
        }

        return response(json_encode($response), $httpCode)
                    ->header('X-Frame-Options', 'SAMEORIGIN')
                    ->header('Strict-Transport-Security', 'max-age=63072000; includeSubDomains')
                    ->header('X-Content-Type-Options', 'nosniff')
                    ->header('Content-Type', 'application/json')
                    ->header('Referrer-Policy', 'origin-when-cross-origin')
                    // ->header('Feature-Policy', '* self');  -- Blocks Mobile API
                    ->header('X-XSS-Protection', '1; mode=block')
                    ->header('Access-Control-Allow-Origin', $alloOrignUrl)
                    // ->header('Access-Control-Allow-Origin', '*');
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}

if (!function_exists('instaMojoPaymentRequest')) {
    function instaMojoPaymentRequest($data)
    {
        if (isset($data["email"]) && empty($data["email"])) {
            $data["email"] = "support@agriapp.com";
        }

        $error = (object) [
            'success' => false
        ];
        try {
            // Version 1.1
            if(conf("insta_mojo.api_version") == "1.1") {
                $app_key = conf("insta_mojo.api_key");
                $auth_token = conf("insta_mojo.auth_token");

                // Create a Payment Request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v1_1") . "/payment-requests/");
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:{$app_key}", "X-Auth-Token:{$auth_token}"));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                return json_decode($json);
            }

            // Version 2
            else if(conf("insta_mojo.api_version") == "2") {
                $bearer_token = instaMojoGenerateBearerToken();
                if ($bearer_token == "") {
                    return $error;
                }

                // Create a Payment Request
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v2") . '/v2/payment_requests/');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $bearer_token));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                $payment_request = json_decode($json);
                $payment_request_id = ($payment_request->id) ?? "";

                // Create an Order using Payment Request ID
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v2") . '/v2/gateway/orders/payment-request/');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $bearer_token));

                $payload = [ 'id' => $payment_request_id ];

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                $json = json_decode($json);
                $payment_request->order = $json;
                $payment_request->order_id = ($json->order_id) ?? "";

                $payment_request = (object) [
                    'success' => true,
                    'payment_request' => $payment_request
                ];

                return $payment_request;
            }
        } catch (\Exception $e) {
        }

        return $error;
    }
}

if (!function_exists('instaMojoGetPaymentDetails')) {
    function instaMojoGetPaymentDetails($reference_id, $for_refund = false)
    {
        $error = (object) [
            'success' => false
        ];
        try {
            // Version 1.1
            if(conf("insta_mojo.api_version") == "1.1") {
                $app_key = conf("insta_mojo.api_key");
                $auth_token = conf("insta_mojo.auth_token");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v1_1") . "/payments/" . $reference_id);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:{$app_key}", "X-Auth-Token:{$auth_token}"));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                return json_decode($json);
            }

            // Version 2
            else if(conf("insta_mojo.api_version") == "2") {
                $bearer_token = instaMojoGenerateBearerToken();
                if ($bearer_token == "") {
                    return $error;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v2") . "/v2/payments/" . $reference_id);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $bearer_token));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                $json = json_decode($json);

                if(!$for_refund) {
                    $json->payment_id = $json->id ?? "";
                    if($json->status == true || $json->status == "true") {
                        $json->status = "Credit";
                    }
                }

                $payment_details = (object) [
                    'success' => true,
                    'payment' => $json
                ];

                return $payment_details;
            }
        } catch (\Exception $e) {
        }

        return $error;
    }
}

if (!function_exists('instaMojoRefund')) {
    function instaMojoRefund($reference_id)
    {
        $error = (object) [
            'success' => false
        ];
        try {
            // Version 1.1
            if(conf("insta_mojo.api_version") == "1.1") {
                $app_key = conf("insta_mojo.api_key");
                $auth_token = conf("insta_mojo.auth_token");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v1_1") . "/refunds/");
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:{$app_key}", "X-Auth-Token:{$auth_token}"));

                $payload = [
                    'transaction_id' => time() . uniqid(),
                    'payment_id' => $reference_id,
                    'type' => 'TAN',
                    'body' => 'Payment Canceled by Backend Staff'
                ];

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                return json_decode($json);
            }

            // Version 2
            else if(conf("insta_mojo.api_version") == "2") {
                $bearer_token = instaMojoGenerateBearerToken();
                if ($bearer_token == "") {
                    return $error;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v2") . "/v2/payments/" . $reference_id . "/refund/");
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $bearer_token));

                $payload = [
                    'transaction_id' => time() . uniqid(),
                    'payment_id' => $reference_id,
                    'type' => 'TAN',
                    'body' => 'Payment Canceled by Backend Staff'
                ];

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    return $error;
                }

                return json_decode($json);
            }
        } catch (\Exception $e) {
        }

        return $error;
    }
}

if (!function_exists('instaMojoGenerateBearerToken')) {
    function instaMojoGenerateBearerToken()
    {
        $sec = 3600;
        return Cache::remember('instaMojoGenerateBearerToken', $sec, function () {
            $bearer_token = "";
            try {
                $client_id = conf("insta_mojo.client_id");
                $client_secret = conf("insta_mojo.client_secret");

                // Generate Bearer Token
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, conf("insta_mojo.api_url_v2") . '/oauth2/token/');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

                $payload = Array(
                    'grant_type' => 'client_credentials',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret
                );

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response = curl_exec($ch);
                curl_close($ch);

                $json = trim($response);
                $json = str_replace("\n", "", $json);
                $json = str_replace("\r", ",", $json);
                $json = str_replace("\n", ',', $json);
                $json = rtrim($json, "\x00..\x1F");

                if (empty($json)) {
                    $bearer_token = "";
                } else {
                    $json = json_decode($json);
                    $bearer_token = ($json->access_token) ?? "";
                }
            } catch (\Exception $e) {
            }

            return $bearer_token;
        });
    }
}

/**
 ** Function to Create Topic in Firebase
 */
if (!function_exists('firebaseCreateTopic')) {
    function firebaseCreateTopic($topic, $token)
    {
        try {
            $url = conf("firebase.create_topic_url");

            $post = [
                "to" => "/topics/{$topic}",
                "registration_tokens" => [
                    $token
                ]
            ];

            $headers = array(
                'Authorization: key=' . conf("firebase.server_key"),
                'Content-Type: application/json',
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response != "") {
                $response = json_decode($response, true);
            }

            if ($http_code == 200) {
                return [
                    'response' => 'success',
                    'data' => $response,
                ];
            }

            return [
                'httpCode' => $http_code,
                'response' => 'error',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'response' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}

/**
 ** Function to Delete Topic in Firebase
 */
if (!function_exists('firebaseRemoveTopic')) {
    function firebaseRemoveTopic($topic, $token)
    {
        try {
            $url = conf("firebase.remove_topic_url");

            $post = [
                "to" => "/topics/{$topic}",
                "registration_tokens" => [
                    $token
                ]
            ];

            $headers = array(
                'Authorization: key=' . conf("firebase.server_key"),
                'Content-Type: application/json',
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response != "") {
                $response = json_decode($response, true);
            }

            if ($http_code == 200) {
                return [
                    'response' => 'success',
                    'data' => $response,
                ];
            }

            return [
                'httpCode' => $http_code,
                'response' => 'error',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'response' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}

/**
 ** Function to Send Push Android
 */
if (!function_exists('firebaseTopicNotificationAndroid')) {
    function firebaseTopicNotificationAndroid(
        $topic,
        $title,
        $message,
        $image = ""
    ) {
        try {
            $url = conf("firebase.notification_url");

            $post = [
                "to" => "/topics/{$topic}",
                'notification' => [
                    "title" => $title,
                    "body" => $message,
                ],
                'data' => [
                    "title" => $title,
                    "body" => $message,
                    "image" => $image,
                    "notification_foreground" => "true"
                ]
            ];

            $headers = array(
                'Authorization: key=' . conf("firebase.server_key"),
                'Content-Type: application/json',
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response != "") {
                $response = json_decode($response, true);
            }

            if ($http_code == 200) {
                return [
                    'response' => 'success',
                    'data' => $response,
                ];
            }

            return [
                'httpCode' => $http_code,
                'response' => 'error',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'response' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}

/**
 * Function Validate FCM
 */
if (!function_exists('firebaseValidateToken')) {
    function firebaseValidateToken($token)
    {
        try {
            $url = conf("firebase.validate_fcm_url") . "/{$token}";
            $headers = array(
                'Authorization: key=' . conf("firebase.server_key"),
                'Content-Type: application/json',
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response != "") {
                $response = json_decode($response, true);
            } else {
                $response = [];
            }

            if ($http_code != 200 && isset($response["error"])) {
                return false;
            }
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::info($e);
        }
        return true;
    }
}

if (!function_exists('WhatsappNotification')) {
    function WhatsappNotification($phone, $bodyValues, $template_name)
    {
        $curl = curl_init();

        $postData = array(
            "channelId" => "63fedf3b664e862006bfc635",
            "channelType" => "whatsapp",
            "recipient" => array(
                "name" => "AgriApp",
                "phone" => $phone
            ),
            "whatsapp" => array(
                "type" => "template",
                "template" => array(
                    "templateName" => $template_name,
                    "bodyValues" => $bodyValues
                )
            )
        );

        $postDataString = json_encode($postData);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://server.gallabox.com/devapi/messages/whatsapp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postDataString,
            CURLOPT_HTTPHEADER => array(
                'apiKey: 641c26636a50a26c6b4a490f',
                'apiSecret: 717a2f1650ca40058c2ce3c5d080e40c',
                'Content-Type: application/json'
            ),
        ));

        // Execute the cURL request
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        return $response;
    }
}

if (!function_exists('sendWhatsAppMessage')) {
    function sendWhatsAppMessage($templateid, $customerContacts, $params)
    {
        $messageId = 0;
        if (!config('constant.whatsapp_notification')) {
            return $messageId;
        }

        try {
            $parameters = [];
            foreach ($params as $value) {
                $parameters[] = array("type" => "text", "text" => $value);
            }
            $components[] = array("type" => "body", "parameters" => $parameters);
            $client = new Client();
            $response = $client->post(
                'https://api.tellephant.com/v1/send-message',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'apikey' => 't94NEsS2sH9wLLhs6RcZck15wUrpAyAUKQrac039a8Fkkjwvk2MnGdGfxtQL',
                        'to' => $customerContacts,
                        'channels' => [
                            'whatsapp',
                        ],
                        'whatsapp' => [
                            'contentType' => 'template',
                            'template' => [
                                'templateId' => "$templateid",
                                'language' => 'en',
                                'components' => $components,
                            ],
                        ],
                    ],
                ]
            );

            $json = json_decode($response->getBody());
            $messageId = $json->messageId;

        } catch (\Exception $e) {
            $messageId = 0;
            \Illuminate\Support\Facades\Log::info($e);
        }
        return $messageId;
    }
}


if (!function_exists('arrayToText')) {
    function arrayToText($array)
    {
        return implode(', ', array_map(
            function ($v, $k) {
                return sprintf("%s: %s", $k, $v);
            },
            $array,
            array_keys($array)
        ));
    }
}



/**
 * Function convert amount to text
 */
if (!function_exists('numberTowords')) {
    function numberTowords(float $amount)
    {
        $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = array();
        $change_words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $here_digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($x < $count_length) {
            $get_divider = ($x == 2) ? 10 : 100;
            $amount = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($amount) {
                $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
                $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
                $string[] = ($amount < 21) ? $change_words[$amount] . ' ' . $here_digits[$counter] . $add_plural . '
            ' . $amt_hundred : $change_words[floor($amount / 10) * 10] . ' ' . $change_words[$amount % 10] . '
            ' . $here_digits[$counter] . $add_plural . ' ' . $amt_hundred;
            } else {
                $string[] = null;
            }
        }
        $implode_to_Rupees = implode('', array_reverse($string));
        $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . "
    " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
        $amount_text = ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
        $amount_text = trim(preg_replace('!\s+!', ' ', $amount_text));
        return $amount_text;
    }
}


/**
 ** Function to Send Push Android
 */
if (!function_exists('sendPushAndroid')) {
    function sendPushAndroid(
        $token_array,
        $title,
        $message,
        $goto_page = "Home",
        $obj = null,
        $image = ""
    ) {
        try {
            $goto_page_id = 0;
            $is_paid = 0;
            $amount = 0;
            if(!is_null($obj)) {
                $goto_page_id = $obj->goto_page_id;
                $is_paid = $obj->is_paid;
                $amount = $obj->amount;
            }

            $url = conf("firebase.notification_url");

            $post = [
                'registration_ids' => $token_array,
                'notification' => [
                    "title" => $title,
                    "body" => $message,
                ],
                'data' => [
                    "title" => $title,
                    "body" => $message,
                    "image" => $image,
                    "goto_page" => $goto_page,
                    "goto_page_id" => $goto_page_id,
                    "is_paid" => $is_paid,
                    "amount" => $amount,
                    "notification_foreground" => "true"
                ]
            ];

            $headers = array(
                'Authorization: key=' . conf("firebase.server_key"),
                'Content-Type: application/json',
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            $result = curl_exec($ch);

            if ($result === false) {

                return false;
            }
            curl_close($ch);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 ** Function to Send Notification
 */
if (!function_exists('sendNotification')) {
    function sendNotification(
        $obj_type,
        $obj,
        $user_id,
        $title = null,
        $message = null,
        $push = true,
        $sms = true,
        $email = true,
        $whatsapp = true
    ) {
        try {

            $notification_type = 1;
            $notification_is_paid = 0;
            $notification_amount = 0;
            $notification_created_by = 0;
            $goto_page = null;
            $goto_page_id = null;
            $whatsapp_template = "";
            $whatsapp_no = "";
            $whatsapp_array = [];
            $whatsapp_new_line = ", ";
            $id_array = [
                "notification_order",
                "notification_soil_test",
                "notification_drone_service",
                "website_url",
                "phone",
                "email"
            ];
            $settings_elq = new \App\Data\Repositories\Settings\SettingsEloquent;
            $settings = $settings_elq->whereInID($id_array);

            $user_elq = new \App\Data\Repositories\User\UserEloquent;
            $user = $user_elq->all([
                ["id", $user_id]
            ]);

            $reference_id = 0;

            // Soil Test
            if ($obj_type == 1) {
                $reference_id = $obj->id;

                if (is_null($title)) {
                    $title = "Soil Test";
                }

                if (is_null($message)) {
                    $message = $settings->where("id", "notification_soil_test")->first()->value;

                    $message = preg_replace('/ORDER_ID/i', $obj->id, $message);
                    $message = preg_replace('/CUSTOMER_ID/i', $user->id, $message);
                }

                $goto_page = "Soil Test";
                $goto_page_id = $reference_id;

                if($whatsapp) {
                    $whatsapp_template = "soil_test_service";
                    $whatsapp_no = $obj->phone_code . $obj->phone;

                    $settings_phone = $settings->where("id", "phone")->first()->value;
                    $settings_email = $settings->where("id", "email")->first()->value;

                    $whatsapp_array = [
                        $obj->name,
                        $obj->id,
                        $obj->soilTestPlan->en()->name,
                        "Order No. {$obj->id}",
                        $obj->soilTestLab->address,
                        $settings_phone,
                        $settings_email
                    ];
                }
            }

            // Order
            elseif ($obj_type == 2) {
                $reference_id = $obj->id;

                if (is_null($title)) {
                    $title = "Order Placed";
                }

                if (is_null($message)) {
                    $message = $settings->where("id", "notification_order")->first()->value;
                    $message = preg_replace('/ORDER_ID/i', $obj->customer_order_id, $message);
                    $message = preg_replace('/CUSTOMER_ID/i', $user->id, $message);
                }

                $goto_page = "Order";
                $goto_page_id = $obj->customer_order_id;
                if($whatsapp) {
                    $plan_check = userPlanCheck($user_id);
                    // Only for Platinum Farmers
                    if($plan_check->is_paid == 1) {
                        $whatsapp_template = "agriapp_purchase_confirmation_v1";
                        $whatsapp_no = $obj->billing_phone_code . $obj->billing_phone;
                        $settings_phone = $settings->where("id", "phone")->first()->value;
                        $settings_email = $settings->where("id", "email")->first()->value;

                        $order_details = [
                            "Order Date: " . date("d M Y h:i A", strtotime($obj->created_at)),
                            "Deliver To: {$obj->address_text}",
                            "Bill To: {$obj->billing_address_text}"
                        ];
                        $order_details = implode($whatsapp_new_line, $order_details);

                        $payment_summary = [];
                        foreach($obj->items as $item) {
                            $p_sum = [];
                            $p_sum[] = "Product: {$item->name} ({$item->variant_name})";
                            $p_sum[] = "Quantity: {$item->quantity}";
                            $p_sum[] = "Unit Price: INR " . number_format($item->paid_amount_without_tax/$item->quantity, 2);
                            $p_sum[] = "Net Price: INR {$item->paid_amount_without_tax}";

                            $payment_summary[] = implode(", ", $p_sum);
                        }
                        $payment_summary = implode($whatsapp_new_line, $payment_summary);

                        if ($obj->total_discount > $obj->total_price) {
                            $obj->total_discount = $obj->total_price;
                        }

                        if ($obj->paid_amount != 0) {
                            $obj->total_price = $obj->paid_amount-$obj->total_tax_in_paid_amount+$obj->total_discount;
                            $obj->total_price = number_format($obj->total_price, 2);
                        }

                        $purchase_summary = [
                            "Subtotal: INR {$obj->total_price}",
                            "Tax: INR {$obj->total_tax_in_paid_amount}",
                            "Discount: INR {$obj->total_discount}",
                            "Total: INR {$obj->paid_amount}",
                        ];
                        $purchase_summary = implode($whatsapp_new_line, $purchase_summary);
                        $whatsapp_array = [
                            $obj->name,
                            conf("name"),
                            $obj->customer_order_id,
                            "INR {$obj->billing->price}",
                            $obj->billing->payment_mode_name,
                            $order_details,
                            $payment_summary,
                            $purchase_summary,
                            conf("name"),
                            conf("name"),
                            $settings_phone,
                            $settings_email
                        ];
                    }
                }
            }

            // Drone Order
            elseif ($obj_type == 3) {
                $reference_id = $obj->id;

                if (is_null($title)) {
                    $title = "Drone service request created";
                }

                if (is_null($message)) {
                    $message = $settings->where("id", "notification_drone_service")->first()->value;

                    $message = preg_replace('/ORDER_ID/i', $obj->customer_order_id, $message);
                    $message = preg_replace('/CUSTOMER_ID/i', $user->id, $message);
                    $message = preg_replace('/ORDER_REQUESTED_DATE/i', date("d-m-Y", strtotime($obj->requested_date)), $message);
                }

                $goto_page = "Drone";
                $goto_page_id = $obj->customer_order_id;

                if($whatsapp) {
                    $whatsapp_template = "drone_order_confirmation_n";
                    $whatsapp_no = $obj->billing_phone_code . $obj->billing_phone;

                    $settings_phone = $settings->where("id", "phone")->first()->value;
                    $settings_email = $settings->where("id", "email")->first()->value;

                    $crop_selected = [];
                    foreach ($obj->droneCrops as $droneCrop) {
                        $crop_selected[] = $droneCrop->en()->name;
                    }
                    $crop_selected = implode($whatsapp_new_line, $crop_selected);

                    $ordered_product = [];
                    foreach($obj->items as $item) {
                        $p_sum = [];
                        $p_sum[] = "Product: {$item->name} ({$item->variant_name})";
                        $p_sum[] = "Quantity: {$item->quantity}";
                        $p_sum[] = "Unit Price: INR " . number_format($item->paid_amount_without_tax/$item->quantity, 2);
                        $p_sum[] = "Net Price: INR {$item->paid_amount_without_tax}";

                        $ordered_product[] = implode(", ", $p_sum);
                    }
                    $ordered_product = implode($whatsapp_new_line, $ordered_product);

                    $payment_summary = [
                        "Subtotal: INR {$obj->total_price}",
                        "Tax: INR {$obj->total_tax_in_paid_amount}",
                        "Discount: INR {$obj->total_discount}",
                        "Total: INR {$obj->paid_amount}",
                    ];
                    $payment_summary = implode($whatsapp_new_line, $payment_summary);

                    $whatsapp_array = [
                        $obj->name,
                        $obj->area_in_acre,
                        $ordered_product,
                        date("d M Y h:i A", strtotime($obj->requested_date)),
                        $obj->address_text,
                        $payment_summary,
                        conf("name"),
                        $settings_phone,
                        $settings_email
                    ];
                }
            }

            // New Farm
            elseif ($obj_type == 4) {
                if (is_null($title)) {
                    $title = "New farm created";
                }
                if (is_null($message)) {
                    $message = "Thank you for registering your crop. Hope you enjoy Agri Inputs & services.";
                }

                $reference_id = $obj->id;
                $whatsapp_template = "crop_register_welcome_note_v1";
                $whatsapp_no = $obj->phone_code . $obj->phone;

                $whatsapp_array = [
                    $obj->user_name,
                    conf("name")
                ];

                $goto_page = "Farm";
                $goto_page_id = $reference_id;
            }

            // Order Cancel
            elseif ($obj_type == 5) {
                $reference_id = $obj->id;

                if (is_null($title)) {
                    $title = "Order Cancelled";
                }

                if (is_null($message)) {
                    $message = "Your order:{$obj->customer_order_id} has been cancelled successfully";
                }

                $goto_page = "Order";
                $goto_page_id = $obj->customer_order_id;
            }

            // Order Cancel Drone
            elseif ($obj_type == 6) {
                $reference_id = $obj->id;

                if (is_null($title)) {
                    $title = "Order Cancelled";
                }

                if (is_null($message)) {
                    $message = "Your order:{$obj->customer_order_id} has been cancelled successfully";
                }

                $goto_page = "OrderDroneCancel";
                $goto_page_id = $obj->customer_order_id;
            }

            // Plan Change
            elseif ($obj_type == 8) {
                $reference_id = $obj->plan_id;
                $notification_type = $obj->notification_type;
                $notification_is_paid = $obj->notification_is_paid;
                $notification_amount = $obj->notification_amount;
                $notification_created_by = $obj->notification_created_by;

                $goto_page = "Plan";
                $goto_page_id = $reference_id;
            }

            // Crop Calender Notification
            elseif ($obj_type == 9) {
                $reference_id = $obj->id;
                $whatsapp_template = "farm_activity_notif_v1";
                $whatsapp_no = $obj->phone_code . $obj->phone;

                $whatsapp_array = [
                    $obj->user_name,
                    date("d/m/Y", strtotime($obj->notification_date)),
                    $obj->area,
                    $obj->crop_name,
                    $obj->operation,
                    $obj->practice,
                    $obj->tips,
                    $obj->settings_obj->phone,
                    $obj->settings_obj->website_url,
                    conf("name")
                ];

                // $goto_page = "Crop Practices";
                $goto_page = "Farm";
                $goto_page_id = $obj->user_farm_id;
            }

            // Order Whatsapp Message Notification
            elseif ($obj_type == 10) {
                $reference_id = $obj->id;
                $whatsapp_template = "order_confirmation_call_missed";
                $whatsapp_no = $obj->phone_code . $obj->phone;

                $settings_phone = $settings->where("id", "phone")->first()->value;

                $whatsapp_array = [
                    $obj->user_name,
                    $obj->customer_order_id,
                    $settings_phone
                ];

                $goto_page = "Order";
                $goto_page_id = $obj->customer_order_id;
            }

            // Push Notification
            $push_id = 0;
            if ($push) {
                if (!empty($title) && !empty($message)) {
                    $data = [
                        "title" => $title,
                        "description" => $message,
                        "type" => $notification_type,
                        "reference_id" => $reference_id,
                        "goto_page" => $goto_page,
                        "goto_page_id" => $goto_page_id,
                        "amount" =>  $notification_amount,
                        "is_paid" => $notification_is_paid,
                        "created_by" => $notification_created_by,
                        "users" => [$user_id]
                    ];

                    $UserNotification_elq = new \App\Data\Repositories\UserNotification\UserNotificationEloquent;
                    $res = $UserNotification_elq->create($data);
                    $data["obj"] = $res;
                    $push_id = $res->id;

                    try {
                        \App\Jobs\SendUserPushNotificationJob::dispatch($data);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::info($e);
                    }
                }
            }

            // SMS
            if ($sms) {
            }

            // Email
            if ($email) {
            }

            // WhatsApp
            if ($whatsapp && $whatsapp_template != "" && $whatsapp_no != "" && count($whatsapp_array) != 0) {
                try {
                    $whats_app_id = sendWhatsAppMessage($whatsapp_template, $whatsapp_no, $whatsapp_array);
                    if ($push_id != 0) {
                        \DB::table("user_notifications")->where("user_id", $user_id)->where("notification_id", $push_id)
                                                        ->update(["whats_app_id" => $whats_app_id]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::info($e);
                }
            }

            return [
                "title" => $title,
                "message" => $message
            ];
        } catch (\Exception $e) {
//            dump($e);
            \Illuminate\Support\Facades\Log::info($e);
        }

    }
}

/**
 ** Function to Send Notification
 */
if (!function_exists('checkEmpty')) {
    function checkEmpty($value) {
        if(is_null($value) || $value == "") {
            return true;
        }

        return false;
    }
}


/**
 * Check if Current Route is Active
 * @param string $route Larval Route
 * @param string $return if Route Active than reaturn the string
 * @return String|null active class name or empty return hoga
 * active-page
 */
function routeActive(string $route = '', $return = 'active'): ?string
{
    return request()->routeIs($route) ? $return : null;
}

/**
 * Image Path to Public Image URl its return default image
 * @param null $path path with file name of image
 * @param string $disk disk you used to upload
 */
function image_url($path=null, $disk ='upload'): string
{
    return  $path ? Storage::disk($disk)->url($path)  : asset(config('constants.no_images'));
}


function list_thumb_image($path=null, $disk ='upload'): string
{
//    return '<img src="'.image_url($path, $disk).'" class="img-thumbnail" alt="'.$path.'" width="70" height="70">';
    return '<img src="'.image_url($path, $disk).'" class="img-thumbnail"  width="70" height="70">';
}

/**
 * Generate OTP
 */
function generateOTP(): int
{
    return rand(1000, 9999);
}

/**
 * role name return
 */
function roleName($roleid)
{
    $data = \DB::table("role")->where('id', $roleid)->first();

    if(is_object($data)){
        return $data->name;
    }else{
        return 'admin';
    }
}


if (!function_exists('convertTextToHtml')) 
{
    function convertTextToHtml($text)
    {
        // Split the text into paragraphs
        $paragraphs = explode("\n\n", $text);

        // Start building the HTML
        $html = '<div>';
        foreach ($paragraphs as $paragraph) {
            // Add a <p> tag for each paragraph
            $html .= '<p>' . nl2br($paragraph) . '</p>';
        }
        $html .= '</div>';

        return $html;
    }
}
