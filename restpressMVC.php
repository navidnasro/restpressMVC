<?php
/*
 * Plugin Name:       restpressMVC
 * Plugin URI:        https://github.com/navidnasro/restpressMVC
 * Description:       manages stock and price of products
 * Version:           1.0.3
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            NavidNasro
 * Author URI:        https://github.com/navidnasro
 * License:           MIT
 * License URI:       https://mit-license.org/
 * Update URI:        https://github.com/navidnasro/restpressMVC
 * Text Domain:       restpressMVC
 * Domain Path:       /languages
 */

namespace restpressMVC;

use restpressMVC\bootstraps\Plugin;

define('ROOT',plugin_dir_url(__FILE__));
define('SiteUrl',get_site_url());
define('PluginRoot',__DIR__);
define('PluginFile',__FILE__);

if (session_status() === PHP_SESSION_NONE)
    session_start();

require_once PluginRoot.'/vendor/autoload.php';
require_once PluginRoot.'/routes/api.php';

Plugin::getPlugin()->init();
