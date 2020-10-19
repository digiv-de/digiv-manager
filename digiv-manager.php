<?php
/**
 * Plugin Name: DigiV Manager
 * Plugin URI: https://digiv.de/tools/dm
 * Version: 0.1
 * Author: Maximilian Kroth
 * Author URI: https://tmaex.com
 */

if (!defined( 'WPINC' )) exit('only useful as wordpress plugin');

/*
 * hook settings
 */

function plugin_setting_callback($args) {
	echo '<input type="'.$args['type'].'" name="'.$args['id'].'" value="'.get_option($args['id']).'">';
}

function digiv_settings_render() {
	echo '<div class="wrap">';
	echo '<h1>'.esc_html( get_admin_page_title() ).'</h1><br>';
	
	echo '<form action="options.php" method="post">';

		settings_fields('digiv_settings');
		do_settings_sections('digiv_settings');

		submit_button();

	echo '</form>';
	
	echo '</div>';
}

function digiv_settings_init() {
	add_submenu_page(
		'options-general.php',
		'DigiV Einstellungen',
		'DigiV',
		'manage_options',
		'digiv_settings',
		'digiv_settings_render');
		
	
	$page = 'digiv_settings';
	
	$section = 'digiv_settings_general';
	add_settings_section($section, 'Allgemein', '__return_false', $page);
	
	add_settings_field(
		'digiv_set_host',
		'Host',
		'plugin_setting_callback',
		$page,
		$section,
		array('id'=>'digiv_set_host','type'=>'text')
		);
		
	add_settings_field(
		'digiv_set_realm',
		'Realm',
		'plugin_setting_callback',
		$page,
		$section,
		array('id'=>'digiv_set_realm','type'=>'text')
		);
		
	add_settings_field(
		'digiv_set_botuser',
		'Bot User',
		'plugin_setting_callback',
		$page,
		$section,
		array('id'=>'digiv_set_botuser','type'=>'text')
		);
		
	add_settings_field(
		'digiv_set_botpassword',
		'Bot Passwort',
		'plugin_setting_callback',
		$page,
		$section,
		array('id'=>'digiv_set_botpassword','type'=>'password')
		);
}
add_action('admin_menu', 'digiv_settings_init');

function digiv_settings_register() {
	register_setting('digiv_settings', 'digiv_set_host');
	register_setting('digiv_settings', 'digiv_set_realm');
	register_setting('digiv_settings', 'digiv_set_botuser');
	register_setting('digiv_settings', 'digiv_set_botpassword');
}
add_action('admin_init', 'digiv_settings_register');

/*
 * admin page
 */

function digiv_admin_page() {
	if ( ! current_user_can( 'edit_users' ) ) return;
	
	$tab = isset($_GET['tab'])?$_GET['tab']:null;

	echo '<div class="wrap">';
	echo '<h1>'.esc_html( get_admin_page_title() ).'</h1><br>';
	
	echo '<nav calss="nav-tab-wrapper">';
		echo '<a href="?page=digiv" class="nav-tab'.($tab==null?' nav-tab-active':'').'">Infos</a>';
		echo '<a href="?page=digiv&tab=list" class="nav-tab'.($tab=='list'?' nav-tab-active':'').'">Liste</a>';
		echo '<a href="?page=digiv&tab=user" class="nav-tab'.($tab=='user'?' nav-tab-active':'').'">Accounts</a>';
		echo '<a href="?page=digiv&tab=new" class="nav-tab'.($tab=='new'?' nav-tab-active':'').'">neu</a>';
	echo '</nav>';
	echo '<div class="clear" style="margin-bottom:20px"></div>';
	echo '<div class="tab-content">';

	switch ($tab) {
	default:
		echo 'Hier können die Daten der Teilnehmenden bearbeitet werden.';
		break;
	case 'list': 
		include(plugin_dir_path( __FILE__ ).'contents/list.php');
		break;
	case 'user':
		include(plugin_dir_path( __FILE__ ).'contents/user.php');
		break;
	}

	echo '</div>';
	echo '</div>';
}

/*
 * hook admin page
 */

function digiv_init_menu() {
	add_menu_page('DigiV Manager', 'DigiV', 'edit_users', 'digiv', 'digiv_admin_page');
}
add_action('admin_menu', 'digiv_init_menu');
