=== Plugin Name ===
Contributors: adiian
Donate link: 
Tags: Subscribe, Subscribe Here, Widget, Rss, Feedburner, Subscribe By Email, Email
Requires at least: 2.3.1
Tested up to: 2.9.1
Stable tag: 1.0

Subscribe Here displays a visible plugin widget in the sidebar with Subscribe by Rss & Subscribe by Email(through Feedburner) options.

== Description ==

Features:

* Show "Subscribe by RSS" and "Subscribe by email"
* Configurable apearance through CSS
* Feedburner Feed Id configurable through widget panel

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the  files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the widget to the sidebar from Appearance > Widgets and configure the Feedburner feed Id. Without it the Subscribe by Email is not shown.

== Frequently Asked Questions ==

= Why Subscribe by Email does not show up? =

You need to create a FeedBurner account and to add your feed there and to configure the Subscribe Here Widget with you Feedburner Feed Id.

= How can I customize the display? =

Just go to plugin directory and edit the css file.

== Screenshots ==

1. Widget Preview
2. Widget Admin

== Changelog ==

= 1.0 =
	* subscribe-here-widget.php(created) - actions added: plugins_loaded(register_sidebar_widget & register_widget_control), wp_head
	* subscribe-here-widget.css(created) - widget stylesheet

== Upgrade Notice ==

= 1.0 =
This is the first plugin version. No Upgrade Notice yet.