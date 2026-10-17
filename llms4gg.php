<?php
/*
Plugin Name: llms4gg
Plugin URI: https://ginkgos.net/plugin/llms4gg/
Description: LLMS For GinkGos - Plugin / LLMS Tools for WordPress.
Version: 1.0.0
Requires at least: 7.0
Requires PHP: 8.3
Author: Franck VANHOUCKE
Author URI: https://ginkgos.net/
Network: true
License: GPLv2 or later
Domain Path: /languages
Text Domain: llms4gg

Copyright 2025-2026 Franck VANHOUCKE

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Make sure we don't expose any info if called directly
if (!function_exists('add_action') ) {
    die('Hi there!  I\'m just a plugin, not much I can do when called directly.');
}

defined('ABSPATH') || die('Hey, what are you doing here? You silly human!');

if (file_exists(__DIR__ . '/vendor/autoload.php') ) {
    include_once __DIR__ . '/vendor/autoload.php';
} else {
    // Repli PSR-4 si le dossier vendor/ est absent.
    spl_autoload_register(
        function ( $class ) {
            $prefix = 'LLMS4GG\\';
            if (0 !== strpos($class, $prefix) ) {
                return;
            }
            $file = __DIR__ . '/inc/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file) ) {
                include_once $file;
            }
        }
    );
}

define('LLMS4GG_VERSION', '1.0.0');
define('LLMS4GG_NAME', 'llms4gg');
define('LLMS4GG_FILE', __FILE__);
define('LLMS4GG_PATH', plugin_dir_path(__FILE__));
define('LLMS4GG_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation
 *
 * @param  bool $network_wide Activation sur tout le réseau.
 * @return void
 */
function llms4gg_activate_plugin( $network_wide = false )
{
    LLMS4GG\Core\Activate::activate($network_wide);
}
register_activation_hook(__FILE__, 'llms4gg_activate_plugin');

/**
 * The code that runs during plugin deactivation
 *
 * @return void
 */
function llms4gg_desactivate_plugin()
{
    LLMS4GG\Core\Desactivate::desactivate();
}
register_deactivation_hook(__FILE__, 'llms4gg_desactivate_plugin');

/**
 * The code that run for Core executing
 * 
 * @return void
 */
function llms4gg_run()
{
    $plugin = new LLMS4GG\Core\Plugin();
    $plugin->run();
}
llms4gg_run();
