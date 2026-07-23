<?php
/**
 * Menu data storage and helpers.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Data {

	/**
	 * Singleton instance.
	 *
	 * @var EMM_Data|null
	 */
	private static $instance = null;

	/**
	 * @return EMM_Data
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get all menus.
	 *
	 * @return array
	 */
	public function get_all() {
		$menus = get_option( EMM_OPTION_KEY, array() );
		return is_array( $menus ) ? $menus : array();
	}

	/**
	 * Get a single menu by ID.
	 *
	 * @param string $id Menu ID.
	 * @return array|null
	 */
	public function get( $id ) {
		$menus = $this->get_all();
		if ( ! isset( $menus[ $id ] ) ) {
			return null;
		}
		$menu             = $menus[ $id ];
		$menu['settings'] = wp_parse_args( $menu['settings'] ?? array(), self::default_settings() );
		return $menu;
	}

	/**
	 * Save a menu.
	 *
	 * @param string $id   Menu ID.
	 * @param array  $data Menu data.
	 * @return bool
	 */
	public function save( $id, $data ) {
		$menus          = $this->get_all();
		$menus[ $id ]   = $this->sanitize_menu( $data );
		return update_option( EMM_OPTION_KEY, $menus );
	}

	/**
	 * Delete a menu.
	 *
	 * @param string $id Menu ID.
	 * @return bool
	 */
	public function delete( $id ) {
		$menus = $this->get_all();
		if ( ! isset( $menus[ $id ] ) ) {
			return false;
		}
		unset( $menus[ $id ] );
		return update_option( EMM_OPTION_KEY, $menus );
	}

	/**
	 * Default design settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'preset'          => 'classic',
			'layout'          => 'sidebar-left',
			'sidebar_bg'      => '#f0f0f5',
			'active_bg'       => '#ffffff',
			'panel_bg'        => '#ffffff',
			'header_bg'       => '#ffffff',
			'accent'          => '#1a73e8',
			'text_color'      => '#2c2c2c',
			'nav_link_color'  => '#2c2c2c',
			'nav_hover_color' => '#1a73e8',
			'muted_color'     => '#6b6b6b',
			'border_color'    => '#e5e5ea',
			'cta_text'        => '#ffffff',
			'grid_columns'    => 3,
			'panel_width'     => 1000,
			'full_width'      => true,
			'sidebar_width'   => 280,
			'border_radius'   => 8,
			'shadow'          => 'medium',
			'nav_align'       => 'center',
			'cta_style'       => 'rounded',
			'uppercase_cats'  => true,
			'show_cat_desc'   => true,
			'panel_title_align' => 'center',
		);
	}

	/**
	 * Built-in design presets (easy one-click themes).
	 *
	 * @return array
	 */
	public static function design_presets() {
		return array(
			'classic' => array(
				'label'  => __( 'Classic', 'easy-mega-menu' ),
				'values' => array(
					'sidebar_bg'      => '#f0f0f5',
					'active_bg'       => '#ffffff',
					'panel_bg'        => '#ffffff',
					'header_bg'       => '#ffffff',
					'accent'          => '#1a73e8',
					'text_color'      => '#2c2c2c',
					'nav_link_color'  => '#2c2c2c',
					'nav_hover_color' => '#1a73e8',
					'muted_color'     => '#6b6b6b',
					'border_color'    => '#e5e5ea',
					'cta_text'        => '#ffffff',
					'shadow'          => 'medium',
					'border_radius'   => 8,
				),
			),
			'dark'    => array(
				'label'  => __( 'Dark', 'easy-mega-menu' ),
				'values' => array(
					'sidebar_bg'      => '#1e1e24',
					'active_bg'       => '#2a2a32',
					'panel_bg'        => '#16161a',
					'header_bg'       => '#111114',
					'accent'          => '#5b9fff',
					'text_color'      => '#f2f2f5',
					'nav_link_color'  => '#e8e8ed',
					'nav_hover_color' => '#5b9fff',
					'muted_color'     => '#a0a0ab',
					'border_color'    => '#2e2e36',
					'cta_text'        => '#111114',
					'shadow'          => 'strong',
					'border_radius'   => 10,
				),
			),
			'soft'    => array(
				'label'  => __( 'Soft', 'easy-mega-menu' ),
				'values' => array(
					'sidebar_bg'      => '#eef6f3',
					'active_bg'       => '#ffffff',
					'panel_bg'        => '#ffffff',
					'header_bg'       => '#fbfffd',
					'accent'          => '#0d9488',
					'text_color'      => '#134e4a',
					'nav_link_color'  => '#134e4a',
					'nav_hover_color' => '#0d9488',
					'muted_color'     => '#5f7a76',
					'border_color'    => '#d5ebe4',
					'cta_text'        => '#ffffff',
					'shadow'          => 'soft',
					'border_radius'   => 14,
				),
			),
			'bold'    => array(
				'label'  => __( 'Bold', 'easy-mega-menu' ),
				'values' => array(
					'sidebar_bg'      => '#fff1e8',
					'active_bg'       => '#ffffff',
					'panel_bg'        => '#ffffff',
					'header_bg'       => '#ffffff',
					'accent'          => '#e85d04',
					'text_color'      => '#1a1a1a',
					'nav_link_color'  => '#1a1a1a',
					'nav_hover_color' => '#e85d04',
					'muted_color'     => '#6b6b6b',
					'border_color'    => '#f0d9c8',
					'cta_text'        => '#ffffff',
					'shadow'          => 'medium',
					'border_radius'   => 4,
				),
			),
			'minimal' => array(
				'label'  => __( 'Minimal', 'easy-mega-menu' ),
				'values' => array(
					'sidebar_bg'      => '#fafafa',
					'active_bg'       => '#ffffff',
					'panel_bg'        => '#ffffff',
					'header_bg'       => '#ffffff',
					'accent'          => '#111111',
					'text_color'      => '#111111',
					'nav_link_color'  => '#111111',
					'nav_hover_color' => '#111111',
					'muted_color'     => '#767676',
					'border_color'    => '#e8e8e8',
					'cta_text'        => '#ffffff',
					'shadow'          => 'none',
					'border_radius'   => 0,
				),
			),
		);
	}

	/**
	 * Create a new empty menu and return its ID.
	 *
	 * @param string $title Menu title.
	 * @return string New menu ID.
	 */
	public function create( $title = '' ) {
		$id    = 'menu_' . wp_generate_password( 8, false, false );
		$menus = $this->get_all();

		$menus[ $id ] = $this->sanitize_menu(
			array(
				'title'    => $title ? $title : __( 'New Mega Menu', 'easy-mega-menu' ),
				'items'    => array(),
				'cta'      => array(
					'label' => __( 'Get a Demo', 'easy-mega-menu' ),
					'url'   => '#',
					'show'  => true,
				),
				'settings' => self::default_settings(),
			)
		);

		update_option( EMM_OPTION_KEY, $menus );
		return $id;
	}

	/**
	 * Sanitize full menu payload.
	 *
	 * @param array $data Raw data.
	 * @return array
	 */
	public function sanitize_menu( $data ) {
		$defaults = self::default_settings();
		$raw      = is_array( $data['settings'] ?? null ) ? $data['settings'] : array();
		$s        = wp_parse_args( $raw, $defaults );

		$layouts = array( 'sidebar-left', 'sidebar-right', 'stacked' );
		$layout  = sanitize_key( $s['layout'] );
		if ( ! in_array( $layout, $layouts, true ) ) {
			$layout = 'sidebar-left';
		}

		$presets = array_keys( self::design_presets() );
		$preset  = sanitize_key( $s['preset'] );
		if ( ! in_array( $preset, $presets, true ) && 'custom' !== $preset ) {
			$preset = 'classic';
		}

		$shadows = array( 'none', 'soft', 'medium', 'strong' );
		$shadow  = sanitize_key( $s['shadow'] );
		if ( ! in_array( $shadow, $shadows, true ) ) {
			$shadow = 'medium';
		}

		$nav_align = sanitize_key( $s['nav_align'] );
		if ( ! in_array( $nav_align, array( 'left', 'center', 'right' ), true ) ) {
			$nav_align = 'center';
		}

		$cta_style = sanitize_key( $s['cta_style'] );
		if ( ! in_array( $cta_style, array( 'square', 'rounded', 'pill' ), true ) ) {
			$cta_style = 'rounded';
		}

		$title_align = sanitize_key( $s['panel_title_align'] );
		if ( ! in_array( $title_align, array( 'left', 'center' ), true ) ) {
			$title_align = 'center';
		}

		$grid = absint( $s['grid_columns'] );
		if ( $grid < 1 || $grid > 6 ) {
			$grid = 3;
		}

		$panel_width   = min( 1400, max( 640, absint( $s['panel_width'] ) ) );
		$sidebar_width = min( 420, max( 180, absint( $s['sidebar_width'] ) ) );
		$radius        = min( 24, max( 0, absint( $s['border_radius'] ) ) );

		$menu = array(
			'title'    => sanitize_text_field( $data['title'] ?? '' ),
			'items'    => array(),
			'cta'      => array(
				'label' => sanitize_text_field( $data['cta']['label'] ?? '' ),
				'url'   => esc_url_raw( $data['cta']['url'] ?? '' ),
				'show'  => ! empty( $data['cta']['show'] ),
			),
			'settings' => array(
				'preset'            => $preset,
				'layout'            => $layout,
				'sidebar_bg'        => sanitize_hex_color( $s['sidebar_bg'] ) ?: $defaults['sidebar_bg'],
				'active_bg'         => sanitize_hex_color( $s['active_bg'] ) ?: $defaults['active_bg'],
				'panel_bg'          => sanitize_hex_color( $s['panel_bg'] ) ?: $defaults['panel_bg'],
				'header_bg'         => sanitize_hex_color( $s['header_bg'] ) ?: $defaults['header_bg'],
				'accent'            => sanitize_hex_color( $s['accent'] ) ?: $defaults['accent'],
				'text_color'        => sanitize_hex_color( $s['text_color'] ) ?: $defaults['text_color'],
				'nav_link_color'    => sanitize_hex_color( $s['nav_link_color'] ?? '' ) ?: ( sanitize_hex_color( $s['text_color'] ) ?: $defaults['nav_link_color'] ),
				'nav_hover_color'   => sanitize_hex_color( $s['nav_hover_color'] ?? '' ) ?: ( sanitize_hex_color( $s['accent'] ) ?: $defaults['nav_hover_color'] ),
				'muted_color'       => sanitize_hex_color( $s['muted_color'] ) ?: $defaults['muted_color'],
				'border_color'      => sanitize_hex_color( $s['border_color'] ) ?: $defaults['border_color'],
				'cta_text'          => sanitize_hex_color( $s['cta_text'] ) ?: $defaults['cta_text'],
				'grid_columns'      => $grid,
				'panel_width'       => $panel_width,
				'full_width'        => ! empty( $s['full_width'] ),
				'sidebar_width'     => $sidebar_width,
				'border_radius'     => $radius,
				'shadow'            => $shadow,
				'nav_align'         => $nav_align,
				'cta_style'         => $cta_style,
				'uppercase_cats'    => ! empty( $s['uppercase_cats'] ),
				'show_cat_desc'     => ! empty( $s['show_cat_desc'] ),
				'panel_title_align' => $title_align,
			),
		);

		if ( ! empty( $data['items'] ) && is_array( $data['items'] ) ) {
			foreach ( $data['items'] as $item ) {
				$menu['items'][] = $this->sanitize_top_item( $item );
			}
		}

		return $menu;
	}

	/**
	 * Sanitize a top-level nav item.
	 *
	 * @param array $item Item data.
	 * @return array
	 */
	private function sanitize_top_item( $item ) {
		$type = sanitize_key( $item['type'] ?? 'link' );
		if ( ! in_array( $type, array( 'link', 'mega' ), true ) ) {
			$type = 'link';
		}

		$out = array(
			'id'    => sanitize_text_field( $item['id'] ?? wp_generate_password( 6, false, false ) ),
			'label' => sanitize_text_field( $item['label'] ?? '' ),
			'url'   => esc_url_raw( $item['url'] ?? '' ),
			'type'  => $type,
		);

		if ( 'mega' === $type ) {
			$style = sanitize_key( $item['mega_style'] ?? 'platforms' );
			if ( ! in_array( $style, array( 'platforms', 'features' ), true ) ) {
				$style = 'platforms';
			}
			$out['mega_style'] = $style;

			$cols = absint( $item['columns'] ?? 0 );
			if ( $cols < 1 || $cols > 4 ) {
				$cols = ( 'features' === $style ) ? 2 : 3;
			}
			$out['columns'] = $cols;

			$out['categories'] = array();
			if ( ! empty( $item['categories'] ) && is_array( $item['categories'] ) ) {
				foreach ( $item['categories'] as $cat ) {
					$out['categories'][] = $this->sanitize_category( $cat );
				}
			}

			$out['links'] = array();
			if ( ! empty( $item['links'] ) && is_array( $item['links'] ) ) {
				foreach ( $item['links'] as $link ) {
					$out['links'][] = $this->sanitize_link( $link );
				}
			}
		}

		return $out;
	}

	/**
	 * Sanitize a single grid link.
	 *
	 * @param array $link Link data.
	 * @return array
	 */
	private function sanitize_link( $link ) {
		return array(
			'id'       => sanitize_text_field( $link['id'] ?? wp_generate_password( 6, false, false ) ),
			'label'    => sanitize_text_field( $link['label'] ?? '' ),
			'url'      => $this->sanitize_menu_url( $link['url'] ?? '' ),
			'icon'     => sanitize_text_field( $link['icon'] ?? '' ),
			'icon_url' => $this->sanitize_menu_url( $link['icon_url'] ?? '' ),
		);
	}

	/**
	 * Sanitize menu URLs, preserving site-relative paths like /about-us.html.
	 *
	 * @param string $url Raw URL.
	 * @return string
	 */
	private function sanitize_menu_url( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url || '#' === $url ) {
			return $url;
		}

		// Keep root-relative paths (common for Sutisoft-style site links).
		if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
			$path = wp_kses_no_null( $url );
			$path = str_replace( array( "\r", "\n", "\t", ' ' ), '', $path );
			return $path;
		}

		return esc_url_raw( $url );
	}

	/**
	 * Sanitize a sidebar category.
	 *
	 * Supports titled column groups (`groups`) under each category.
	 * Legacy flat `links` are migrated into a single untitled group.
	 *
	 * @param array $cat Category data.
	 * @return array
	 */
	private function sanitize_category( $cat ) {
		$out = array(
			'id'          => sanitize_text_field( $cat['id'] ?? wp_generate_password( 6, false, false ) ),
			'title'       => sanitize_text_field( $cat['title'] ?? '' ),
			'description' => sanitize_text_field( $cat['description'] ?? '' ),
			'icon'        => sanitize_text_field( $cat['icon'] ?? '' ),
			'icon_url'    => $this->sanitize_menu_url( $cat['icon_url'] ?? '' ),
			'panel_title' => sanitize_text_field( $cat['panel_title'] ?? '' ),
			'panel_url'   => $this->sanitize_menu_url( $cat['panel_url'] ?? '' ),
			'groups'      => array(),
			'links'       => array(),
		);

		if ( ! empty( $cat['groups'] ) && is_array( $cat['groups'] ) ) {
			foreach ( $cat['groups'] as $group ) {
				$out['groups'][] = $this->sanitize_group( $group );
			}
		} elseif ( ! empty( $cat['links'] ) && is_array( $cat['links'] ) ) {
			$legacy_links = array();
			foreach ( $cat['links'] as $link ) {
				$legacy_links[] = $this->sanitize_link( $link );
			}
			$out['groups'][] = array(
				'id'    => 'col_' . wp_generate_password( 6, false, false ),
				'title' => '',
				'url'   => '',
				'links' => $legacy_links,
			);
		}

		return $out;
	}

	/**
	 * Sanitize a column group inside a category.
	 *
	 * @param array $group Group data.
	 * @return array
	 */
	private function sanitize_group( $group ) {
		$out = array(
			'id'    => sanitize_text_field( $group['id'] ?? wp_generate_password( 6, false, false ) ),
			'title' => sanitize_text_field( $group['title'] ?? '' ),
			'url'   => $this->sanitize_menu_url( $group['url'] ?? '' ),
			'links' => array(),
		);

		if ( ! empty( $group['links'] ) && is_array( $group['links'] ) ) {
			foreach ( $group['links'] as $link ) {
				$out['links'][] = $this->sanitize_link( $link );
			}
		}

		return $out;
	}

	/**
	 * Demo menus matching the Sutisoft Platforms mega menu.
	 *
	 * @return array
	 */
	public static function demo_menus() {
		$empty = array( 'icon' => '', 'icon_url' => '' );

		return array(
			'menu_demo' => array(
				'title'    => __( 'Main Navigation', 'easy-mega-menu' ),
				'items'    => array(
					array(
						'id'         => 'platforms',
						'label'      => 'Platforms',
						'url'        => '#',
						'type'       => 'mega',
						'mega_style' => 'platforms',
						'columns'    => 3,
						'links'      => array(),
						'categories' => array(
						array(
							'id'          => 'spend',
							'title'       => 'SPEND',
							'description' => 'Total Spend Control. Powered by AI.',
							'icon'        => 'wallet',
							'icon_url'    => '',
							'panel_title' => 'Spend Platform Overview',
							'panel_url'   => '/spend-management-software',
							'groups'      => array(
									array(
										'id'    => 'spend_col1',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'sp1', 'label' => 'Expense', 'url' => '/sutiexpense/' ), $empty ),
											array_merge( array( 'id' => 'sp2', 'label' => 'Accounts Payable', 'url' => '/accounts-payable-software/' ), $empty ),
											array_merge( array( 'id' => 'sp3', 'label' => 'Procurement', 'url' => '/sutiprocure/' ), $empty ),
										),
									),
									array(
										'id'    => 'spend_col2',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'sp4', 'label' => 'Business Travel', 'url' => '/business-travel/' ), $empty ),
											array_merge( array( 'id' => 'sp5', 'label' => 'Invoicing', 'url' => '/sutiinvoice/' ), $empty ),
											array_merge( array( 'id' => 'sp6', 'label' => 'Asset Management', 'url' => '/sutiams/' ), $empty ),
										),
									),
									array(
										'id'    => 'spend_col3',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'sp7', 'label' => 'Inventory Management', 'url' => '/inventory-management-software/' ), $empty ),
											array_merge( array( 'id' => 'sp8', 'label' => 'Supplier Relationship Management', 'url' => '/sutisrm/' ), $empty ),
											array_merge( array( 'id' => 'sp9', 'label' => 'Data Analytics', 'url' => '/sutidanalytics/' ), $empty ),
										),
									),
								),
							),
						array(
							'id'          => 'sign',
							'title'       => 'SIGN',
							'description' => 'Create, Review, Sign, and Store—All in One Secure Platform.',
							'icon'        => 'signature',
							'icon_url'    => '',
							'panel_title' => 'eSignature Platform Overview',
							'panel_url'   => '/e-signature-software/platform/',
							'groups'      => array(
									array(
										'id'    => 'sign_col1',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'sg1', 'label' => 'Electronic Signature', 'url' => '/e-signature-software' ), $empty ),
											array_merge( array( 'id' => 'sg2', 'label' => 'Contract Lifecycle Management', 'url' => '/suticlm/' ), $empty ),
										),
									),
									array(
										'id'    => 'sign_col2',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'sg3', 'label' => 'Document Management', 'url' => '/sutidms/' ), $empty ),
											array_merge( array( 'id' => 'sg4', 'label' => 'Data Analytics', 'url' => '/sutidanalytics/' ), $empty ),
										),
									),
								),
							),
						array(
							'id'          => 'hr',
							'title'       => 'HR',
							'description' => 'All-in-one HR automation to power your workforce.',
							'icon'        => 'users',
							'icon_url'    => '',
							'panel_title' => 'HR Platform Overview',
							'panel_url'   => '/hr-software/',
							'groups'      => array(
								array(
									'id'    => 'hr_talent',
									'title' => 'Talent Management',
									'url'   => '/hr-software/talent-management-software.html',
									'links' => array(
											array_merge( array( 'id' => 'ht1', 'label' => 'Applicant Tracking System', 'url' => '/hr-software/applicant-tracking.html' ), $empty ),
											array_merge( array( 'id' => 'ht2', 'label' => 'Employee Training', 'url' => '/hr-software/training-management.html' ), $empty ),
											array_merge( array( 'id' => 'ht3', 'label' => 'Goal Management', 'url' => '/hr-software/goal-management.html' ), $empty ),
											array_merge( array( 'id' => 'ht4', 'label' => 'Performance Management', 'url' => '/hr-software/performance-review.html' ), $empty ),
											array_merge( array( 'id' => 'ht5', 'label' => 'Compensation Management', 'url' => '/hr-software/compensation-management.html' ), $empty ),
											array_merge( array( 'id' => 'ht6', 'label' => 'HR Analytics & Reports', 'url' => '/hr-software/hr-reports.html' ), $empty ),
										),
									),
									array(
										'id'    => 'hr_workforce',
										'title' => 'Workforce Management',
										'url'   => '/hr-software/workforce-management-software.html',
										'links' => array(
											array_merge( array( 'id' => 'hw1', 'label' => 'Time Tracking', 'url' => '/hr-software/time-tracking-software.html' ), $empty ),
											array_merge( array( 'id' => 'hw2', 'label' => 'Attendance Management', 'url' => '/hr-software/attendance-management-software.html' ), $empty ),
											array_merge( array( 'id' => 'hw3', 'label' => 'Leave Management', 'url' => '/hr-software/leave-management-software.html' ), $empty ),
											array_merge( array( 'id' => 'hw4', 'label' => 'Shift Scheduling', 'url' => '/hr-software/employee-scheduling-software.html' ), $empty ),
											array_merge( array( 'id' => 'hw5', 'label' => 'Project Time Tracking', 'url' => '/hr-software/project-time-tracking.html' ), $empty ),
											array_merge( array( 'id' => 'hw6', 'label' => 'Payroll Management', 'url' => '/hr-software/payroll-management-software.html' ), $empty ),
											array_merge( array( 'id' => 'hw7', 'label' => 'Expense Management', 'url' => '/hr-software/employee-expense-tracking.html' ), $empty ),
										),
									),
									array(
										'id'    => 'hr_core',
										'title' => 'Core HR',
										'url'   => '/hr-software/',
										'links' => array(
											array_merge( array( 'id' => 'hc1', 'label' => 'Employee Records', 'url' => '/hr-software/personnel-management.html' ), $empty ),
											array_merge( array( 'id' => 'hc2', 'label' => 'Benefits Administration', 'url' => '/hr-software/employee-benefits.html' ), $empty ),
											array_merge( array( 'id' => 'hc3', 'label' => 'Document Management', 'url' => '/hr-software/document-management.html' ), $empty ),
											array_merge( array( 'id' => 'hc4', 'label' => 'Employee Self-Service', 'url' => '/hr-software/employee-self-service.html' ), $empty ),
											array_merge( array( 'id' => 'hc5', 'label' => 'Mobile App', 'url' => '/hr-software/mobile.html' ), $empty ),
										),
									),
								),
							),
							array(
								'id'          => 'crm',
								'title'       => 'CRM',
								'description' => 'Smarter Insights, Stronger Relationships, Faster Sales.',
								'icon'        => 'handshake',
								'icon_url'    => '',
								'panel_title' => 'CRM Platform Overview',
								'panel_url'   => '/crm-software/',
								'groups'      => array(
									array(
										'id'    => 'crm_col1',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'cr1', 'label' => 'Help Desk', 'url' => '/helpdesk-software/' ), $empty ),
											array_merge( array( 'id' => 'cr2', 'label' => 'Property Management', 'url' => '/sutipms/' ), $empty ),
										),
									),
									array(
										'id'    => 'crm_col2',
										'title' => '',
										'links' => array(
											array_merge( array( 'id' => 'cr3', 'label' => 'Survey', 'url' => 'http://www.sutisurvey.com' ), $empty ),
											array_merge( array( 'id' => 'cr4', 'label' => 'Data Analytics', 'url' => '/sutidanalytics/' ), $empty ),
										),
									),
								),
							),
						),
					),
					array(
						'id'         => 'company',
						'label'      => 'Company',
						'url'        => '#',
						'type'       => 'mega',
						'mega_style' => 'features',
						'columns'    => 1,
						'categories' => array(),
						'links'      => array(
							array( 'id' => 'co1', 'label' => 'About Us', 'url' => '/about-us.html', 'icon' => 'building', 'icon_url' => '' ),
							array( 'id' => 'co2', 'label' => 'Management', 'url' => '/management.html', 'icon' => 'users', 'icon_url' => '' ),
							array( 'id' => 'co3', 'label' => 'Customers', 'url' => '/customers.html', 'icon' => 'handshake', 'icon_url' => '' ),
						),
					),
					array(
						'id'         => 'resources',
						'label'      => 'Resources',
						'url'        => '#',
						'type'       => 'mega',
						'mega_style' => 'features',
						'columns'    => 1,
						'categories' => array(),
						'links'      => array(
							array( 'id' => 're1', 'label' => 'Blog', 'url' => 'https://www.sutisoft.com/blog-home/', 'icon' => 'document', 'icon_url' => '' ),
							array( 'id' => 're2', 'label' => 'Press Releases', 'url' => '/latest-news.html', 'icon' => 'clipboard', 'icon_url' => '' ),
							array( 'id' => 're3', 'label' => 'White Papers', 'url' => '/whitepapers.html', 'icon' => 'document', 'icon_url' => '' ),
						),
					),
					array(
						'id'    => 'contact',
						'label' => 'Contact Us',
						'url'   => '/contact-us.html',
						'type'  => 'link',
					),
				),
				'cta'      => array(
					'label' => 'Get a Demo',
					'url'   => '/contact-us.html',
					'show'  => true,
				),
				'settings' => EMM_Data::default_settings(),
			),
		);
	}
}
