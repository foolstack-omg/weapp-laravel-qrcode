<?php

namespace App\Http\Requests\Api;

use App\Constants\Constant;
use Illuminate\Validation\Rule;

class QrcodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'int|required',
            'url' => 'string|required_if:type,'.Constant::QR_CODE_TYPE_URL,
            'text' => 'string|required_if:type,'.Constant::QR_CODE_TYPE_TEXT,
            'bit_coin_address' => 'string|required_if:type,'.Constant::QR_CODE_TYPE_BIT_COIN,
            'bit_coin_amount' => 'numeric|required_if:type,'.Constant::QR_CODE_TYPE_BIT_COIN,
            'bit_coin_label' => 'string',
            'bit_coin_message' => 'string',
            'phone_number' => 'integer|required_if:type,'.Constant::QR_CODE_TYPE_PHONE,
            'sms_phone_number' => 'integer|required_if:type,'.Constant::QR_CODE_TYPE_SMS,
            'wifi_ssid' => 'string|required_if:type,'.Constant::QR_CODE_TYPE_WIFI,
            'wifi_password' => 'string|required_if:type,'.Constant::QR_CODE_TYPE_WIFI,
            'geo_latitude' => 'numeric|required_if:type,'.Constant::QR_CODE_TYPE_LOCATION,
            'geo_longitude' => 'numeric|required_if:type,'.Constant::QR_CODE_TYPE_LOCATION,
            'email_to' => 'email',
            'email_subject' => 'string',
            'email_content' => 'string',
            'size' => 'required|integer|min:100|max:1000',
            'margin' => 'required|integer|min:0|max:5',
            'error_correction' => [
                'required',
                'string',
                'size:1',
                Rule::in(['L', 'M', 'Q', 'H'])
            ],
            'color' => [
                'required',
                'regex:/^(#[a-fA-F0-9]{6})|(#[a-fA-F0-9]{3})$/'
            ],
            'background_color' => [
                'required',
                'regex:/^(#[a-fA-F0-9]{6})|(#[a-fA-F0-9]{3})$/'
            ],
            'logo_percentage' => 'integer',
        ];
    }

    public function attributes()
    {
        return [
            'type' => '二维码类型',
            'url' => '网址',
            'text' => '文本',
            'bit_coin_address' => '比特币地址',
            'bit_coin_amount' => '比特币金额',
            'bit_coin_label' => '比特币收款人名称',
            'bit_coin_message' => '比特币附加信息',
            'phone_number' => '手机号码',
            'sms_phone_number' => '短信发送手机号码',
            'sms_message' => '短信内容',
            'wifi_ssid' => 'Wifi名',
            'wifi_password' => 'Wifi密码',
            'geo_latitude' => '经度',
            'geo_longitude' => '纬度',
            'email_to' => '收件人',
            'email_subject' => '标题',
            'email_content' => '内容',
            'size' => '二维码大小',
            'margin' => '外边宽度',
            'error_correction' => '容错',
            'color' => '前景色',
            'background_color' => '背景色',
            'background_transparent' => '背景色透明',
            'logo_percentage' => 'Logo百分比',
        ];
    }
}
