<?php

namespace sellerhub\app\controllers\api;

use sellerhub\app\helpers\Utils;
use sellerhub\app\models\User;
use sellerhub\app\models\VendorProof;
use sellerhub\core\storage\Export;
use WP_REST_Request;
use WP_REST_Response;

class UserController
{
    public function index(WP_REST_Request $request): WP_REST_Response
    {
        $users = User::all();
        $response = [];

        foreach ($users as $user)
        {
            $response[] = [
                'id'             => $user->id,
                'name'           => $user->name,
                'national_id'    => $user->national_id,
                'role'           => $user->role,
                'city'           => $user->city,
                'phone'          => $user->phone_number,
                'balance'        => $user->wallet_balance
            ];
        }

        return new WP_REST_Response(
            [
                'data' => $response,
            ],
            200
        );
    }

    public function create(WP_REST_Request $request): WP_REST_Response
    {
        $name = sanitize_text_field($request->get_param('name'));
        $city = sanitize_text_field($request->get_param('city'));
        $nationalId = sanitize_text_field($request->get_param('national_id'));
        $phoneNumber = sanitize_text_field($request->get_param('phone_number'));

        // vendor creates the customer here , so role is always customer

        if (empty($name) || empty($phoneNumber))
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'ارسال نام و شماره تماس اجباریست'
                    ],
                ],
                400
            );
        }

        if (!preg_match('/^09\d{9}$/', Utils::convertToEnglishDigits($phoneNumber)))
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'شماره تلفن وارده باید به صورت 11 رقم و با 09 شروع شود!',
                    ],
                ],
                400
            );
        }

        if (User::findByPhone($phoneNumber))
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'شماره تماس از قبل وجود دارد!'
                    ],
                ],
                400
            );
        }

        if (mb_strlen($name,'UTF-8') < 3)
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'حداقل حروف برای نام لحاظ نشده',
                    ],
                ],
                400
            );
        }

        if (!empty($nationalId) && !preg_match('/^\d{10}$/', Utils::convertToEnglishDigits($nationalId)))
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'کدملی معتبر نیست',
                    ],
                ],
                400
            );
        }

        $userId = User::create(
            [
                'name'          => $name,
                'city'          => $city ? $city : null,
                'phone_number'  => $phoneNumber,
                'role'          => 'customer',
                'national_id'   => $nationalId ? $nationalId : null
            ]
        );

        if (!$userId)
        {
            return new WP_REST_Response(
                [
                    'data' => [
                        'message' => 'خطا هنگام ایجاد حساب کاربری'
                    ],
                ],
                500
            );
        }

        $payload = [
            'id'        => $userId,
            'phone'     => $phoneNumber,
            'time'      => time()
        ];

        $token = Utils::jwtEncode($payload, AUTH_KEY);

        return new WP_REST_Response(
            [
                'data' => [
                    'id'            => $userId,
                    'token'         => $token,
                    'name'          => $name,
                    'city'          => $city,
                    'role'          => 'customer',
                    'balance'       => 0,
                    'national_id'   => $nationalId,
                    'phone'         => $phoneNumber,
                    'message'       => 'حساب کاربری با موفقیت ایجاد شد',
                ],
            ],
            201
        );
    }
}