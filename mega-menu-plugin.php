<?php
/**
 * Plugin Name: Easy Mega Menu
 * Plugin URI:  https://example.com/easy-mega-menu
 * Description: Create beautiful mega menus like corporate platforms menus — managed visually from the admin panel. No coding required.
 * Version:     1.2.5
 * Author:      Cameron
 * Author URI:  https://example.com
 * License:     GPL-2.0-or-later
 * Text Domain: easy-mega-menu
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EMM_VERSION', '1.2.5' );
define( 'EMM_PLUGIN_FILE', __FILE__ );
define( 'EMM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EMM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EMM_OPTION_KEY', 'emm_menus' );
define( 'EMM_VERSION_OPTION', 'emm_plugin_version' );

require_once EMM_PLUGIN_DIR . 'includes/class-emm-data.php';
require_once EMM_PLUGIN_DIR . 'includes/class-emm-icons.php';
require_once EMM_PLUGIN_DIR . 'includes/class-emm-admin.php';
require_once EMM_PLUGIN_DIR . 'includes/class-emm-frontend.php';
require_once EMM_PLUGIN_DIR . 'includes/class-emm-shortcode.php';

/**
 * Bootstrap the plugin.
 */
function emm_init() {
	EMM_Data::instance();
	EMM_Icons::instance();
	emm_maybe_upgrade();

	if ( is_admin() ) {
		EMM_Admin::instance();
	}

	EMM_Frontend::instance();
	EMM_Shortcode::instance();
}
add_action( 'plugins_loaded', 'emm_init' );

/**
 * Refresh demo menu content when the plugin version changes.
 */
function emm_maybe_upgrade() {
	$stored = get_option( EMM_VERSION_OPTION );
	if ( EMM_VERSION === $stored ) {
		return;
	}

	$menus = get_option( EMM_OPTION_KEY, array() );
	if ( ! is_array( $menus ) ) {
		$menus = array();
	}

	$demo               = EMM_Data::demo_menus();
	$menus['menu_demo'] = $demo['menu_demo'];
	update_option( EMM_OPTION_KEY, $menus );
	update_option( EMM_VERSION_OPTION, EMM_VERSION );
}

/**
 * Activation: seed demo menu matching the Sutisoft mega menu.
 */
function emm_activate() {
	$existing = get_option( EMM_OPTION_KEY );
	if ( false === $existing || empty( $existing ) ) {
		update_option( EMM_OPTION_KEY, EMM_Data::demo_menus() );
	} else {
		emm_maybe_upgrade();
	}
	update_option( EMM_VERSION_OPTION, EMM_VERSION );
}
register_activation_hook( __FILE__, 'emm_activate' );
