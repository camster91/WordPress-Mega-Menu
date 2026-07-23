<?php
/**
 * Plugin Name: Easy Mega Menu
 * Plugin URI:  https://github.com/camster91/WordPress-Mega-Menu
 * Description: Create beautiful mega menus like corporate platforms menus — managed visually from the admin panel. No coding required.
 * Version:     1.2.5
 * Author:      Cameron
 * Author URI:  https://github.com/camster91
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
require_once EMM_PLUGIN_DIR . 'includes/class-emm-updater.php';

/* ==================================================================
   Bootstrap
   ================================================================== */

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

/* ==================================================================
   Activation
   ================================================================== */

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

/* ==================================================================
   Deactivation
   ================================================================== */

function emm_deactivate() {
	// Clear update transients so the next activation gets a fresh check.
	delete_transient( 'emm_github_release' );
	delete_transient( 'emm_plugin_info' );
}
register_deactivation_hook( __FILE__, 'emm_deactivate' );

/* ==================================================================
   Upgrade handler
   ================================================================== */

function emm_maybe_upgrade() {
	$stored = get_option( EMM_VERSION_OPTION );

	if ( EMM_VERSION === $stored ) {
		return;
	}

	$menus = get_option( EMM_OPTION_KEY, array() );
	if ( ! is_array( $menus ) ) {
		$menus = array();
	}

	// Always refresh demo menu on version bump.
	$demo               = EMM_Data::demo_menus();
	$menus['menu_demo'] = $demo['menu_demo'];
	update_option( EMM_OPTION_KEY, $menus );
	update_option( EMM_VERSION_OPTION, EMM_VERSION );
}

/* ==================================================================
   Auto-updater (GitHub releases)
   ================================================================== */

add_action( 'plugins_loaded', array( 'EMM_Updater', 'instance' ), 11 );
