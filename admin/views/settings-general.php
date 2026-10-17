<?php
/**
 * Plugin General Settings Screen.
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

use LLMS4GG\Core\Options;

if (! defined('ABSPATH') ) {
    exit;
}

$llms4gg_settings   = Options::get_settings();
$llms4gg_post_types = get_post_types(array( 'public' => true ), 'objects');
?>
<div class="wrap llms4gg-wrap">
    <h1><?php esc_html_e('LLMS4GG — Réglages généraux', 'llms4gg'); ?></h1>
    <p><?php esc_html_e('Configurez la génération des fichiers llms.txt et llms-full.txt conformément au standard llmstxt.org.', 'llms4gg'); ?></p>

    <form method="post" action="options.php">
        <?php settings_fields('llms4gg_settings_group'); ?>
        <input type="hidden" name="llms4gg_plugin[_form]" value="1">
        <table class="form-table">
            <tr>
                <th><label for="site_title"><?php esc_html_e('Titre du site (H1)', 'llms4gg'); ?></label></th>
                <td><input type="text" class="regular-text" id="site_title" name="llms4gg_plugin[site_title]" value="<?php echo esc_attr($llms4gg_settings['site_title']); ?>"></td>
            </tr>
            <tr>
                <th><label for="site_summary"><?php esc_html_e('Résumé (citation)', 'llms4gg'); ?></label></th>
                <td>
                    <input type="text" class="large-text" id="site_summary" name="llms4gg_plugin[site_summary]" value="<?php echo esc_attr($llms4gg_settings['site_summary']); ?>">
                    <p class="description"><?php esc_html_e('Courte phrase affichée en citation juste sous le titre (blockquote).', 'llms4gg'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="site_description"><?php esc_html_e('Description complémentaire', 'llms4gg'); ?></label></th>
                <td><textarea class="large-text" rows="4" id="site_description" name="llms4gg_plugin[site_description]"><?php echo esc_textarea($llms4gg_settings['site_description']); ?></textarea></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Contenus inclus automatiquement', 'llms4gg'); ?></th>
                <td>
                    <?php foreach ( $llms4gg_post_types as $llms4gg_pt ) : ?>
                        <label style="display:block;margin-bottom:4px;">
                            <input type="checkbox" name="llms4gg_plugin[include_post_types][]" value="<?php echo esc_attr($llms4gg_pt->name); ?>" <?php checked(in_array($llms4gg_pt->name, (array) $llms4gg_settings['include_post_types'], true)); ?>>
                        <?php echo esc_html($llms4gg_pt->labels->name); ?> <code><?php echo esc_html($llms4gg_pt->name); ?></code>
                        </label>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e('Ces contenus sont listés automatiquement : liens + extrait dans llms.txt, contenu complet dans llms-full.txt.', 'llms4gg'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="excerpt_length"><?php esc_html_e('Longueur des extraits (mots)', 'llms4gg'); ?></label></th>
                <td><input type="number" min="5" id="excerpt_length" name="llms4gg_plugin[excerpt_length]" value="<?php echo esc_attr($llms4gg_settings['excerpt_length']); ?>"></td>
            </tr>
            <tr>
                <th><?php esc_html_e("Arborescence d'accès via sitemap", 'llms4gg'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="llms4gg_plugin[use_sitemap]" value="1" <?php checked(! empty($llms4gg_settings['use_sitemap'])); ?>>
                        <?php esc_html_e("Activer la génération de l'arbre d'accès à partir du sitemap (llms-full.txt uniquement)", 'llms4gg'); ?>
                    </label>
                    <p>
                        <label for="sitemap_url"><?php esc_html_e('URL du sitemap', 'llms4gg'); ?></label><br>
                        <input type="url" class="large-text" id="sitemap_url" name="llms4gg_plugin[sitemap_url]" value="<?php echo esc_attr($llms4gg_settings['sitemap_url']); ?>">
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="cache_ttl"><?php esc_html_e('Durée du cache (heures, 0 = désactivé)', 'llms4gg'); ?></label></th>
                <td><input type="number" min="0" id="cache_ttl" name="llms4gg_plugin[cache_ttl]" value="<?php echo esc_attr($llms4gg_settings['cache_ttl']); ?>"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>

    <hr>
    <h2><?php esc_html_e('URLs publiques', 'llms4gg'); ?></h2>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/llms.txt')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/llms.txt')); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/llms-full.txt')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/llms-full.txt')); ?></a></li>
    </ul>
    <p class="description"><?php esc_html_e('En cas de 404 sur ces URLs : allez dans Réglages > Permaliens puis cliquez sur Enregistrer pour actualiser les règles de réécriture.', 'llms4gg'); ?></p>
</div>
