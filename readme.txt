=== tinySource ===

Contributors: ideag
Tags: text, source, custom-post-type
Requires at least: 3.0.0
Tested up to: 3.0.1
Stable tag: 0.2.2

This plugin enables users to easily note the source of republished text. EARLY DEVELOPMENT VERSION. DO NOT USE IN PRODUCTION ENVIRONMENT.

== Description ==

This plugin enables users to easily note the source of republished text. It adds a custom post type 'Sources' and a metabox to choose from various sources in post editing window.

ATTENTION: This is still an early development version (aka ALPHA), thus use with your own risk. It has the basic functionality, but not many configuration options and some bugs are probably present.

Basic functions:

* Allows to create list of possible sources using Custom Post Types functionality;
* Creates a meta-box in post editor screen to choose from list of available sources;
* Customizable template - you can use your own html and shorttags %title%, %title_attr%, %text%, %img%, %img_h%, %img_w% and %link%;
* You can use template tag `<?php tinysource_show(); ?>` to insert plugin output;

TO DO:

* More customization options
* Automatic insertion after post text
* Localization

== Installation ==

1. Unzip `tinysource.zip` file
1. Upload `tinysource` directory to the `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php tinysource_show(); ?>` in your templates


== Changelog ==

= 0.2 = 
* changed custom field from `tinysource_source` to `_tinysource_source`
* fixed one typo


= 0.1 =
* initial *ALPHA* release


