<?php

/**
 * Plugin DB.
 * 
 * PHP version 8
 *
 * @category  PHP
 * @package   LLMS4GG
 * @author    Franck VANHOUCKE <ct4gg@ginkgos.net>
 * @copyright 2025-2026 Copyright 2026, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.0.0 GIT:https://github.com/thanatos-vf-2000/llms4gg
 * @link      https://ginkgos.net
 */

namespace LLMS4GG\Core;

class DB
{



    /**
     * List Of Mysql Table
     *
     * @var array
     */
    public static $db_table = array(
    'sections',
    );

    /**
     * List of array exist
     *
     * @var array
     */
    public static $db_table_exist = array();

    /**
     * Table name Structure in Database
     *
     * @var string
     */
    public static $tbl_name = '[prefix]llms4gg_[name]';

    /**
     * Get WordPress Table Collate
     *
     * @return mixed
     */
    public static function charset_collate()
    {
        global $wpdb;
        return $wpdb->get_charset_collate();
    }

    /**
     * Get WordPress Table Prefix
     */
    public static function prefix()
    {
        global $wpdb;
        return $wpdb->prefix;
    }

    /**
     * Get Table name
     *
     * @param  $tbl
     * @return mixed
     */
    public static function get_table_name( $tbl )
    {
        return str_ireplace(array( '[prefix]', '[name]' ), array( self::prefix(), $tbl ), self::$tbl_name);
    }

    /**
     * Check Exist Table in Database
     *
     * @param  $tbl_name
     * @return bool
     */
    public static function exist_table( $tbl_name )
    {
        global $wpdb;
        return ( $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tbl_name))) === $tbl_name );
    }

    /**
     * Table List
     *
     * @param  string $export
     * @param  array  $except
     * @return array|null|string
     */
    public static function table( $export = 'all', $except = array() )
    {

        // Create Empty Object
        $list = array();

        // Convert except String to array
        if (is_string($except) ) {
            $except = array( $except );
        }

        // Check Except List
        $mysql_list_table = array_diff(self::$db_table, $except);

        // Get List
        foreach ( $mysql_list_table as $tbl ) {
            $table_name = self::get_table_name($tbl);

            if ('all' === $export ) {
                if (self::exist_table($table_name) ) {
                    $list[ $tbl ] = $table_name;
                }
            } else {
                $list[ $tbl ] = $table_name;
            }
        }

        // Export Data
        return ( 'all' === $export ? $list : ( array_key_exists($export, $list) ? $list[ $export ] : null ) );
    }

}