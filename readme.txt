=== Menu Logic ===
Contributors: shazdeh, paulkirspuu, lulech23
Plugin Name: Menu Logic
Tags: menu, nav-menu, navigation, navigation menu, conditional tags, context, filter
Requires at least: 5.4
Tested up to: 6.0.1
Stable tag: 0.5.1

Control individual menu items' visibility based on your desired condition.

== Description ==

Using this plugin you can use WordPress <a href="http://codex.wordpress.org/Conditional_Tags">Conditional Tags</a> to enable or disable menu items on the front-end. It works like "Widget Logic" but for menu items.

*PLEASE NOTE*: The conditions are PHP codes that are evaluated using PHP's "<a href="https://www.php.net/manual/en/function.eval.php">eval</a>" function, meaning anyone who has access to the Menu manager in WordPress can execute any code. Be mindful of who has access to the Menu manager.

= Usage =
You must insert conditional tags in the "Menu Logic" box in the menu item options form. You can use any PHP or WordPress functions to build virtually any condition for menu items. For example, to hide the menu item on homepage you can set the visibility to:
<pre><code>!is_home()</code></pre>

Show the menu only to logged-in users: 
<pre><code>is_user_logged_in()</code></pre>

Show the menu only to guest visitors: 
<pre><code>!is_user_logged_in()</code></pre>

To show the menu item based on <a href="https://wordpress.org/support/article/roles-and-capabilities/">user capability</a>:
<pre><code>current_user_can('manage_options')</code></pre>


== Installation ==

1. Upload the `menu-logic` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Locate the "Menus" item on the "Appearance" menu
4. While editing your menu item, you will see another option, "Menu Logic" - input your logic and that's it


== Changelog ==

= 0.5.1 =
* Merged code structure from CXL fork while preserving original functionality
* Renamed to Menu Logic for consistency with similar plugins
* Added logic check when updating menu items - WordPress will display an error upon saving malformatted code

= 0.5 =
* Fix error handler in php8

= 0.4 =
* Improve error handling

= 0.3.9 =
* Better compatibility with outdated themes that don't support WP 4.5

= 0.3.8 =
* Updated to use the new API in WP 4.5

= 0.3.7 =
* Fix JS error on Menus manager

= 0.3.6 =
* Revamp of how fields are added to WP UI, should prevent conflict with other plugins and themes

= 0.3.5 =
* Possible fatal error prevention

= 0.3.4 =
* Fix compatibility with Menu Icons plugin

= 0.3.3 =
* Fix menu item edit screen styles

= 0.3.2 =
* Fix Customizer wiping out the Visibility value upon save

= 0.3.1 =
* Got rid of PHP notices in the admin area
* Updated Walker_Nav_Menu_Edit

= 0.3 =
* Gantry 4.0 compatibility
* implemented singleton pattern
* added the remove_visibility_meta function which cleans up the meta datas for deleted menu items

= 0.2.1 =
* Fixed a minor bug where unnecessary database rows in postmeta table would be created upon save
* fixed a bug concerning using quotes in conditions

= 0.2 =
* Compatibility with latest WordPress release
* Fixed a minor bug where conditions would also execute on the admin area