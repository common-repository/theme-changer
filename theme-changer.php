<?php
/*
Plugin Name: Theme Changer
Plugin URI: http://www.elegants.biz/theme-changer.php
Description: Easy theme change in the get parameter. this to be a per-session only change, and one that everyone (all visitors) can use. I just enter the following URL. It's easy. e.g. http://wordpress_install_domain/?theme_changer=theme_folder_name
Version: 1.3
Author: momen2009
Author URI: http://www.elegants.biz/
License: GPLv2 or later
*/

/*  Copyright 2017 木綿の優雅な一日 (email : momen.yutaka@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$theme_changer_theme;

function add_meta_query_vars( $public_query_vars ) {
    if(is_admin()) return;
    $public_query_vars[] = "theme_changer";
    return $public_query_vars;
}

function theme_changer(){
    if(is_admin()) return;
    global $wpdb;
    if (!isset($_SESSION)) {
        session_start();
    }

    global $theme_changer_theme;
    $theme_changer_password = get_option("theme_changer_password");
    if($theme_changer_password != false){
        $now_theme = wp_get_theme();
        $theme_changer_theme = $now_theme -> get_stylesheet();

        if(isset($_SESSION["theme_changer_password"]) && !isset($_GET["theme_changer_password"])){
            if($_SESSION["theme_changer_password"] != $theme_changer_password) return;
        }else{
            if($theme_changer_password != $wpdb->escape($_GET["theme_changer_password"])){
                return;
            }else{
                $_SESSION["theme_changer_password"] = $theme_changer_password;
            }
        }
    }

    $theme_changer = $wpdb->escape($_GET["theme_changer"]);
    if(isset($theme_changer) && $theme_changer != ""){
        $theme_changer = $wpdb->escape($_GET["theme_changer"]);
    }elseif(isset($_SESSION["theme_changer"])){
        $theme_changer = $_SESSION["theme_changer"];
    }
    if($value = exist_search_theme($theme_changer)){
        $theme_changer_theme = $value -> get_stylesheet();
        $_SESSION["theme_changer"]=$theme_changer;
    }
}

function exist_search_theme($stylesheet){
    foreach(get_themes() as $value){
        if($value->get_stylesheet() == $stylesheet) return $value;
    }
    return false;
}

function my_theme_switcher($theme){
    global $theme_changer_theme;
    if(exist_search_theme($theme_changer_theme)){
        $overrideTheme = wp_get_theme($theme_changer_theme);
        if ($overrideTheme->exists()) {
            return $overrideTheme['Template'];
        } else {
            return $theme;
        }
    }else{
        return $theme;
    }
}

if(!is_admin()){
    add_filter("query_vars","add_meta_query_vars");
    add_filter("setup_theme","theme_changer");
    add_filter('stylesheet', 'my_theme_switcher');
    add_filter('template', 'my_theme_switcher');
}

add_action('init', 'theme_changer_logged_in');

function theme_changer_logged_in(){
    if (is_user_logged_in()) {
        add_action('wp_footer', 'theme_changer_footer' );
    }
}

function theme_changer_footer() {
    global $theme_changer_theme;
    $output .= "<style>\r\n#theme_changer{z-index:1000 !important;position:fixed;padding:10px;bottom:10px;left:10px;opacity:0.2;}#theme_changer label {color: #333 !important;display: block !important;font-weight: 800 !important;margin-bottom: 0.5em !important;font-family: 'Hiragino Kaku Gothic Pro', Meiryo, sans-serif !important;font-size: 16px !important;}#theme_changer select {font-weight:normal !important;font-size: 16px !important;color: #333 !important;border: 1px solid #bbb !important;-webkit-border-radius: 3px !important;border-radius: 3px !important;height: 3em !important;max-width: 100% !important;}#theme_changer p {font-size: 9px;}</style><script>jQuery(document).ready(function(){jQuery('#theme_changer select').change(function() {if (jQuery(this).val() != '') {";
    $output .= "var kvp2; kvp2 = insertParameter(document.location.search.substr(1).split('&'),'theme_changer',jQuery(this).val());";
    $theme_changer_password = get_option("theme_changer_password");
    if($theme_changer_password != false){
        $output .= "kvp2 = insertParameter(kvp2.split('&'),'theme_changer_password','" . $theme_changer_password . "');";
    }
    $output .= "document.location.search = kvp2;}});});";
    $output .= "function insertParameter(kvp, key, value) {key = encodeURI(key);value = encodeURI(value);var i = kvp.length;var x;while (i--) {x = kvp[i].split('=');if (x[0] == key) {x[1] = value;kvp[i] = x.join('=');break;}}if (i < 0) {kvp[kvp.length] = [key, value].join('=');}return kvp.join('&');}";
    $output .= " jQuery('body').append('";
    $output .= "<div id=\"theme_changer\"><label for=\"theme_changer_select\">Theme Changer</label><select id=\"theme_changer_select\">";
    
    foreach(wp_get_themes() as $value){
        $output .= "<option value=\"";
        $output .= $value -> get_stylesheet();
        $output .= "\"";
        if($value -> get_stylesheet() == $theme_changer_theme){
            $output .= " selected";
        } 
        $output .= ">";
        $output .= $value -> Name;
        $output .= "</option>";
    }
    $output .= "<select><p>This will only be displayed if you are logged in.</p></div>');</script>";
    echo $output;
}

add_action('admin_menu','theme_changer_menu');

function theme_changer_menu() {
	add_options_page( 'Theme Changer Options', 'Theme Changer Options', 'manage_options', 'theme-changer-options', 'theme_changer_options' );
}

function theme_changer_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

    if (isset($_POST['theme_changer_password'])) {
        update_option('theme_changer_password', wp_unslash($_POST['theme_changer_password']));
    }
?>
<div class="wrap">
<h1>Theme Changer Options</h1>
<?php
if(isset($_POST['theme_changer_password'])) {
    echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>Settings saved.</strong></p></div>';
}
?>
<form method="post" action="">
<table class="form-table">
    <tr>
        <th scope="row"><label for="theme_changer_password">Password</label></th>
        <td><input name="theme_changer_password" type="text" id="theme_changer_password" value="<?php form_option('theme_changer_password'); ?>" class="regular-text" /><p class="description" id="theme-changer-password-description">You can attach a password to the Theme Changer. e.g. http://wordpress_install_domain/?theme_changer=theme_folder_name&amp;<strong>theme_changer_password=input_password<strong></p></td>
    </tr>
</table>
<?php submit_button(); ?>
</form>
</div>
<?php
}
?>