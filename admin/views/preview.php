<?php
/**
 * Preview screen for the llms.txt / llms-full.txt files.
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

use LLMS4GG\Core\Generator;

if (! defined('ABSPATH') ) {
    exit;
}

$llms4gg_generator = new Generator();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple bascule d'affichage en lecture seule, aucune donnée n'est modifiée.
$llms4gg_which   = ( isset($_GET['file']) && 'full' === $_GET['file'] ) ? 'full' : 'llms';
$llms4gg_content = $llms4gg_generator->generate('full' === $llms4gg_which);
?>
<div class="wrap llms4gg-wrap">
    <h1><?php esc_html_e('Aperçu', 'llms4gg'); ?></h1>
    <p>
        <a class="button <?php echo 'llms' === $llms4gg_which ? 'button-primary' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=llms4gg-preview&file=llms')); ?>">llms.txt</a>
        <a class="button <?php echo 'full' === $llms4gg_which ? 'button-primary' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=llms4gg-preview&file=full')); ?>">llms-full.txt</a>
        <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=llms4gg_purge_cache'), 'llms4gg_purge_cache')); ?>"><?php esc_html_e('Vider le cache', 'llms4gg'); ?></a>
    </p>
    <textarea readonly rows="30" style="width:100%;font-family:monospace;"><?php echo esc_textarea($llms4gg_content); ?></textarea>
</div>
