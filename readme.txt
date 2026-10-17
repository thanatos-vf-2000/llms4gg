=== llms4gg ===
Contributors: vanhoucke
Tags: llms.txt, llm, ai, seo
Requires at least: 7.0
Tested up to: 7.2
Requires PHP: 8.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dynamically generates the llms.txt and llms-full.txt files according to the llmstxt.org standard.

== Description ==

LLMS4GG automatically generates the following files at the site's root URL:

* /llms.txt — links + short descriptions (standard llmstxt.org format)
* /llms-full.txt — full content, with an optional directory structure based on the sitemap

Features:

* Static sections stored in the database (section, title, description, URL, display order, target file)
* Automatic and configurable addition of published posts, pages, and custom content types
* Generation of a navigation tree for llms-full.txt based on the site’s XML sitemap (optional)
* Caching via transients with a configurable duration, automatically invalidated upon content publication
* Preview page in the admin panel before publication

= Active Contributors =
<li>[Franck VANHOUCKE](https://profiles.wordpress.org/vanhoucke/) (Development)</li>

== Screenshots ==

1. Logo,
2. Parameters,
3. LLMS add section.


== Installation ==

1. Upload the `llms4gg` folder to `/wp-content/plugins/`.
2. Activate the plugin from the WordPress Plugins menu.
3. Go to Settings > Permalinks and click Save to enable the rewrite rules for /llms.txt and /llms-full.txt.
4. Configure the plugin from the LLMS4GG menu.

== Changelog ==

= 1.0.0 (202610) =
*Release Date - 17 October 2026*

* Initial version

See [changelog.txt](https://plugins.svn.wordpress.org/llms4gg/trunk/changelog.txt) for older changelog

== Upgrade Notice ==

Please contact me by email or through the contact form on the site [ginkgos.net] (https://ginkgos.net/). Please do not post on the forums.
