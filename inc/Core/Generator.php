<?php
/**
 * Plugin 
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
 * Génère le contenu de llms.txt / llms-full.txt selon https://llmstxt.org/
 */
class Generator
{

    /**
     * Réglages courants du plugin.
     *
     * @var array
     */
    protected $settings;

    /**
     * Charge les réglages du plugin.
     */
    public function __construct()
    {
        $this->settings = Options::get_settings();
    }

    /**
     * Construit le contenu complet du fichier demandé.
     *
     * @param  bool $full true = llms-full.txt (contenu complet + arbre sitemap), false = llms.txt (liens + résumé).
     * @return string
     */
    public function generate( $full = false )
    {
        $lines = array();

        // H1 : titre du site.
        $lines[] = '# ' . $this->clean_line($this->settings['site_title']);
        $lines[] = '';

        // Citation : résumé court (obligatoire selon la spec llmstxt.org).
        if (! empty($this->settings['site_summary']) ) {
            $lines[] = '> ' . $this->clean_line($this->settings['site_summary']);
            $lines[] = '';
        }

        // Paragraphe libre optionnel.
        if (! empty($this->settings['site_description']) ) {
            $lines[] = trim($this->settings['site_description']);
            $lines[] = '';
        }

        // Arborescence du sitemap : uniquement pour llms-full.txt et si activée.
        if ($full && ! empty($this->settings['use_sitemap']) && ! empty($this->settings['sitemap_url']) ) {
            $sitemap = new Sitemap();
            $tree    = $sitemap->get_tree($this->settings['sitemap_url']);
            if (! empty($tree) ) {
                $lines[] = '## ' . __('Arborescence du site', 'llms4gg');
                $lines[] = '';
                $lines[] = rtrim($sitemap->render_tree($tree));
                $lines[] = '';
            }
        }

        // Sections statiques enregistrées en base, groupées par nom de section.
        $target   = $full ? 'llms_full' : 'llms';
        $sections = Sections::get_sections(array( 'target' => $target ));
        $grouped  = array();
        foreach ( $sections as $section ) {
            $grouped[ $section->section ][] = $section;
        }
        foreach ( $grouped as $group => $items ) {
            $lines[] = '## ' . $this->clean_line($group);
            $lines[] = '';
            foreach ( $items as $item ) {
                $lines[] = $this->format_link($item->title, $item->url, $item->description);
            }
            $lines[] = '';
        }

        // Contenu WordPress ajouté automatiquement (posts, pages, CPT sélectionnés).
        $post_types = ! empty($this->settings['include_post_types']) ? (array) $this->settings['include_post_types'] : array();
        foreach ( $post_types as $pt ) {
            $pt_obj = get_post_type_object($pt);
            if (! $pt_obj ) {
                continue;
            }

            $posts = get_posts(
                array(
                'post_type'   => $pt,
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby'     => 'menu_order title',
                'order'       => 'ASC',
                )
            );
            if (empty($posts) ) {
                continue;
            }

            $lines[] = '## ' . $this->clean_line($pt_obj->labels->name);
            $lines[] = '';

            foreach ( $posts as $post ) {
                $permalink = get_permalink($post);

                if ($full ) {
                    $lines[] = '- [' . $this->clean_line($post->post_title) . '](' . esc_url_raw($permalink) . ')';
                    $lines[] = '';
                    $lines[] = $this->post_to_text($post);
                    $lines[] = '';
                    $lines[] = '---';
                    $lines[] = '';
                } else {
                    $excerpt = $this->get_excerpt($post);
                    $lines[] = $this->format_link($post->post_title, $permalink, $excerpt);
                }
            }

            if (! $full ) {
                $lines[] = '';
            }
        }

        return trim(implode("\n", $lines)) . "\n";
    }

    /**
     * Formate une ligne de lien au format llmstxt.org : `- [titre](url): description`.
     *
     * @param  string $title       Titre du lien.
     * @param  string $url         URL cible.
     * @param  string $description Description associée, facultative.
     * @return string
     */
    protected function format_link( $title, $url, $description )
    {
        $title       = $this->clean_line($title);
        $description = $this->clean_line($description);

        $line = '- [' . $title . '](' . esc_url_raw($url) . ')';
        if (! empty($description) ) {
            $line .= ': ' . $description;
        }
        return $line;
    }

    /**
     * Retourne l'extrait d'un article (existant ou généré depuis le contenu).
     *
     * @param  WP_Post $post Article concerné.
     * @return string
     */
    protected function get_excerpt( $post )
    {
        if (has_excerpt($post) ) {
            return $post->post_excerpt;
        }
        return wp_trim_words(wp_strip_all_tags($post->post_content), intval($this->settings['excerpt_length']));
    }

    /**
     * Convertit le contenu HTML d'un article en texte brut pour llms-full.txt.
     *
     * @param  WP_Post $post Article concerné.
     * @return string
     */
    protected function post_to_text( $post )
    {
        $content = apply_filters('llms4gg_the_content', $post->post_content);
        $content = str_replace(array( '</p>', '</h1>', '</h2>', '</h3>', '</h4>', '<br />', '<br>' ), "\n", $content);
        $text    = wp_strip_all_tags($content);
        $text    = preg_replace("/\n{3,}/", "\n\n", $text);
        return trim($text);
    }

    /**
     * Nettoie un texte pour l'insérer sur une seule ligne (titre, description).
     *
     * @param  string $text Texte à nettoyer.
     * @return string
     */
    protected function clean_line( $text )
    {
        $text = wp_strip_all_tags((string) $text);
        $text = str_replace(array( "\r\n", "\r", "\n" ), ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}