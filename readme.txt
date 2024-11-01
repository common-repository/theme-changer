=== Theme Changer ===
Contributors: momen2009
Tags: theme,change,get,parameter,demo
Requires at least: 3.0
Tested up to: 4.8
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy theme change in the get parameter. Can be used theme to demo.
this to be a per-session only change, and one that everyone (all visitors) can use.

== Description ==

Easy theme change in the get parameter. Can be used theme to demo.
this to be a per-session only change, and one that everyone (all visitors) can use.

= How to use =
I just enter the following URL. It's easy.

http://wordpress_install_domain/?theme_changer=theme_folder_name

== Installation ==

1. Upload the `theme-changer` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.

== Changelog ==

= 1.0 =
* First release.

= 1.1 =
* While logging in to the management screen, we prepared a watermark for changing the theme to the lower left.

= 1.2 =
* You can attach a password to the Theme Changer. e.g. http://wordpress_install_domain/?theme_changer=theme_folder_name&theme_changer_password=input_password

= 1.3 =
* Fixed a problem where the theme returns to the original when transitioning to the lower page. Once the password matched, the modified theme was applied even to the lower page.