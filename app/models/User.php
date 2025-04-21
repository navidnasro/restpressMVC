<?php

namespace restpressMVC\app\models;

use restpressMVC\core\database\model\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByPhone($phone): ?User
    {
        $row = self::getDB()->get_row(
            self::getDB()->prepare(
                "SELECT * FROM " . static::getTable() . " WHERE phone_number = %s",
                $phone
            )
        );

        return $row ? static::hydrate($row) : null;
    }
}