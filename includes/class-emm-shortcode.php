<?php
/**
 * Shortcode + theme helper.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Shortcode {

	/**
	 * @var EMM_Shortcode|null
	 */
	private static $instance = null;

	/**
	 * @return EMM_Shortcode
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'easy_mega_menu', array( $this, 'shortcode' ) );
	}

	/**
	 * [easy_mega_menu id="menu_demo"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'easy_mega_menu'
		);

		if ( empty( $atts['id'] ) ) {
			$menus = EMM_Data::instance()->get_all();
			if ( empty( $menus ) ) {
				return '';
			}
			$atts['id'] = array_key_first( $menus );
		}

		return EMM_Frontend::instance()->render( $atts['id'] );
	}
}

/**
 * Theme helper: echo a mega menu by ID.
 *
 * @param string $menu_id Menu ID.
 * @param array  $args    Optional args.
 */
function emm_render_menu( $menu_id, $args = array() ) {
	echo EMM_Frontend::instance()->render( $menu_id, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
