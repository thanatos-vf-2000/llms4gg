<?php
/**
 * Plugin.
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

class Install
{



    /**
     * Install
     *
     * @param $network_wide
     */
    public static function install( $network_wide )
    {
        // Create MySQL Table
        self::create_table($network_wide);

        
    }

    /**
     * Adding new MYSQL Table in Activation Plugin
     *
     * @param $network_wide
     */
    public static function create_table( $network_wide )
    {
        global $wpdb;

        if (is_multisite() && $network_wide ) {
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog($blog_id);
                self::table_sql();
                restore_current_blog();
            }
        } else {
            self::table_sql();
        }
    }

    /**
     * Create Database Table
     */
    public static function table_sql()
    {
        // Load dbDelta WordPress
        self::load_db_delta();

        // Charset Collate
        $collate = DB::charset_collate();

        // Sections table
        $create_table = ( '
			CREATE TABLE ' . DB::get_table_name('sections') . " (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				section varchar(191) NOT NULL DEFAULT '',
				title varchar(255) NOT NULL DEFAULT '',
				description text NULL,
				url varchar(500) NULL,
				target_file varchar(20) NOT NULL DEFAULT 'both',
				menu_order int(11) NOT NULL DEFAULT 0,
				created_at datetime NULL,
				updated_at datetime NULL,
				PRIMARY KEY  (id),
				KEY section (section(191))
					) {$collate}" );
        dbDelta($create_table);

    }

    /**
     * Load WordPress dbDelta Function
     */
    public static function load_db_delta()
    {
        if (! function_exists('dbDelta') ) {
            include ABSPATH . 'wp-admin/includes/upgrade.php';
        }
    }
}
