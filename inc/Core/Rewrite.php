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

/**
 * Gère les URLs virtuelles /llms.txt et /llms-full.txt.
 */
class Rewrite
{

    /**
     * Enregistre les règles de réécriture pour /llms.txt et /llms-full.txt.
     *
     * @return void
     */
    public static function add_rules()
    {
        add_rewrite_rule('^llms\.txt$', 'index.php?llms4gg_file=llms', 'top');
        add_rewrite_rule('^llms-full\.txt$', 'index.php?llms4gg_file=llms_full', 'top');
    }

    /**
     * Déclare la query var utilisée par les règles de réécriture.
     *
     * @param  array $vars Liste des query vars publiques.
     * @return array
     */
    public static function add_query_vars( $vars )
    {
        $vars[] = 'llms4gg_file';
        return $vars;
    }

    /**
     * Sert le contenu généré (ou mis en cache) pour la requête courante.
     *
     * @return void
     */
    public static function serve_file()
    {
        $file = get_query_var('llms4gg_file');
        if (! $file ) {
            return;
        }

        $full          = ( 'llms_full' === $file );
        $transient_key = $full ? 'llms4gg_cache_llms_full' : 'llms4gg_cache_llms';
        $content       = get_transient($transient_key);

        if (false === $content ) {
            $generator = new Generator();
            $content   = $generator->generate($full);

            $settings = Options::get_settings();
            $ttl      = max(0, intval($settings['cache_ttl'])) * HOUR_IN_SECONDS;
            if ($ttl > 0 ) {
                set_transient($transient_key, $content, $ttl);
            }
        }

        nocache_headers();
        header('Content-Type: text/plain; charset=utf-8');
     echo $content; // phpcs:ignore -- contenu texte déjà nettoyé lors de la génération.
        exit;
    }

    /**
     * Vide le cache des fichiers llms.txt et llms-full.txt.
     *
     * @return void
     */
    public static function clear_cache()
    {
        delete_transient('llms4gg_cache_llms');
        delete_transient('llms4gg_cache_llms_full');
    }
}
