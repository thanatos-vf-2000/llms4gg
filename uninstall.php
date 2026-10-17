<?php
/**
 * Clean uninstallation of the plugin: removes the table, settings, and cache.
 *
 * PHP version 8
 *
 * @category  PHP
 * @package   LLMS4GGPlugin
 * @author    Franck VANHOUCKE <ct4gg@ginkgos.net>
 * @copyright 2025-2026 Copyright 2026, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.0.0 GIT:https://github.com/thanatos-vf-2000/llms4gg
 * @link      https://ginkgos.net
 */

if (! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

define('LLMS4GG_NAME', 'llms4gg');
define('LLMS4GG_PATH', plugin_dir_path(__FILE__));

if (file_exists(__DIR__ . '/vendor/autoload.php') ) {
    include_once __DIR__ . '/vendor/autoload.php';
} else {
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

LLMS4GG\Core\Uninstall::uninstall();
