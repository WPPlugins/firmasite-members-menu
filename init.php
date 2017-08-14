<?php
/**
 * Plugin Name: FirmaSite Members Menu
 * Plugin URI:  http://theme.firmasite.com/
 * Description: You can select alternative menus to theme locations for showing only to the logged in members.
 * Author:      Ünsal Korkmaz
 * Author URI:  http://unsalkorkmaz.com
 * Version:     1.0
 * Text Domain: firmasite-members-menu
 * Domain Path: /languages/
 */
 
 __("FirmaSite Members Menu", "firmasite-members-menu");
 __("You can select alternative menus to theme locations for showing only to the logged in members.", "firmasite-members-menu");
 
/*
 * We create alternative menu for each menu registered
 */
add_action('after_setup_theme', "firmasite_members_menu_setup",999 );
function firmasite_members_menu_setup() {
	$menu_locations = get_registered_nav_menus();
	foreach ($menu_locations as $menu_id => $menu_name) {
	  register_nav_menus(array(
		'members_only_' . $menu_id => $menu_name . __(' (Members Only)', "firmasite-members-menu"),
	  ));	
	}
}

/*
 * We use alternative versions for logged in users
 */
add_filter( "theme_mod_nav_menu_locations", "firmasite_members_menu_customize_menu_access");
function firmasite_members_menu_customize_menu_access($array) {
	if(is_admin()) return $array;
	global $blog_id;
	// Check for more capabilities:
	// http://codex.wordpress.org/Roles_and_Capabilities
	// If user is cant even "read", dont show "main_menu"
	if ( current_user_can_for_blog( $blog_id, 'read' ) ) {
		
		$array_keys = array_keys($array);
		$members_only_array_keys = preg_grep("/^members_only_*/", $array_keys);
		if(!empty($members_only_array_keys) && is_array($members_only_array_keys))
		foreach($members_only_array_keys as $members_only){
			$real_menu = preg_split('/members_only_/', $members_only);
			if (!empty($array[$members_only])){
				$array[$real_menu[1]] = $array[$members_only];
			}
		}
	}
	return $array;
}


// Translation
function firmasite_members_menu_translate_init() {
  load_plugin_textdomain( 'firmasite-members-menu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action('plugins_loaded', 'firmasite_members_menu_translate_init');


