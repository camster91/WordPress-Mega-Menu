<?php
/**
 * Admin panel: list + visual mega menu builder.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Admin {

	/**
	 * @var EMM_Admin|null
	 */
	private static $instance = null;

	/**
	 * @return EMM_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_ajax_emm_save_menu', array( $this, 'ajax_save_menu' ) );
		add_action( 'wp_ajax_emm_delete_menu', array( $this, 'ajax_delete_menu' ) );
		add_action( 'wp_ajax_emm_create_menu', array( $this, 'ajax_create_menu' ) );
	}

	/**
	 * Register admin pages.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Easy Mega Menu', 'easy-mega-menu' ),
			__( 'Mega Menu', 'easy-mega-menu' ),
			'manage_options',
			'easy-mega-menu',
			array( $this, 'render_page' ),
			'dashicons-menu-alt3',
			58
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue( $hook ) {
		if ( 'toplevel_page_easy-mega-menu' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wplink' );
		wp_enqueue_style( 'editor-buttons' );

		wp_enqueue_style(
			'emm-admin',
			EMM_PLUGIN_URL . 'assets/css/admin.css',
			array( 'editor-buttons' ),
			EMM_VERSION
		);

		wp_enqueue_script(
			'emm-admin',
			EMM_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker', 'wplink' ),
			EMM_VERSION,
			true
		);

		$icons = EMM_Icons::instance();

		wp_localize_script(
			'emm-admin',
			'emmAdmin',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'emm_admin' ),
				'icons'     => $icons->all(),
				'iconLabels'=> $icons->labels(),
				'strings'   => array(
					'saved'          => __( 'Menu saved successfully!', 'easy-mega-menu' ),
					'saveError'      => __( 'Could not save. Please try again.', 'easy-mega-menu' ),
					'confirmDelete'  => __( 'Delete this menu? This cannot be undone.', 'easy-mega-menu' ),
					'confirmRemove'  => __( 'Remove this item?', 'easy-mega-menu' ),
					'newMenu'        => __( 'New Mega Menu', 'easy-mega-menu' ),
					'untitled'       => __( 'Untitled', 'easy-mega-menu' ),
					'addCategory'    => __( 'Add Category', 'easy-mega-menu' ),
					'addLink'        => __( 'Add Link', 'easy-mega-menu' ),
					'addNavItem'     => __( 'Add Menu Item', 'easy-mega-menu' ),
					'copyShortcode'  => __( 'Shortcode copied!', 'easy-mega-menu' ),
					'selectIcon'     => __( 'Choose an icon', 'easy-mega-menu' ),
					'uploadIcon'     => __( 'Upload custom icon', 'easy-mega-menu' ),
					'categoryTitle'  => __( 'Category title', 'easy-mega-menu' ),
					'categoryDesc'   => __( 'Short description', 'easy-mega-menu' ),
					'panelTitle'     => __( 'Panel heading (right side)', 'easy-mega-menu' ),
					'panelUrl'       => __( 'Panel heading link URL', 'easy-mega-menu' ),
					'groupUrl'       => __( 'Column heading link URL', 'easy-mega-menu' ),
					'linkLabel'      => __( 'Link label', 'easy-mega-menu' ),
					'linkUrl'        => __( 'Search or type URL', 'easy-mega-menu' ),
					'selectLink'     => __( 'Select / edit link', 'easy-mega-menu' ),
					'megaMenu'       => __( 'Mega Menu', 'easy-mega-menu' ),
					'simpleLink'     => __( 'Simple Link', 'easy-mega-menu' ),
					'dragHint'       => __( 'Drag to reorder', 'easy-mega-menu' ),
				),
			)
		);

		add_action( 'admin_footer', array( $this, 'print_link_dialog' ) );
	}

	/**
	 * Output the core Insert/Edit Link dialog markup.
	 */
	public function print_link_dialog() {
		if ( ! class_exists( '_WP_Editors', false ) ) {
			require_once ABSPATH . WPINC . '/class-wp-editor.php';
		}
		\_WP_Editors::wp_link_dialog();
				\_WP_Editors::wp_link_dialog(); // Leading backslash = global class ref (PHP 7.4+).="emm-wplink-textarea" style="display:none;" aria-hidden="true"></textarea>';
	}

	/**
	 * Render the admin app shell.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$menus   = EMM_Data::instance()->get_all();
		$edit_id = isset( $_GET['edit'] ) ? sanitize_text_field( wp_unslash( $_GET['edit'] ) ) : '';

		if ( $edit_id && isset( $menus[ $edit_id ] ) ) {
			include EMM_PLUGIN_DIR . 'admin/views/builder.php';
		} else {
			include EMM_PLUGIN_DIR . 'admin/views/list.php';
		}
	}

	/**
	 * AJAX: save menu.
	 */
	public function ajax_save_menu() {
		$this->verify_ajax();

		$id   = isset( $_POST['menu_id'] ) ? sanitize_text_field( wp_unslash( $_POST['menu_id'] ) ) : '';
		$raw  = isset( $_POST['menu_data'] ) ? wp_unslash( $_POST['menu_data'] ) : '';
		$data = json_decode( $raw, true );

		if ( ! $id || ! is_array( $data ) ) {
			wp_send_json_error( array( 'message' => 'Invalid data' ), 400 );
		}

		$saved = EMM_Data::instance()->save( $id, $data );
		if ( ! $saved ) {
			wp_send_json_error( array( 'message' => __( 'Database save failed.', 'easy-mega-menu' ) ), 500 );
		}
		wp_send_json_success( array( 'message' => 'saved' ) );
	}

	/**
	 * AJAX: delete menu.
	 */
	public function ajax_delete_menu() {
		$this->verify_ajax();

		$id = isset( $_POST['menu_id'] ) ? sanitize_text_field( wp_unslash( $_POST['menu_id'] ) ) : '';
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => 'Missing ID' ), 400 );
		}

		EMM_Data::instance()->delete( $id );
		wp_send_json_success();
	}

	/**
	 * AJAX: create menu.
	 */
	public function ajax_create_menu() {
		$this->verify_ajax();

		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$id    = EMM_Data::instance()->create( $title );

		wp_send_json_success(
			array(
				'id'  => $id,
				'url' => admin_url( 'admin.php?page=easy-mega-menu&edit=' . rawurlencode( $id ) ),
			)
		);
	}

	/**
	 * Verify nonce and capability.
	 */
	private function verify_ajax() {
		check_ajax_referer( 'emm_admin', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Forbidden' ), 403 );
		}
	}
}
