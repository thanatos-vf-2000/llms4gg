<?php
/**
 * Plugin main class: hooks into WordPress.
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

use LLMS4GG\Admin\Admin;

if (! defined('ABSPATH') ) {
    exit;
}

class Plugin
{

    /**
     * Enregistre tous les hooks WordPress du plugin.
     *
     * @return void
     */
    public function run()
    {
        //add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action('init', array( Rewrite::class, 'add_rules' ));
        add_filter('query_vars', array( Rewrite::class, 'add_query_vars' ));
        add_action('template_redirect', array( Rewrite::class, 'serve_file' ));

        // Invalidation du cache quand le contenu ou les réglages changent.
        add_action('save_post', array( Rewrite::class, 'clear_cache' ));
        add_action('deleted_post', array( Rewrite::class, 'clear_cache' ));
        add_action('trashed_post', array( Rewrite::class, 'clear_cache' ));
        add_action('update_option_' . LLMS4GG_NAME . '_plugin', array( Rewrite::class, 'clear_cache' ));

        if (is_admin() ) {
            new Admin();
        }
    }

    /**
     * Charge les traductions du plugin.
     *
     * @return void
     */
    public function load_textdomain()
    {
        //load_plugin_textdomain( 'llms4gg', false, dirname( plugin_basename( LLMS4GG_FILE ) ) . '/languages' );
    }
}
