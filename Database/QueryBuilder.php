<?php

namespace Database;

class QueryBuilder
{
    /**
     * SLQ String result for query built
     *
     * @var string
     */
    public static $sql = '';

    /**
     * Self instance
     *
     * @var QueryBuilder
     */
    public static $instance = null;

    /**
     * Initial part for the built SQL
     *
     * @var string
     */
    public static $prefix = '';

    /**
     * Statements added to the query
     *
     * @var array
     */
    public static $where = [];

    /**
     * Add limit or offset to the query
     *
     * @var array
     */
    public static $control = ['', ''];

    /**
     * Build a select statement
     *
     * @param string $table
     * @param array|string $cols
     * @return self::$instance
     */
    public static function select(String $table, $cols = null) 
    {
        self::$instance = new self();

        if($cols) {
            $cols = is_array($cols) ? implode(',', $cols) : $cols;
            self::$prefix = "SELECT $cols FROM $table";
            
            return self::$instance;
        }

        self::$prefix = "SELECT * FROM $table";

        return self::$instance;
    }

    /**
     * Set a where and add an AND if where is set
     *
     * @param string $field
     * @param string $operator
     * @param string|mixed $value
     * @return void
     */
    public static function where(String $field = '', String $operator = '=', $value = null)
    {
        if (!array_key_exists(0, self::$where)) {
            self::$where[0] = "WHERE $field $operator $value";
        } else {
            self::$where[] = "AND $field $operator $value";
        }

        return self::$instance;
    }

    /**
     * Set an OR
     *
     * @param string $field
     * @param string $operator
     * @param string|mixed $value
     * @return void
     */
    public static function or_where(String $field = '', String $operator = '=', $value = null)
    {
        self::$where[] = "OR $field $operator $value";

        return self::$instance;
    }

    /**
     * Set a like
     *
     * @param string $a field
     * @param string $b field
     * @return self::$instance
     */
    public static function like($a, $b)
    {
        self::$where[] = trim("$a LIKE $b");
        return self::$instance;
    }

    /**
     * Add an AND
     *
     * @param mixed $a
     * @return void
     */
    public static function and($a = null)
    {
        self::$where[] = trim("AND $a");
        return self::$instance;
    }

    /**
     * Add an OR
     *
     * @param [type] $a
     * @return self::$instance
     */
    public static function or($a = null)
    {
        self::$where[] = trim("OR $a");
        return self::$instance;
    }

    /**
     * Add an IN
     *
     * @param array|string $a
     * @return self::$instance
     */
    public static function in($a)
    {
        $in_options = is_array($a) ? implode(',', $a) : $a;
        self::$where[] = "IN ($in_options)";
        return self::$instance;
    }

    /**
     * Add a NOT
     *
     * @param string $a
     * @return self::$instance
    */ 
    public static function not($a = null)
    {
        self::$where[] = trim("NOT $a");
        return self::$instance;
    }

    /**
     * Set LIMIT to sql
     *
     * @param integer $limit
     * @return self::$instance
     */
    public static function limit(integer $limit)
    {
        self::$control[0] = "LIMIT $limit";
        return self::$instance;
    }

    /**
     * Set an OFFSET to the sql
     *
     * @param [type] $offset
     * @return self::$instance
     */
    public static function offset($offset)
    {
        self::$control[1] = "OFFSET $offset";
        return self::$instance;
    }

    /**
     * Get the built sql
     *
     * @return string
     */
    public static function get_sql()
    {
        self::$sql = self::$prefix
            . implode(' ', self::$where)
            . ' '
            . self::$control[0]
            . ' '
            . self::$control[1];
        preg_replace('/  /', ' ', self::$sql);
        return trim(self::$sql);
    }
}
