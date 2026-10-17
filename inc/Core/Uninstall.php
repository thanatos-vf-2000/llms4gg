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

class Uninstall
{



    /**
     * Uninstall : supprime table, options et cache sur le site (ou sur tous les sites en multisite).
     */
    public static function uninstall()
    {
        global $wpdb;

        if (is_multisite() ) {
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog($blog_id);
                self::site_removal();
                restore_current_blog();
            }
        } else {
            self::site_removal();
        }
    }

    /**
     * Removes database options, transients & tables of the current site
     */
    public static function site_removal()
    {
        global $wpdb;

        // Drop the tables (seules les tables existantes sont retournées).
        foreach ( DB::table() as $table_name ) {
            $wpdb->query('DROP TABLE IF EXISTS `' . esc_sql($table_name) . '`');
        }

        delete_option(LLMS4GG_NAME . '_plugin');
        Rewrite::clear_cache();
    }
}
