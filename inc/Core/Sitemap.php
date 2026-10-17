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
 * Lit le sitemap XML du site et construit une arborescence hiérarchique
 * utilisée pour enrichir llms-full.txt.
 */
class Sitemap
{

    /**
     * URLs déjà visitées, pour éviter les boucles infinies sur les sitemaps imbriqués.
     *
     * @var array
     */
    protected $seen = array();

    /**
     * Retourne l'arbre construit à partir de l'URL de sitemap fournie.
     *
     * @param  string $sitemap_url URL du sitemap XML (index ou simple) à analyser.
     * @return array
     */
    public function get_tree( $sitemap_url )
    {
        $urls = $this->collect_urls($sitemap_url);
        return $this->build_tree($urls);
    }

    /**
     * Parcourt récursivement les sitemaps (index + sous-sitemaps) et
     * retourne la liste plate des URLs de contenu.
     *
     * @param  string $url   URL du sitemap à lire.
     * @param  int    $depth Profondeur de récursion courante (protection anti-boucle).
     * @return array
     */
    protected function collect_urls( $url, $depth = 0 )
    {
        if ($depth > 3 || in_array($url, $this->seen, true) || empty($url) ) {
            return array();
        }
        $this->seen[] = $url;

        $response = wp_remote_get($url, array( 'timeout' => 15 ));
        if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response) ) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body) ) {
            return array();
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        libxml_clear_errors();
        if (! $xml ) {
            return array();
        }

        $urls = array();

        if (isset($xml->sitemap) ) {
            foreach ( $xml->sitemap as $sitemap ) {
                $loc = (string) $sitemap->loc;
                if ($loc ) {
                    $urls = array_merge($urls, $this->collect_urls($loc, $depth + 1));
                }
            }
        } elseif (isset($xml->url) ) {
            foreach ( $xml->url as $u ) {
                $loc = (string) $u->loc;
                if ($loc ) {
                    $urls[] = $loc;
                }
            }
        }

        return $urls;
    }

    /**
     * Construit un arbre associatif à partir des segments de chemin des URLs.
     *
     * @param  array $urls Liste plate des URLs collectées depuis le sitemap.
     * @return array
     */
    protected function build_tree( $urls )
    {
        $tree = array();

        foreach ( $urls as $url ) {
            $path     = (string) wp_parse_url($url, PHP_URL_PATH);
            $path     = trim($path, '/');
            $segments = ( '' === $path ) ? array() : explode('/', $path);

            if (empty($segments) ) {
                continue;
            }

            $node  = &$tree;
            $count = count($segments);
            foreach ( $segments as $i => $seg ) {
                if (! isset($node[ $seg ]) ) {
                    $node[ $seg ] = array(
                     '__url'      => ( $i === $count - 1 ) ? $url : '',
                     '__children' => array(),
                    );
                } elseif ($i === $count - 1 ) {
                    $node[ $seg ]['__url'] = $url;
                }
                $node = &$node[ $seg ]['__children'];
            }
            unset($node);
        }

        return $tree;
    }

    /**
     * Rend l'arbre sous forme de liste imbriquée au format Markdown.
     *
     * @param  array $tree  Arbre généré par build_tree().
     * @param  int   $depth Profondeur courante, utilisée pour l'indentation.
     * @return string
     */
    public function render_tree( $tree, $depth = 0 )
    {
        $out = '';
        foreach ( $tree as $label => $node ) {
            $indent = str_repeat('  ', $depth);
            $title  = ucwords(str_replace(array( '-', '_' ), ' ', $label));

            if (! empty($node['__url']) ) {
                $out .= $indent . '- [' . $title . '](' . esc_url_raw($node['__url']) . ")\n";
            } else {
                $out .= $indent . '- ' . $title . "\n";
            }

            if (! empty($node['__children']) ) {
                $out .= $this->render_tree($node['__children'], $depth + 1);
            }
        }
        return $out;
    }
}
