<?php

namespace sellerhub\route;

// defining routes
use sellerhub\app\controllers\api\AuthController;
use sellerhub\app\controllers\api\GuaranteeController;
use sellerhub\app\controllers\api\ProductController;
use sellerhub\app\controllers\api\TokenController;
use sellerhub\app\controllers\api\UserController;
use sellerhub\app\controllers\api\WalletController;
use sellerhub\core\route\Api;

Api::register('POST','/user/create',[new UserController(),'create']);
Api::register('GET','/user/index',[new UserController(),'index']);

Api::register('POST','/token',[new TokenController(),'generateToken'],false);
