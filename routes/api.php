<?php

namespace restpressMVC\route;

// defining routes
use restpressMVC\app\controllers\api\TokenController;
use restpressMVC\app\controllers\api\UserController;
use restpressMVC\core\route\Api;

Api::register('POST','/user/create',[new UserController(),'create']);
Api::register('GET','/user/index',[new UserController(),'index']);

Api::register('POST','/token',[new TokenController(),'generateToken'],false);
