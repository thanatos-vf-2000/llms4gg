<?php
/**
 * Static Sections Management Screen (list + form).
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

use LLMS4GG\Core\Sections;

if (! defined('ABSPATH') ) {
    exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple sélection en lecture de la section à éditer, aucune donnée n'est modifiée.
$llms4gg_edit_id      = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$llms4gg_edit_section = $llms4gg_edit_id ? Sections::get_section($llms4gg_edit_id) : null;
$llms4gg_sections     = Sections::get_sections();
?>
<div class="wrap llms4gg-wrap">
    <h1><?php esc_html_e('Sections statiques', 'llms4gg'); ?></h1>
    <p><?php esc_html_e("Ces sections sont ajoutées manuellement dans llms.txt et/ou llms-full.txt : elles sont regroupées par nom de section (ex : Docs, Optional), puis triées par ordre d'apparition.", 'llms4gg'); ?></p>

    <?php require LLMS4GG_PATH . 'admin/views/section-form.php'; ?>

    <h2><?php esc_html_e('Sections enregistrées', 'llms4gg'); ?></h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Ordre', 'llms4gg'); ?></th>
                <th><?php esc_html_e('Section', 'llms4gg'); ?></th>
                <th><?php esc_html_e('Titre', 'llms4gg'); ?></th>
                <th><?php esc_html_e('URL', 'llms4gg'); ?></th>
                <th><?php esc_html_e('Description', 'llms4gg'); ?></th>
                <th><?php esc_html_e('Fichier cible', 'llms4gg'); ?></th>
                <th><?php esc_html_e('Actions', 'llms4gg'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($llms4gg_sections) ) : ?>
            <tr><td colspan="7"><?php esc_html_e('Aucune section pour le moment.', 'llms4gg'); ?></td></tr>
            <?php
        else :
            foreach ( $llms4gg_sections as $llms4gg_section_row ) :
                ?>
            <tr>
                <td><?php echo esc_html($llms4gg_section_row->menu_order); ?></td>
                <td><?php echo esc_html($llms4gg_section_row->section); ?></td>
                <td><?php echo esc_html($llms4gg_section_row->title); ?></td>
                <td><a href="<?php echo esc_url($llms4gg_section_row->url); ?>" target="_blank" rel="noopener"><?php echo esc_html($llms4gg_section_row->url); ?></a></td>
                <td><?php echo esc_html(wp_trim_words($llms4gg_section_row->description, 15)); ?></td>
                <td><?php echo esc_html($llms4gg_section_row->target_file); ?></td>
                <td>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=llms4gg-sections&edit=' . $llms4gg_section_row->id)); ?>"><?php esc_html_e('Modifier', 'llms4gg'); ?></a>
                    |
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=llms4gg_delete_section&id=' . $llms4gg_section_row->id), 'llms4gg_delete_section')); ?>" onclick="return confirm('<?php echo esc_js(__('Supprimer cette section ?', 'llms4gg')); ?>');"><?php esc_html_e('Supprimer', 'llms4gg'); ?></a>
                </td>
            </tr>
                <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>
