<?php

namespace sellerhub\app\models;

use sellerhub\core\database\model\Model;

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

    public function getWithdrawals(): ?Withdrawal
    {
        return Withdrawal::where('user_id', $this->id);
    }

    public function hasOpenWithdrawals(): ?Withdrawal
    {
        return Withdrawal::where('user_id', $this->id)->andWhere('status','pending')->first();
    }

    public function getProducts()
    {
        if ($this->role == 'vendor')
        {
            $sql = "
            SELECT p.* 
            FROM ".self::getTable('sales')." s 
                INNER JOIN ".self::getTable('guarantees')." g ON s.vendor_id = g.user_id 
                INNER JOIN ".self::getTable('products')." p ON g.product_id = p.id 
            WHERE s.vendor_id = %d;
            ";

            $params = [$this->id];
        }

        else
        {
            $sql = "
            SELECT DISTINCT p.*
            FROM ".self::getTable('products')." p
                INNER JOIN ".self::getTable('guarantees')." g ON p.id = g.product_id
                INNER JOIN ".self::getTable('sales')." s ON g.id = s.guarantee_id
            WHERE (g.user_id = %d OR s.customer_id = %d)
            ";

            $params = [$this->id,$this->id];
        }

        return self::query($sql,$params,Product::class);
    }

    public function getCustomers()
    {
        if ($this->role != 'vendor')
            return [];

        $sql = "
        SELECT DISTINCT u.* 
        FROM ".self::getTable('sales')." s 
            INNER JOIN ".self::getTable('users')." u ON s.customer_id = u.id 
        WHERE vendor_id = %d;
        ";

        return self::query($sql,[$this->id]);
    }

    public function getProductsSoldTo(int $customerId)
    {
        if ($this->role != 'vendor')
            return [];

        $sql = "
        SELECT p.* 
        FROM ".self::getTable('sales')." s 
            INNER JOIN ".self::getTable('guarantees')." g ON s.guarantee_id = g.id 
            INNER JOIN ".self::getTable('products')." p ON g.product_id = p.id 
        WHERE s.vendor_id = %d AND customer_id = %d
        ";

        return self::query($sql,[$this->id,$customerId]);
    }

    public function getProofs()
    {
        return VendorProof::where('vendor_id',$this->id)->get();
    }

    public function getHistory()
    {
        if ($this->role == 'vendor')
        {
            $sql = "
            SELECT p.name AS `product`,g.guarantee_code AS `code`,u.* 
            FROM ".self::getTable('sales')." s 
                INNER JOIN ".self::getTable('guarantees')." g ON s.guarantee_id = g.id 
                INNER JOIN ".self::getTable('products')." p ON g.product_id = p.id 
                INNER JOIN ".self::getTable('users')." u ON s.customer_id = u.id 
            WHERE s.vendor_id = %d;
            ";

            $params = [$this->id];
        }

        else
        {
            $sql = "
            SELECT DISTINCT p.name AS `product`,g.guarantee_code AS `code`
            FROM ".self::getTable('products')." p
                INNER JOIN ".self::getTable('guarantees')." g ON p.id = g.product_id
                INNER JOIN ".self::getTable('sales')." s ON g.id = s.guarantee_id
            WHERE (g.user_id = %d OR s.customer_id = %d)
            ";

            $params = [$this->id,$this->id];
        }

        return self::query($sql,$params);
    }
}