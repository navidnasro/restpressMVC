<?php

namespace restpressMVC\bootstraps;

use restpressMVC\app\controllers\api\GuaranteeController;
use restpressMVC\app\controllers\api\WalletController;
use restpressMVC\app\helpers\Utils;
use restpressMVC\app\models\VendorProof;
use restpressMVC\core\database\migration\MigrationManager;
use restpressMVC\core\database\seeder\SeederManager;
use WP_Query;
use WP_REST_Request;

defined('ABSPATH') || exit;

class Plugin
{
    private static ?Plugin $instance = null;

    /**
     * Returns plugin instance
     *
     * @return Plugin
     */
    public static function getPlugin(): Plugin
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * Initializes plugin
     *
     * @return void
     */
    public function init(): void
    {
        add_action('admin_menu',[$this,'addMenu']);
        add_action('admin_notices', [$this,'showNotices']);
//        add_action('rest_api_init', [$this,'enableCors']);

        register_activation_hook(PluginFile, [$this,'runMigrations']);
//        register_deactivation_hook(PluginFile, [$this,'rollbackMigrations']);
    }

    public function runMigrations(): void
    {
        $migrationManager = new MigrationManager();
        $migrationManager->runMigrations();

        $seederManager = new SeederManager();
        $seederManager->runSeeders();
    }

    public function rollbackMigrations(): void
    {
        $migrationManager = new MigrationManager();
        $migrationManager->rollbackAllMigrations();
    }

    /**
     * adds plugin menu
     *
     * @return void
     */
    public function addMenu(): void
    {
        add_menu_page(
            Utils::withTranslation('باشگاه فروشندگان'),
            Utils::withTranslation('باشگاه فروشندگان'),
            'manage_options',
            'club_dashboard',
            [$this,'renderDashboard'],
            'dashicons-store',
            2
        );

        add_submenu_page(
            'club_dashboard',
            Utils::withTranslation('کاربران'),
            Utils::withTranslation('کاربران'),
            'manage_options',
            'club_dashboard_users',
            function (){ require_once __DIR__.'/../app/views/customers-and-vendors.php'; }
        );
    }

    public function enableCors(): void
    {
        header("Access-Control-Allow-Origin: https://club.com");
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");

        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            http_response_code(200);
            exit;
        }
    }

    /**
     * shows notices
     *
     * @return void
     */
    public function showNotices(): void
    {
        if (isset($_GET['show-message']) && $_GET['show-message'] === 'success' &&
            isset($_GET['show']) && $_GET['show'] === 'withdraw-request' &&
            isset($_GET['page']) && $_GET['page'] === 'seller_club_dashboard')
        {
            echo '<div class="notice notice-success is-dismissible"><p>'.$_GET['message'].'</p></div>';
        }

        else if (isset($_GET['show-message']) && $_GET['show-message'] === 'success' &&
            isset($_GET['show']) && $_GET['show'] === 'guarantee-submission' &&
            isset($_GET['page']) && $_GET['page'] === 'seller_club_dashboard')
        {
            echo '<div class="notice notice-success is-dismissible"><p>'.$_GET['message'].'</p></div>';
        }
    }

    /**
     * renders plugin menu dashboard
     *
     * @return void
     */
    public function renderDashboard(): void
    {
        $view = isset($_GET['show']) ? $_GET['show'] : 'index';

        if ($view === 'index')
            require_once __DIR__.'/../app/views/index.php';

        else if ($view === 'customers-and-vendors')
            require_once __DIR__.'/../app/views/customers-and-vendors.php';
    }
}