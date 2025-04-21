<?php

namespace sellerhub\database\seeders;

use sellerhub\core\database\seeder\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
        $table = $this->prefix . 'users';
        $resources = $this->getResources(137);

        foreach ($resources as $resource)
            $this->wpdb->insert($table,$resource);
    }

    private function getResources(int $count): array
    {
        $resources = [];
        for ($i = 0; $i < $count; $i++)
        {
            $createdAt = self::$faker->dateTimeBetween('-8 years')->format('Y-m-d H:i:s');

            $resources[] = [
                'name'              => self::$faker->name(),
                'phone_number'      => self::$faker->unique()->phoneNumber(),
                'city'              => self::$faker->city(),
                'national_id'       => self::$faker->unique()->numerify('###########'),
                'role'              => self::$faker->randomElement(['customer', 'vendor']),
                'wallet_balance'    => self::$faker->numberBetween(0, 100000),
                'created_at'        => $createdAt,
                'updated_at'        => $createdAt
            ];
        }

        return $resources;
    }
};
