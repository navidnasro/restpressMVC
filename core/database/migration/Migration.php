<?php

namespace restpressMVC\core\database\migration;

use restpressMVC\bootstraps\Environment;
use wpdb;

abstract class Migration
{
    protected wpdb $wpdb;
    protected string $prefix;
    protected string $charset;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix.Environment::TablePreFix;
        $this->charset = $wpdb->get_charset_collate();
    }

    abstract public function up(): void;
    abstract public function down(): void;
}