<?php
/**
 * Class Desactive
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

class Desactivate
{


    /**
     * Désactivation : nettoie les règles de réécriture et le cache.
     * La suppression des données (table + options) est réservée à la désinstallation (uninstall.php).
     *
     * @return void
     */
    public static function desactivate()
    {
        Rewrite::clear_cache();
        flush_rewrite_rules();
    }
}
