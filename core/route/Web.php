<?php

namespace restpressMVC\core\route;

defined('ABSPATH') || exit;

class Web
{
    /**
     * Registers new endpoints
     *
     * @param string $name
     * @param int $places
     * @param array $handler
     * @return void
     */
    public static function register(string $name,int $places,array $handler)
    {
        // adds the endpoint
        add_action('init',function () use ($name,$places)
        {
            add_rewrite_endpoint($name,$places);
        });

        if (!get_option('stockprice_plugin_flushed_rewrite_rules'))
        {
            // flushes the rules
            add_action('wp_loaded',function ()
            {
                flush_rewrite_rules();
            });

            update_option('stockprice_plugin_flushed_rewrite_rules',true);
        }

        // adds the endpoint name into query vars
        add_filter('request', function($vars) use ($name)
        {
            if (isset($vars[$name]))
                $vars[$name] = true;

            return $vars;
        });

        // rendering the template for endpoint
        add_action('template_redirect', function() use ($name,$handler)
        {
            if (current_user_can('administrator') || current_user_can('manage_woocommerce'))
            {
                if (get_query_var($name))
                {
                    echo call_user_func($handler);
                    exit;
                }
            }
        });
    }
}