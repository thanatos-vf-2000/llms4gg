<?php
/**
 * Plugin administration interface.
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

namespace LLMS4GG\Admin;

use LLMS4GG\Core\Options;
use LLMS4GG\Core\Rewrite;
use LLMS4GG\Core\Sections;

if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Interface d'administration : réglages, CRUD des sections statiques, aperçu.
 */
class Admin
{

    /**
     * Enregistre les hooks d'administration.
     */
    public function __construct()
    {
        add_action('admin_menu', array( $this, 'menu' ));
        add_action('admin_init', array( $this, 'register_settings' ));
        add_action('admin_post_llms4gg_save_section', array( $this, 'handle_save_section' ));
        add_action('admin_post_llms4gg_delete_section', array( $this, 'handle_delete_section' ));
        add_action('admin_post_llms4gg_purge_cache', array( $this, 'handle_purge_cache' ));
        add_action('admin_enqueue_scripts', array( $this, 'assets' ));
        add_action('admin_notices', array( $this, 'notices' ));
    }

    /**
     * Charge les feuilles de style et scripts d'administration du plugin.
     *
     * @param  string $hook Identifiant de l'écran d'administration courant.
     * @return void
     */
    public function assets( $hook )
    {
        if (false === strpos((string) $hook, 'llms4gg') ) {
            return;
        }
        wp_enqueue_style('llms4gg-admin', LLMS4GG_URL . 'admin/css/admin.css', array(), LLMS4GG_VERSION);
        wp_enqueue_script('llms4gg-admin', LLMS4GG_URL . 'admin/js/admin.js', array( 'jquery' ), LLMS4GG_VERSION, true);
    }

    /**
     * Déclare les pages de menu du plugin.
     *
     * @return void
     */
    public function menu()
    {
        add_menu_page(__('LLMS4GG', 'llms4gg'), 'LLMS4GG', 'manage_options', 'llms4gg', array( $this, 'render_general' ), 'dashicons-media-text', 80);
        add_submenu_page('llms4gg', __('Réglages généraux', 'llms4gg'), __('Réglages', 'llms4gg'), 'manage_options', 'llms4gg', array( $this, 'render_general' ));
        add_submenu_page('llms4gg', __('Sections statiques', 'llms4gg'), __('Sections statiques', 'llms4gg'), 'manage_options', 'llms4gg-sections', array( $this, 'render_sections' ));
        add_submenu_page('llms4gg', __('Aperçu', 'llms4gg'), __('Aperçu', 'llms4gg'), 'manage_options', 'llms4gg-preview', array( $this, 'render_preview' ));
    }

    /**
     * Enregistre le réglage principal du plugin (options WordPress).
     *
     * @return void
     */
    public function register_settings()
    {
        register_setting('llms4gg_settings_group', LLMS4GG_NAME . '_plugin', array( $this, 'sanitize_settings' ));
    }

    /**
     * Nettoie et valide les réglages soumis via le formulaire des options.
     *
     * @param  array $input Valeurs brutes soumises par le formulaire.
     * @return array
     */
    public function sanitize_settings( $input )
    {
        $input = (array) $input;

        // Mise à jour programmatique (hors formulaire) : on ne touche pas aux valeurs.
        if (empty($input['_form']) ) {
            return $input;
        }

        $defaults = Options::defaults();
        $output   = (array) Options::get_db_options(); // conserve version, install, t...

        $output['site_title']         = sanitize_text_field($input['site_title'] ?? $defaults['site_title']);
        $output['site_summary']       = sanitize_text_field($input['site_summary'] ?? '');
        $output['site_description']   = sanitize_textarea_field($input['site_description'] ?? '');
        $output['include_post_types'] = isset($input['include_post_types']) ? array_map('sanitize_key', (array) $input['include_post_types']) : array();
        $output['excerpt_length']     = max(5, intval($input['excerpt_length'] ?? 30));
        $output['use_sitemap']        = ! empty($input['use_sitemap']) ? 1 : 0;
        $output['sitemap_url']        = esc_url_raw($input['sitemap_url'] ?? $defaults['sitemap_url']);
        $output['cache_ttl']          = max(0, intval($input['cache_ttl'] ?? 12));

        Rewrite::clear_cache();

        return $output;
    }

