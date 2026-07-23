<?php
/**
 * Gutenberg block registration for Easy Mega Menu.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Block {

	/**
	 * Singleton instance.
	 *
	 * @var EMM_Block|null
	 */
	private static $instance = null;

	/**
	 * @return EMM_Block
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'localize_block_data' ) );
	}

	/**
	 * Register the block type from the build or src directory.
	 */
	public function register_block() {
		$build_dir = EMM_PLUGIN_DIR . 'build/blocks/mega-menu';
		$src_dir   = EMM_PLUGIN_DIR . 'src/blocks/mega-menu';

		if ( file_exists( $build_dir . '/block.json' ) ) {
			register_block_type( $build_dir, array(
				'render_callback' => array( $this, 'render_block' ),
			) );
		} elseif ( file_exists( $src_dir . '/block.json' ) ) {
			// Development fallback — block editor script won't work without build,
			// but the server-side render callback is available.
			register_block_type( $src_dir, array(
				'render_callback' => array( $this, 'render_block' ),
			) );
		}
		// If neither exists, the block is silently unavailable.
	}

	/**
	 * Server-side render callback.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string
	 */
	public function render_block( $attributes, $content, $block ) {
		$menu_id = $attributes['menuId'] ?? '';
		if ( ! $menu_id ) {
			return '';
		}
		return EMM_Frontend::instance()->render( $menu_id );
	}

	/**
	 * Pass available menus to the block editor so the dropdown can populate.
	 */
	public function localize_block_data() {
		$menus = EMM_Data::instance()->get_all();

		$menu_list = array();
		foreach ( $menus as $id => $menu ) {
			$menu_list[ $id ] = array(
				'title' => $menu['title'] ?? $id,
			);
		}

		wp_localize_script(
			'easy-mega-menu-mega-menu-editor-script',
			'emmBlockData',
			array(
				'menus' => $menu_list,
			)
		);
	}
}
