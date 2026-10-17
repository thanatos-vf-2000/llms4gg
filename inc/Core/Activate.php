<?php
/**
 * Activate function
 *
 * PHP version 8
 *
 * @category  PHP
 * @package   LLMS4GGPlugin
 * @author    Franck VANHOUCKE <contact@ginkgos.net>
 * @copyright 2025-2026 Copyright 2026, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.0.0 GIT:https://github.com/thanatos-vf-2000/llms4gg
 * @link      https://ginkgos.net
 */

namespace LLMS4GG\Core;

class Activate
{


    /**
     * Activation : crée la table, initialise les réglages par défaut, enregistre les règles de réécriture.
     *
     * @param  bool $network_wide Activation sur tout le réseau (multisite).
     * @return void
     */
    public static function activate( $network_wide = false )
    {
        Install::install($network_wide);

        // Réglages par défaut uniquement à la première activation (on ne réécrase pas une config existante).
        if (! get_option(LLMS4GG_NAME . '_plugin') ) {
            foreach ( Options::defaults() as $name => $value ) {
                Options::set_option($name, $value);
            }
        }
        Options::set_option('version', LLMS4GG_VERSION);
        Options::set_option('install', 1);

        Rewrite::add_rules();
        flush_rewrite_rules();
    }
}
