<?php
/*
 * Plugin Name:       Seller Hub
 * Plugin URI:        https://github.com/navidnasro
 * Description:       manages stock and price of products
 * Version:           3.3.2
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            NavidNasro
 * Author URI:        https://github.com/navidnasro
 * License:           MIT
 * License URI:       https://mit-license.org/
 * Update URI:        https://github.com/navidnasro
 * Text Domain:       seller hub
 * Domain Path:       /languages
 */

namespace stockPriceManager;

use sellerhub\bootstraps\Plugin;

define('ROOT',plugin_dir_url(__FILE__));
define('SiteUrl',get_site_url());
define('PluginRoot',__DIR__);
define('PluginFile',__FILE__);

if (session_status() === PHP_SESSION_NONE)
    session_start();

require_once PluginRoot.'/vendor/autoload.php';
require_once PluginRoot.'/routes/api.php';

Plugin::getPlugin()->init();