    /**
     * Affiche les messages de confirmation après une action CRUD ou un vidage de cache.
     *
     * @return void
     */
    public function notices()
    {
     // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule d'un paramètre d'URL pour l'affichage, aucune donnée n'est traitée ni modifiée ici.
        if (! isset($_GET['page']) || false === strpos(sanitize_text_field(wp_unslash($_GET['page'])), 'llms4gg') ) {
            return;
        }
     // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule d'un paramètre d'URL pour l'affichage, aucune donnée n'est traitée ni modifiée ici.
        if (! empty($_GET['llms4gg_msg']) ) {
         // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule d'un paramètre d'URL pour l'affichage, aucune donnée n'est traitée ni modifiée ici.
            $msg      = sanitize_text_field(wp_unslash($_GET['llms4gg_msg']));
            $messages = array(
            'saved'   => __('Section enregistrée.', 'llms4gg'),
            'deleted' => __('Section supprimée.', 'llms4gg'),
            'purged'  => __('Cache vidé.', 'llms4gg'),
            );
            if (isset($messages[ $msg ]) ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[ $msg ]) . '</p></div>';
            }
        }
    }

    /**
     * Traite la soumission du formulaire d'ajout/modification d'une section statique.
     *
     * @return void
     */
    public function handle_save_section()
    {
        if (! current_user_can('manage_options') || ! isset($_POST['llms4gg_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['llms4gg_nonce'])), 'llms4gg_save_section') ) {
            wp_die(esc_html__('Action non autorisée.', 'llms4gg'));
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        Sections::save_section(wp_unslash($_POST), $id); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce déjà vérifié ci-dessus via llms4gg_nonce.
        Rewrite::clear_cache();
        wp_safe_redirect(admin_url('admin.php?page=llms4gg-sections&llms4gg_msg=saved'));
        exit;
    }

    /**
     * Traite la suppression d'une section statique.
     *
     * @return void
     */
    public function handle_delete_section()
    {
        if (! current_user_can('manage_options') || ! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'llms4gg_delete_section') ) {
            wp_die(esc_html__('Action non autorisée.', 'llms4gg'));
        }
        Sections::delete_section(isset($_GET['id']) ? intval($_GET['id']) : 0); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce déjà vérifié ci-dessus.
        Rewrite::clear_cache();
        wp_safe_redirect(admin_url('admin.php?page=llms4gg-sections&llms4gg_msg=deleted'));
        exit;
    }

    /**
     * Vide le cache des fichiers générés, à la demande de l'administrateur.
     *
     * @return void
     */
    public function handle_purge_cache()
    {
        if (! current_user_can('manage_options') || ! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'llms4gg_purge_cache') ) {
            wp_die(esc_html__('Action non autorisée.', 'llms4gg'));
        }
        Rewrite::clear_cache();
        wp_safe_redirect(admin_url('admin.php?page=llms4gg-preview&llms4gg_msg=purged'));
        exit;
    }

    /**
     * Affiche l'écran des réglages généraux.
     *
     * @return void
     */
    public function render_general()
    {
        include LLMS4GG_PATH . 'admin/views/settings-general.php';
    }

    /**
     * Affiche l'écran de gestion des sections statiques.
     *
     * @return void
     */
    public function render_sections()
    {
        include LLMS4GG_PATH . 'admin/views/settings-sections.php';
    }

    /**
     * Affiche l'écran d'aperçu des fichiers générés.
     *
     * @return void
     */
    public function render_preview()
    {
        include LLMS4GG_PATH . 'admin/views/preview.php';
    }
}
