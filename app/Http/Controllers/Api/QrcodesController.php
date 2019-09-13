<?php
/**
 * Created by PhpStorm.
 * User: liuxiaofeng
 * Date: 2019-03-16
 * Time: 16:50
 */

namespace App\Http\Controllers\Api;



use App\Constants\Constant;
use App\Http\Requests\Api\QrcodeRequest;
use Image;
use QrCode;
use SimpleSoftwareIO\QrCode\DataTypes\PhoneNumber;

class QrcodesController extends Controller
{
    /**
     * 生成二维码
     * @param QrcodeRequest $request
     * @return mixed
     */
    public function generate(QrcodeRequest $request) {


        $qrcode = QrCode::format('png')->encoding('UTF-8');
        $color_rgb = $this->hex2rgb($request->color);
        $background_color_rgb = $this->hex2rgb($request->background_color);
        $qrcode->size($request->size)
            ->margin($request->margin)
            ->color($color_rgb[0], $color_rgb[1], $color_rgb[2])
            ->backgroundColor($background_color_rgb[0], $background_color_rgb[1], $background_color_rgb[2])
            ->errorCorrection($request->error_correction);

        // Logo
        if($request->file('logo')) {
            $logo = $request->file('logo');
            $logo_path = $logo->getRealPath();
            \Log::debug('Logo Path:'. $logo_path);
            $percentage = .3;
            if($request->input('logo_percentage')) {
                $percentage = bcdiv($request->logo_percentage, 100, 2);
            }
            $qrcode->merge($logo_path, $percentage, true);

            $qrcode->errorCorrection('H');
        }




        $file_data = '';
        switch ($request->type) {
            case Constant::QR_CODE_TYPE_URL:
                $url = $request->url;
                if(!starts_with($url, ['http://', 'https://'])) {
                    $url = "http://".$url;
                }
                $file_data = $qrcode->generate($url);
                break;
            case Constant::QR_CODE_TYPE_TEXT:
                $file_data = $qrcode->generate($request->text);
                break;
            case Constant::QR_CODE_TYPE_PHONE:
                $file_data = $qrcode->phoneNumber($request->phone_number);
                break;
            case Constant::QR_CODE_TYPE_SMS:
                $file_data = $qrcode->SMS($request->sms_phone_number, $request->sms_message ?? '');
                break;
            case Constant::QR_CODE_TYPE_EMAIL:
                $file_data = $qrcode->email($request->email_to ?? null, $request->email_subject ?? null, $request->email_content ?? null);
                break;
            case Constant::QR_CODE_TYPE_LOCATION:
                $file_data = $qrcode->geo($request->geo_latitude, $request->geo_longitude);
                break;
            case Constant::QR_CODE_TYPE_BIT_COIN:
                $extra = [];
                if($request->input('bit_coin_label')) {
                    $extra['label'] = $request->input('bit_coin_label');
                }
                if($request->input('bit_coin_message')) {
                    $extra['message'] = $request->input('bit_coin_message');
                }
                $file_data = $qrcode->BTC($request->bit_coin_address, $request->bit_coin_amount, $extra);

                break;
            case Constant::QR_CODE_TYPE_WIFI:
                $file_data = $qrcode->wiFi([
                    'encryption' => 'WPA',
                    'ssid' => $request->wifi_ssid,
                    'password' => $request->wifi_password
                ]);
                break;
        }


        $base_64 = "data:image/png;base64, ".base64_encode($file_data);
        // <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate('Make me into an QrCode!')) !!} ">

        return $this->success(['qrcode' => $base_64]);
    }

    /**
     * 十六进制转RGB
     *
     * @param string $color 16进制颜色值
     * @return array
     */
    private function hex2rgb($color)
    {
        $hexColor = str_replace('#', '', $color);
        $lens = strlen($hexColor);
        if ($lens != 3 && $lens != 6) {
            return false;
        }
        $newcolor = '';
        if ($lens == 3) {
            for ($i = 0; $i < $lens; $i++) {
                $newcolor .= $hexColor[$i] . $hexColor[$i];
            }
        } else {
            $newcolor = $hexColor;
        }
        $hex = str_split($newcolor, 2);
        $rgb = [];
        foreach ($hex as $key => $vls) {
            $rgb[] = hexdec($vls);
        }
        return $rgb;
    }

}
