<?php

namespace sellerhub\core\database\model;

use sellerhub\bootstraps\Environment;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    // store query state
    protected static array $conditions = [];
    protected static string $orderBy = '';
    protected static string $limit = '';
    protected static array $conditionValues = [];

    public static function all()
    {
        return static::query("SELECT * FROM " . static::getTable());
    }

    public static function latest(bool $beCreatedAt = false)
    {
        $column = $beCreatedAt ? 'created_at' : 'updated_at';
        static::$orderBy = " ORDER BY ".$column." DESC";
        return new static();
    }

    public static function find($id)
    {
        return static::queryRow(
            "SELECT * FROM " . static::getTable() . " WHERE " . static::$primaryKey . " = %d",
            [$id]
        );
    }

    public static function create($data)
    {
        static::getDB()->insert(static::getTable(), $data);
        return static::getDB()->insert_id;
    }

    public static function update($id, $data)
    {
        return static::getDB()->update(static::getTable(), $data, [static::$primaryKey => $id]);
    }

    public static function delete($id)
    {
        return static::getDB()->delete(static::getTable(), [static::$primaryKey => $id]);
    }

    public static function where($column, $operator, $value = null)
    {
        // If $value is null, assume the operator is '=' and $value is the second argument
        if ($value === null)
        {
            $value = $operator;
            $operator = '=';
        }

        // Validate the operator
        if (!in_array($operator, ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN']))
            return null;

        // Add the condition and value
        static::$conditions[] = "$column $operator %s";
        static::$conditionValues[] = $value;

        // Return the class name for chaining
        return new static();
    }

    public function andWhere($column, $operator, $value = null)
    {
        if ($value === null)
        {
            $value = $operator;
            $operator = '=';
        }

        if (!in_array($operator, ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN']))
            return null;

        if (!empty(static::$conditions))
            static::$conditions[] = "AND $column $operator %s";
        else
            static::$conditions[] = "$column $operator %s";

        static::$conditionValues[] = $value;

        return new static();
    }

    public function orWhere($column, $operator, $value = null)
    {
        if ($value === null)
        {
            $value = $operator;
            $operator = '=';
        }

        if (!in_array($operator, ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN']))
            return null;

        if (!empty(static::$conditions))
            static::$conditions[] = "OR $column $operator %s";
        else
            static::$conditions[] = "$column $operator %s";

        static::$conditionValues[] = $value;

        return new static();
    }

    public function orderBy($column, $direction = 'ASC')
    {
        static::$orderBy = " ORDER BY $column " . strtoupper($direction);
        return new static();
    }

    public function limit($limit, $offset = 0)
    {
        static::$limit = " LIMIT $limit OFFSET $offset";
        return new static();
    }

    public function get()
    {
        $sql = "SELECT * FROM " . static::getTable() . static::applyWhereClause() . static::$orderBy . static::$limit;
        $results = static::query($sql, static::$conditionValues);

        // Reset the query state after execution
        static::resetQueryState();

        return $results;
    }

    public function first()
    {
        $sql = "SELECT * FROM " . static::getTable() . static::applyWhereClause() . static::$orderBy . " LIMIT 1";
        $result = static::queryRow($sql, static::$conditionValues);

        // Reset the query state after execution
        static::resetQueryState();

        return $result;
    }

    public function paginate($perPage, $page = 1)
    {
        $offset = ($page - 1) * $perPage;

        $total = static::queryValue("SELECT COUNT(*) FROM " . static::getTable() . $this->applyWhereClause(),self::$conditionValues);
        $data = static::query("SELECT * FROM " . static::getTable() . $this->applyWhereClause() . self::$orderBy. " LIMIT {$perPage} OFFSET {$offset}",self::$conditionValues);

        self::resetQueryState();

        return [
            'data' => $data,
            'pagination' => [
                'total' => (int)$total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }

    protected function applyWhereClause(): string
    {
        if (empty(static::$conditions)) {
            return '';
        }

        $whereClause = ' WHERE ';

        // Ensure proper AND/OR placement
        $formattedConditions = [];
        foreach (static::$conditions as $index => $condition)
        {
            // Remove leading AND/OR for the first condition
            $formattedConditions[] = ($index === 0) ? preg_replace('/^(AND|OR) /', '', $condition) : $condition;
        }

        return $whereClause . implode(' ', $formattedConditions);
    }

    protected static function getTable(string $tableName = ''): string
    {
        $tableName = !empty($tableName) ? $tableName : static::$table;
        return static::getDB()->prefix . Environment::TablePreFix . $tableName;
    }

    protected static function getDB()
    {
        global $wpdb;
        return $wpdb;
    }

    protected static function query($sql, $params = [], $class = '')
    {
        $results = static::getDB()->get_results(empty($params) ? $sql : static::getDB()->prepare($sql, ...$params));

        return $results ? static::hydrate($results,true,$class) : null;
    }

    protected static function queryRow($sql, $params = [], $class = '')
    {
        $row = static::getDB()->get_row(empty($params) ? $sql : static::getDB()->prepare($sql, ...$params));

        return $row ? static::hydrate($row,false,$class) : null;
    }

    protected static function queryValue($sql, $params = [])
    {
        return static::getDB()->get_var(empty($params) ? $sql : static::getDB()->prepare($sql, ...$params));
    }

    protected static function resetQueryState()
    {
        static::$conditions = [];
        static::$orderBy = '';
        static::$limit = '';
        static::$conditionValues = [];
    }

    public static function hydrate($data, bool $multiple = false, string $class = '')
    {
        $class = !empty($class) ? $class : static::class;

        if ($multiple)
        {
            $instances = [];

            foreach ($data as $object)
            {
                $instance = new $class();

                foreach ($object as $key => $value)
                    $instance->$key = $value;

                $instances[] = $instance;
            }

            return $instances;
        }

        else
        {
            $instance = new $class();

            foreach ($data as $key => $value)
                $instance->$key = $value;

            return $instance;
        }
    }
}
