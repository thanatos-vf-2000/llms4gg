<img src="https://github.com/thanatos-vf-2000/llms4gg/blob/master/assets/img/banner772x250.png" width="400">

# llms4gg

[![GitHub version](https://badge.fury.io/gh/thanatos-vf-2000%2Fllms4gg.svg)](https://badge.fury.io/gh/thanatos-vf-2000%2Fllms4gg)
[![License](http://poser.pugx.org/thanatos-vf-2000/llms4gg/license)](https://packagist.org/packages/thanatos-vf-2000/llms4gg)
[![PHP Version Require](http://poser.pugx.org/thanatos-vf-2000/llms4gg/require/php)](https://packagist.org/packages/thanatos-vf-2000/llms4gg)

Contributors: vanhoucke
Tags: llms.txt, llm, ai, seo
Requires at least: 7.0
Tested up to: 7.2
Requires PHP: 8.3
Stable tag: 1.0.0
License: GPLv2 or later

Dynamically generates the llms.txt and llms-full.txt files according to the llmstxt.org standard.

## Description

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
* Preview page in the admin panel before publicationhelp for everyone. We all seek to hide or personalize options or displays; that's why your feedback is important to me. Thank you for helping me make WordPress the best blogging platform in the world.

## Active Contributors
<li>[Franck VANHOUCKE](https://profiles.wordpress.org/vanhoucke/) (Development)</li>

## Screenshots

1. Logo,
2. Parameters,
3. LLMS add section.


<img src="https://github.com/thanatos-vf-2000/llms4gg/blob/master/assets/img/screenshot-1.png" width="30%"></img> <img src="https://github.com/thanatos-vf-2000/llms4gg/blob/master/assets/img/screenshot-2.png" width="30%"></img> <img src="https://github.com/thanatos-vf-2000/llms4gg/blob/master/assets/img/screenshot-3.png" width="30%"> 

## Installation

1. Upload the `llms4gg` folder to `/wp-content/plugins/`.
2. Activate the plugin from the WordPress Plugins menu.
3. Go to Settings > Permalinks and click Save to enable the rewrite rules for /llms.txt and /llms-full.txt.
4. Configure the plugin from the LLMS4GG menu.

## new features
If you want a new feature, you can contact me by email at contact@ginkgos.net 

## Changelog

See [changelog.txt](https://plugins.svn.wordpress.org/llms4gg/trunk/changelog.txt) for older changelog

## Upgrade Notice

Please contact me by email or through the contact form on the site [ginkgos.net] (https://ginkgos.net/). Please do not post on the forums.