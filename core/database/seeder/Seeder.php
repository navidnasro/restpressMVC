<?php

namespace sellerhub\core\database\seeder;

use Faker\Factory;
use Faker\Generator;
use wpdb;
use sellerhub\bootstraps\Environment;

abstract class Seeder
{
    protected wpdb $wpdb;
    protected string $prefix;
    protected static ?Generator $faker = null;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix.Environment::TablePreFix;

        if (is_null(self::$faker))
            self::$faker = Factory::create('fa_IR');
    }

    abstract public function run(): void;
}