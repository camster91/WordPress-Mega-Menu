<?php
/**
 * Built-in SVG icon library for mega menu items.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Icons {

	/**
	 * @var EMM_Icons|null
	 */
	private static $instance = null;

	/**
	 * @return EMM_Icons
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Icon key => SVG markup map.
	 *
	 * @return array
	 */
	public function all() {
		$attrs = 'xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';

		return array(
			'wallet'      => '<svg ' . $attrs . '><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/></svg>',
			'signature'   => '<svg ' . $attrs . '><path d="M3 17c2-4 4-6 6-6s2 3 4 3 3-5 5-5 2 2 3 4"/><path d="M3 21h18"/></svg>',
			'users'       => '<svg ' . $attrs . '><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
			'handshake'   => '<svg ' . $attrs . '><path d="M11 17l2 2a1.5 1.5 0 0 0 2.1 0l5.4-5.4a1.5 1.5 0 0 0 0-2.1L16 7"/><path d="M14 13l-2.5 2.5a1.5 1.5 0 0 1-2.1 0L5.5 12"/><path d="M2 12l4-4 3 3"/><path d="M22 12l-4-4-3 3"/></svg>',
			'dollar'      => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M12 6v12"/><path d="M15 9.5c0-1.5-1.3-2.5-3-2.5s-3 1-3 2.5 1.3 2 3 2.5 3 1 3 2.5-1.3 2.5-3 2.5-3-1-3-2.5"/></svg>',
			'airplane'    => '<svg ' . $attrs . '><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.6c-.2.5 0 1 .4 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.4l.6-.3c.4-.2.6-.6.5-1.1z"/></svg>',
			'card'        => '<svg ' . $attrs . '><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>',
			'search-gear' => '<svg ' . $attrs . '><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/><path d="M11 8v2"/><path d="M11 14v.01"/></svg>',
			'document'    => '<svg ' . $attrs . '><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h8"/></svg>',
			'building'    => '<svg ' . $attrs . '><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-4h6v4"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 13h.01"/><path d="M15 13h.01"/></svg>',
			'clipboard'   => '<svg ' . $attrs . '><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>',
			'monitor'     => '<svg ' . $attrs . '><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>',
			'chart'       => '<svg ' . $attrs . '><path d="M3 3v18h18"/><path d="M7 14l4-4 4 2 5-6"/></svg>',
			'link'        => '<svg ' . $attrs . '><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
			'star'        => '<svg ' . $attrs . '><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
			'grid'        => '<svg ' . $attrs . '><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
			'heart'       => '<svg ' . $attrs . '><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>',
			'shield'      => '<svg ' . $attrs . '><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
			'globe'       => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18"/><path d="M12 3a14 14 0 0 0 0 18"/></svg>',
			'mail'        => '<svg ' . $attrs . '><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>',
			'phone'       => '<svg ' . $attrs . '><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.8.3 1.6.6 2.3a2 2 0 0 1-.4 2.1L8.1 9.9a16 16 0 0 0 6 6l1.8-1.2a2 2 0 0 1 2.1-.4c.7.3 1.5.5 2.3.6a2 2 0 0 1 1.7 2z"/></svg>',
			'settings'    => '<svg ' . $attrs . '><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>',
		);
	}

	/**
	 * Labels for the icon picker UI.
	 *
	 * @return array
	 */
	public function labels() {
		return array(
			'wallet'      => __( 'Wallet', 'easy-mega-menu' ),
			'signature'   => __( 'Signature', 'easy-mega-menu' ),
			'users'       => __( 'Users', 'easy-mega-menu' ),
			'handshake'   => __( 'Handshake', 'easy-mega-menu' ),
			'dollar'      => __( 'Dollar', 'easy-mega-menu' ),
			'airplane'    => __( 'Airplane', 'easy-mega-menu' ),
			'card'        => __( 'Card', 'easy-mega-menu' ),
			'search-gear' => __( 'Search', 'easy-mega-menu' ),
			'document'    => __( 'Document', 'easy-mega-menu' ),
			'building'    => __( 'Building', 'easy-mega-menu' ),
			'clipboard'   => __( 'Clipboard', 'easy-mega-menu' ),
			'monitor'     => __( 'Monitor', 'easy-mega-menu' ),
			'chart'       => __( 'Chart', 'easy-mega-menu' ),
			'link'        => __( 'Link', 'easy-mega-menu' ),
			'star'        => __( 'Star', 'easy-mega-menu' ),
			'grid'        => __( 'Grid', 'easy-mega-menu' ),
			'heart'       => __( 'Heart', 'easy-mega-menu' ),
			'shield'      => __( 'Shield', 'easy-mega-menu' ),
			'globe'       => __( 'Globe', 'easy-mega-menu' ),
			'mail'        => __( 'Mail', 'easy-mega-menu' ),
			'phone'       => __( 'Phone', 'easy-mega-menu' ),
			'settings'    => __( 'Settings', 'easy-mega-menu' ),
		);
	}

	/**
	 * Render an icon by key or custom URL.
	 *
	 * @param string $key     Built-in icon key.
	 * @param string $url     Optional custom image URL.
	 * @param string $class   CSS class.
	 * @return string HTML
	 */
	public function render( $key, $url = '', $class = 'emm-icon' ) {
		if ( $url ) {
			return sprintf(
				'<img src="%s" alt="" class="%s emm-icon--custom" />',
				esc_url( $url ),
				esc_attr( $class )
			);
		}

		$icons = $this->all();
		if ( $key && isset( $icons[ $key ] ) ) {
			return '<span class="' . esc_attr( $class ) . '">' . $icons[ $key ] . '</span>';
		}

		return '<span class="' . esc_attr( $class ) . '">' . $icons['link'] . '</span>';
	}
}
