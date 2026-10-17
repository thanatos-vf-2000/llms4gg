<?php
/**
 * Form for adding or modifying a static section.
 *
 * Expects the $edit_section (object|null) variable as input, provided by
 * the settings-sections.php view that includes this file.
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

if (! defined('ABSPATH') ) {
    exit;
}
?>
<div class="llms4gg-form-box">
    <h2><?php echo $edit_section ? esc_html__('Modifier la section', 'llms4gg') : esc_html__('Ajouter une section', 'llms4gg'); ?></h2>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="llms4gg_save_section">
        <input type="hidden" name="id" value="<?php echo esc_attr($edit_section->id ?? 0); ?>">
        <?php wp_nonce_field('llms4gg_save_section', 'llms4gg_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="section"><?php esc_html_e('Nom de la section (ex : Docs, Optional)', 'llms4gg'); ?></label></th>
                <td><input required type="text" id="section" name="section" class="regular-text" value="<?php echo esc_attr($edit_section->section ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="title"><?php esc_html_e('Titre du lien', 'llms4gg'); ?></label></th>
                <td><input required type="text" id="title" name="title" class="regular-text" value="<?php echo esc_attr($edit_section->title ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="url"><?php esc_html_e('URL', 'llms4gg'); ?></label></th>
                <td><input type="url" id="url" name="url" class="regular-text" value="<?php echo esc_attr($edit_section->url ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="description"><?php esc_html_e('Description', 'llms4gg'); ?></label></th>
                <td><textarea id="description" name="description" class="large-text" rows="3"><?php echo esc_textarea($edit_section->description ?? ''); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="target_file"><?php esc_html_e('Fichier cible', 'llms4gg'); ?></label></th>
                <td>
                    <?php $llms4gg_tf = $edit_section->target_file ?? 'both'; ?>
                    <select id="target_file" name="target_file">
                        <option value="both" <?php selected($llms4gg_tf, 'both'); ?>><?php esc_html_e('llms.txt + llms-full.txt', 'llms4gg'); ?></option>
                        <option value="llms" <?php selected($llms4gg_tf, 'llms'); ?>><?php esc_html_e('llms.txt uniquement', 'llms4gg'); ?></option>
                        <option value="llms_full" <?php selected($llms4gg_tf, 'llms_full'); ?>><?php esc_html_e('llms-full.txt uniquement', 'llms4gg'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="menu_order"><?php esc_html_e("Ordre d'apparition", 'llms4gg'); ?></label></th>
                <td><input type="number" id="menu_order" name="menu_order" value="<?php echo esc_attr($edit_section->menu_order ?? 0); ?>"></td>
            </tr>
        </table>
        <?php submit_button($edit_section ? __('Mettre à jour', 'llms4gg') : __('Ajouter', 'llms4gg')); ?>
    </form>
</div>
