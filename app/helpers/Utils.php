<?php

namespace restpressMVC\app\helpers;

use restpressMVC\app\models\User;
use restpressMVC\bootstraps\Environment;

class Utils
{
    /**
     * returns string with translatable textdomain
     *
     * @param string $data
     * @return string
     */
    public static function withTranslation(string $data): string
    {
        return esc_html__($data,Environment::TextDomain);
    }

    public static function jwtEncode(array $payload,string $key): string
    {
        return base64_encode(json_encode($payload)).'.'.hash_hmac('sha256',json_encode($payload),$key);
    }

    public static function jwtDecode(string $token, string $key)
    {
        list($payload, $signature) = explode('.', $token);
        $valid_signature = hash_hmac('sha256',base64_decode($payload),$key);

        if ($valid_signature !== $signature)
            return false;

        return json_decode(base64_decode($payload));
    }

    public static function validateToken(string $token): bool
    {
        $payload = self::jwtDecode($token,AUTH_KEY);

        if (!$payload)
            return false;

        $user = User::find($payload->id);

        if (!$user)
            return false;

        return true;
    }

    public static function convertToEnglishDigits(string $input): array|string
    {
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $arabic  = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];

        return str_replace($persian, $english, str_replace($arabic, $english, $input));
    }
}