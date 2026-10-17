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

class Options
{



    /**
     * Class instance.
     *
     * @since  0.1.0
     * @access private
     * @var    $instance Class instance.
     */
    private static $instance;

    /**
     * A static option variable.
     *
     * @since  0.1.0
     * @access private
     * @var    mixed $db_options
     */
    private static $db_options;

    /**
     * A static option variable.
     *
     * @since  0.1.0
     * @access private
     * @var    mixed $db_options
     */
    private static $db_options_no_defaults;

    /**
     * Initiator
     *
     * @since 0.1.0
     */
    public function register()
    {
        if (! isset(self::$instance) ) {
            self::$instance = new self();
            if (empty(self::$db_options) ) {
                self::refresh();
            }
        }
    }

    /**
     * LoadPHPConfig - load default config for plugin
     *
     * @since  0.1.0
     * @return array()
     */
    private static function load_php_config( $path )
    {

        if (! file_exists($path) ) {
            return array();
        }

        $content = include $path;

        return $content;
    }

    /**
     * Set default option values
     *
     * @since  0.1.0
     * @return default values of the .
     */
    public static function defaults()
    {
        // Defaults list of options.
        $defaults_sys = array(
        'version' => '0.0.0',
        't'       => time(),
        );
        $defaults_app = array(
        'site_title'         => get_bloginfo('name'),
        'site_summary'       => get_bloginfo('description'),
        'site_description'   => '',
        'include_post_types' => array( 'post', 'page' ),
        'excerpt_length'     => 30,
        'use_sitemap'        => 0,
        'sitemap_url'        => trailingslashit(home_url()) . 'wp-sitemap.xml',
        'cache_ttl'          => 12, // heures.
        );
        // Surcharge optionnelle via assets/options.php.
        $defaults_file = self::load_php_config(LLMS4GG_PATH . 'assets/options.php');
        if (! is_array($defaults_file) ) {
            $defaults_file = array();
        }

        return array_merge($defaults_sys, $defaults_app, $defaults_file);
    }

    /**
     * Get options from static array()
     *
     * @since  0.1.0
     * @return array    Return array of options.
     */
    public static function get_options()
    {
        if (empty(self::$db_options) ) {
            self::refresh();
        }
        return self::$db_options;
    }

    /**
     * Get specific option
     *
     * @since  0.1.0
     * @return array    Return array of options.
     */
    public static function get_option( $opt )
    {
        if (empty(self::$db_options) ) {
            self::refresh();
        }
        return self::$db_options[ $opt ] ?? null;
    }

    /**
     * Update  static option array.
     *
     * @since 0.1.0
     */
    public static function refresh()
    {
        self::$db_options = wp_parse_args(
            (array) self::get_db_options(),
            self::defaults()
        );
    }

    /**
     * Get options from static array() from database
     *
     * @since  0.1.0
     * @return array    Return array of options from database.
     */
    public static function get_db_options()
    {
        self::$db_options_no_defaults = get_option(LLMS4GG_NAME . '_plugin');
        return self::$db_options_no_defaults;
    }

    /**
     * Set option to database
     *
     * @since  0.1.0
     * @return true/false
     */
    public static function set_option( $name, $value )
    {
        if (empty(self::$db_options) ) {
            self::refresh();
        }
        self::$db_options[ $name ] = $value;
        self::$db_options['t']     = time();
        update_option(LLMS4GG_NAME . '_plugin', self::$db_options);
        return true;
    }

    /**
     * Delete option to database
     *
     * @since  0.1.0
     * @return true/false
     */
    public static function del_option( $name )
    {
        if (empty(self::$db_options) ) {
            self::refresh();
        }
        unset(self::$db_options[ $name ]);
        self::$db_options['t'] = time();
        update_option(LLMS4GG_NAME . '_plugin', self::$db_options);
        return true;
    }

    /**
     * Réglages courants (options enregistrées fusionnées avec les valeurs par défaut).
     *
     * @since  0.1.0
     * @return array
     */
    public static function get_settings()
    {
        return wp_parse_args((array) self::get_db_options(), self::defaults());
    }
}
