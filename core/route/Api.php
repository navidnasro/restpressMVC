<?php

namespace restpressMVC\core\route;

use restpressMVC\app\helpers\Utils;
use WP_Error;

defined('ABSPATH') || exit;

class Api
{
    public static function register(string $verb,string $url,array $handler,bool $hasToken = true)
    {
        add_action('rest_api_init', function () use ($verb,$url,$handler,$hasToken)
        {
            $permission = $hasToken ? [self::class,'checkBearerToken'] : '__return_true';

            register_rest_route(
                'api',
                $url,
                [
                    'methods' => $verb,
                    'callback' => $handler,
                    'permission_callback' => $permission,
                ]
            );
        });
    }

    public static function checkBearerToken()
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

        if (empty($header) || !preg_match('/Bearer\s(\S+)/', $header, $matches))
        {
            return new WP_Error(
                'unauthorized',
                'Bearer token not found',
                ['status' => 401]
            );
        }

        $token = $matches[1];
        $decodedToken = Utils::jwtDecode($token, AUTH_KEY);

        if (!$decodedToken || $decodedToken->exp != -1)
        {
            return new WP_Error(
                'unauthorized',
                'Invalid or expired token',
                ['status' => 401]
            );
        }

        $userId = $decodedToken->user_id;
        $user = get_userdata($userId);

        return user_can($user,'edit_users');
    }
}