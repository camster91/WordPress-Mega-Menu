<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Easy_Mega_Menu
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Capability check.
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}

// Prevent running twice.
if ( defined( 'EMM_UNINSTALLING' ) ) {
	return;
}
define( 'EMM_UNINSTALLING', true );

/* ------------------------------------------------------------------
   Remove all plugin options / transients
   ------------------------------------------------------------------ */
$option_keys = array(
	'emm_menus',
	'emm_plugin_version',
	'emm_updater_last_check',
	'emm_updater_latest_version',
	'emm_updater_latest_url',
);

foreach ( $option_keys as $key ) {
	delete_option( $key );
	delete_site_option( $key ); // multisite
}

/* ------------------------------------------------------------------
   Remove transients
   ------------------------------------------------------------------ */
$transient_keys = array(
	'emm_github_release',
	'emm_plugin_info',
);

foreach ( $transient_keys as $key ) {
	delete_transient( $key );
	delete_site_transient( $key ); // multisite
}
