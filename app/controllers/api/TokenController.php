<?php

namespace sellerhub\app\controllers\api;

use sellerhub\app\helpers\Utils;
use WP_REST_Request;
use WP_REST_Response;

class TokenController
{
    /**
     * Generate a token for the user
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function generateToken(WP_REST_Request $request): WP_REST_Response
    {
        $username = sanitize_text_field($request->get_param('username'));
        $password = $request->get_param('password');

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user))
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'causes' => $user->get_error_codes(),
                        'messages' => $user->get_error_messages(),
                    ],
                ],
                401
            );
        }

        $payload = [
            'user_id' => $user->ID,
            'iat'     => time(),
            'exp'     => -1,
        ];

        $token = Utils::jwtEncode($payload, AUTH_KEY);

        return new WP_REST_Response(
            [
                'data' => [
                    'token' => $token,
                ],
            ],
            200
        );
    }
}