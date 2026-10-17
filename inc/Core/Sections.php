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

if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Accès à la table des sections statiques.
 */
class Sections
{

    /**
     * Retourne le nom complet (avec préfixe) de la table des sections.
     *
     * @return string
     */
    public static function table()
    {
        return DB::get_table_name('sections');
    }

    /**
     * Récupère les sections, éventuellement filtrées par fichier cible.
     *
     * @param array $args {
     *                    Arguments de filtrage facultatifs.
     *
     * @type   string $target  Fichier cible : llms ou llms_full.
     * @type   string $section Nom du groupe de section à filtrer.
     * }
     * @return array
     */
    public static function get_sections( $args = array() )
    {
        global $wpdb;
        $table    = self::table();
        $defaults = array(
        'target'  => '',
        'section' => '',
        );
        $args     = wp_parse_args($args, $defaults);
        $where    = array( '1=1' );

        // Chaque fragment est déjà échappé individuellement via $wpdb->prepare().
        if (! empty($args['target']) ) {
            $where[] = $wpdb->prepare("( target_file = %s OR target_file = 'both' )", $args['target']);
        }
        if (! empty($args['section']) ) {
            $where[] = $wpdb->prepare('section = %s', $args['section']);
        }

        $cache_key = 'llms4gg_sections_' . md5($table . implode('|', $where));
        $results   = wp_cache_get($cache_key, 'llms4gg');

        if (false === $results ) {
            // $table est un identifiant de table interne construit depuis le préfixe $wpdb, jamais issu d'une entrée utilisateur ; chaque condition de $where est déjà passée par $wpdb->prepare().
            $sql     = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where) . ' ORDER BY section ASC, menu_order ASC, id ASC';
            $results = $wpdb->get_results($sql); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared -- table personnalisée sans équivalent dans l'API WordPress ; $sql est déjà sûre (voir commentaire ci-dessus) et le résultat est mis en cache via wp_cache_get()/wp_cache_set().
            wp_cache_set($cache_key, $results, 'llms4gg', HOUR_IN_SECONDS);
        }

        return $results;
    }

    /**
     * Récupère une section par son identifiant.
     *
     * @param  int $id Identifiant de la section.
     * @return object|null
     */
    public static function get_section( $id )
    {
        global $wpdb;
        $table = self::table();
        $id    = intval($id);

        $cache_key = 'llms4gg_section_' . $id;
        $section   = wp_cache_get($cache_key, 'llms4gg');

        if (false === $section ) {
            // $table est un identifiant de table interne construit depuis le préfixe $wpdb, jamais issu d'une entrée utilisateur ; %d est préparé via $wpdb->prepare().
            $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table personnalisée sans équivalent dans l'API WordPress ; $table est sûre (voir commentaire ci-dessus), résultat mis en cache via wp_cache_get()/wp_cache_set().
            wp_cache_set($cache_key, $section, 'llms4gg', HOUR_IN_SECONDS);
        }

        return $section;
    }

    /**
     * Retourne la liste distincte des noms de groupes de sections.
     *
     * @return array
     */
    public static function get_groups()
    {
        global $wpdb;
        $table = self::table();

        $groups = wp_cache_get('llms4gg_section_groups', 'llms4gg');

        if (false === $groups ) {
            // $table est un identifiant de table interne construit depuis le préfixe $wpdb, jamais issu d'une entrée utilisateur ; aucune valeur utilisateur dans cette requête.
            $groups = $wpdb->get_col("SELECT DISTINCT section FROM {$table} ORDER BY section ASC"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table personnalisée sans équivalent dans l'API WordPress ; $table est sûre (voir commentaire ci-dessus), résultat mis en cache via wp_cache_get()/wp_cache_set().
            wp_cache_set('llms4gg_section_groups', $groups, 'llms4gg', HOUR_IN_SECONDS);
        }

        return $groups;
    }

    /**
     * Enregistre (crée ou met à jour) une section statique.
     *
     * @param  array $data Données brutes issues du formulaire ($_POST).
     * @param  int   $id   Identifiant de la section à mettre à jour, 0 pour une création.
     * @return int Identifiant de la section enregistrée.
     */
    public static function save_section( $data, $id = 0 )
    {
        global $wpdb;
        $table = self::table();

        $target_file = isset($data['target_file']) ? $data['target_file'] : 'both';
        if (! in_array($target_file, array( 'llms', 'llms_full', 'both' ), true) ) {
            $target_file = 'both';
        }

        $fields = array(
        'section'     => isset($data['section']) ? sanitize_text_field($data['section']) : '',
        'title'       => isset($data['title']) ? sanitize_text_field($data['title']) : '',
        'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
        'url'         => isset($data['url']) ? esc_url_raw($data['url']) : '',
        'target_file' => $target_file,
        'menu_order'  => isset($data['menu_order']) ? intval($data['menu_order']) : 0,
        'updated_at'  => current_time('mysql'),
        );

        $id = intval($id);

        if ($id > 0 ) {
         // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table personnalisée du plugin, sans équivalent dans l'API WordPress ; écriture (pas de lecture à mettre en cache), le cache de lecture est vidé juste après via flush_cache().
            $wpdb->update($table, $fields, array( 'id' => $id ));
            self::flush_cache();
            return $id;
        }

        $fields['created_at'] = current_time('mysql');
     // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table personnalisée du plugin, sans équivalent dans l'API WordPress ; écriture (pas de lecture à mettre en cache), le cache de lecture est vidé juste après via flush_cache().
        $wpdb->insert($table, $fields);
        self::flush_cache();
        return $wpdb->insert_id;
    }

    /**
     * Supprime une section statique.
     *
     * @param  int $id Identifiant de la section à supprimer.
     * @return int|false Nombre de lignes supprimées, ou false en cas d'erreur.
     */
    public static function delete_section( $id )
    {
        global $wpdb;
        $table = self::table();
     // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table personnalisée du plugin, sans équivalent dans l'API WordPress ; écriture (pas de lecture à mettre en cache), le cache de lecture est vidé juste après via flush_cache().
        $result = $wpdb->delete($table, array( 'id' => intval($id) ));
        self::flush_cache();
        return $result;
    }

    /**
     * Vide le cache interne des sections (groupes, listes, éléments individuels).
     *
     * @return void
     */
    protected static function flush_cache()
    {
        wp_cache_flush_group('llms4gg');
    }
}
