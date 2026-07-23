<?php
/**
 * Frontend rendering of mega menus.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Frontend {

	/**
	 * @var EMM_Frontend|null
	 */
	private static $instance = null;

	/**
	 * @var bool
	 */
	private $assets_enqueued = false;

	/**
	 * @return EMM_Frontend
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register (but do not always enqueue) front assets.
	 */
	public function register_assets() {
		wp_register_style(
			'emm-frontend',
			EMM_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			EMM_VERSION
		);

		wp_register_script(
			'emm-frontend',
			EMM_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			EMM_VERSION,
			true
		);
	}

	/**
	 * Enqueue assets once when a menu is rendered.
	 */
	public function enqueue_assets() {
		if ( $this->assets_enqueued ) {
			return;
		}
		wp_enqueue_style( 'emm-frontend' );
		wp_enqueue_script( 'emm-frontend' );
		$this->assets_enqueued = true;
	}

	/**
	 * Render a full menu by ID.
	 *
	 * @param string $menu_id Menu ID.
	 * @param array  $args    Optional args.
	 * @return string HTML
	 */
	public function render( $menu_id, $args = array() ) {
		$menu = EMM_Data::instance()->get( $menu_id );
		if ( ! $menu ) {
			return '';
		}

		$this->enqueue_assets();

		$settings = wp_parse_args(
			$menu['settings'] ?? array(),
			EMM_Data::default_settings()
		);

		$cta = $menu['cta'] ?? array();

		$classes = array(
			'emm-header',
			'emm-layout--' . sanitize_html_class( $settings['layout'] ),
			'emm-shadow--' . sanitize_html_class( $settings['shadow'] ),
			'emm-nav-align--' . sanitize_html_class( $settings['nav_align'] ),
			'emm-cta--' . sanitize_html_class( $settings['cta_style'] ),
			'emm-title--' . sanitize_html_class( $settings['panel_title_align'] ),
		);
		if ( ! empty( $settings['uppercase_cats'] ) ) {
			$classes[] = 'emm-cats-upper';
		}
		if ( empty( $settings['show_cat_desc'] ) ) {
			$classes[] = 'emm-hide-cat-desc';
		}
		if ( ! empty( $settings['full_width'] ) ) {
			$classes[] = 'emm-full-width';
		}

		ob_start();
		?>
		<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $this->css_vars( $settings ) ); ?>" data-emm-id="<?php echo esc_attr( $menu_id ); ?>">
			<nav class="emm-nav" aria-label="<?php echo esc_attr( $menu['title'] ); ?>">
				<div class="emm-nav__bar">
					<button type="button" class="emm-nav__toggle" aria-expanded="false" aria-controls="emm-nav-menu-<?php echo esc_attr( $menu_id ); ?>" aria-label="<?php esc_attr_e( 'Open menu', 'easy-mega-menu' ); ?>">
						<span class="emm-nav__toggle-box" aria-hidden="true">
							<span class="emm-nav__toggle-bar"></span>
						</span>
					</button>

					<?php if ( ! empty( $cta['show'] ) && ! empty( $cta['label'] ) ) : ?>
						<a class="emm-nav__cta emm-nav__cta--bar" href="<?php echo esc_url( $cta['url'] ?: '#' ); ?>">
							<?php echo esc_html( $cta['label'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="emm-nav__drawer" id="emm-nav-menu-<?php echo esc_attr( $menu_id ); ?>">
					<div class="emm-nav__drawer-head">
						<span class="emm-nav__drawer-title"><?php esc_html_e( 'Menu', 'easy-mega-menu' ); ?></span>
						<button type="button" class="emm-nav__drawer-close" data-emm-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'easy-mega-menu' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="emm-nav__inner">
						<ul class="emm-nav__list">
							<?php foreach ( ( $menu['items'] ?? array() ) as $item ) : ?>
								<?php echo $this->render_nav_item( $item, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endforeach; ?>
						</ul>

						<?php if ( ! empty( $cta['show'] ) && ! empty( $cta['label'] ) ) : ?>
							<a class="emm-nav__cta emm-nav__cta--drawer" href="<?php echo esc_url( $cta['url'] ?: '#' ); ?>">
								<?php echo esc_html( $cta['label'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="emm-nav__backdrop" data-emm-backdrop hidden></div>
			</nav>
		</header>
		<?php
		return ob_get_clean();
	}

	/**
	 * Build CSS custom properties string.
	 *
	 * @param array $settings Settings.
	 * @return string
	 */
	private function css_vars( $settings ) {
		$s = wp_parse_args( $settings, EMM_Data::default_settings() );
		return sprintf(
			'--emm-sidebar-bg:%1$s;--emm-active-bg:%2$s;--emm-panel-bg:%3$s;--emm-header-bg:%4$s;--emm-accent:%5$s;--emm-text:%6$s;--emm-nav-link:%7$s;--emm-nav-hover:%8$s;--emm-muted:%9$s;--emm-border:%10$s;--emm-cta-text:%11$s;--emm-grid-cols:%12$d;--emm-panel-width:%13$dpx;--emm-sidebar-width:%14$dpx;--emm-radius:%15$dpx;',
			$s['sidebar_bg'],
			$s['active_bg'],
			$s['panel_bg'],
			$s['header_bg'],
			$s['accent'],
			$s['text_color'],
			$s['nav_link_color'] ?? $s['text_color'],
			$s['nav_hover_color'] ?? $s['accent'],
			$s['muted_color'],
			$s['border_color'],
			$s['cta_text'],
			(int) $s['grid_columns'],
			(int) $s['panel_width'],
			(int) $s['sidebar_width'],
			(int) $s['border_radius']
		);
	}

	/**
	 * Render one top-level nav item (link or mega).
	 *
	 * @param array $item     Item data.
	 * @param array $settings Menu settings.
	 * @return string
	 */
	private function render_nav_item( $item, $settings ) {
		$is_mega = ( 'mega' === ( $item['type'] ?? '' ) );
		$classes = 'emm-nav__item' . ( $is_mega ? ' emm-nav__item--mega' : '' );
		$uid     = esc_attr( $item['id'] ?? uniqid( 'i' ) );

		ob_start();
		?>
		<li class="<?php echo esc_attr( $classes ); ?>">
			<?php if ( $is_mega ) : ?>
				<button
					type="button"
					class="emm-nav__link emm-nav__link--toggle"
					aria-expanded="false"
					aria-haspopup="true"
					aria-controls="emm-panel-<?php echo $uid; ?>"
					data-emm-mega-trigger
				>
					<?php echo esc_html( $item['label'] ); ?>
					<svg class="emm-chevron" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5L6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<?php echo $this->render_mega_panel( $item, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<a class="emm-nav__link" href="<?php echo esc_url( $item['url'] ?: '#' ); ?>">
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endif; ?>
		</li>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render mega dropdown panel (sidebar + grid).
	 *
	 * @param array $item     Mega item.
	 * @param array $settings Settings.
	 * @return string
	 */
	private function render_mega_panel( $item, $settings ) {
		$style = $item['mega_style'] ?? 'platforms';
		if ( 'features' === $style ) {
			return $this->render_features_panel( $item, $settings );
		}
		return $this->render_platforms_panel( $item, $settings );
	}

	/**
	 * Platforms mega: left sidebar categories + right feature grid.
	 *
	 * @param array $item     Mega item.
	 * @param array $settings Settings.
	 * @return string
	 */
	private function render_platforms_panel( $item, $settings ) {
		$categories = $item['categories'] ?? array();
		$uid        = esc_attr( $item['id'] ?? uniqid( 'p' ) );
		$icons      = EMM_Icons::instance();

		ob_start();
		?>
		<div class="emm-mega emm-mega--platforms" id="emm-panel-<?php echo $uid; ?>" hidden data-emm-mega-panel data-emm-style="platforms">
			<div class="emm-mega__inner">
				<?php if ( ! empty( $categories ) ) : ?>
					<div class="emm-mega__accordion">
						<?php foreach ( $categories as $index => $cat ) : ?>
							<?php
							$cat_id = esc_attr( $cat['id'] ?? 'cat' . $index );
							$active = 0 === $index;
							?>
							<section class="emm-mega__section<?php echo $active ? ' is-open' : ''; ?>" data-emm-section="<?php echo $cat_id; ?>">
								<div
									class="emm-mega__cat<?php echo $active ? ' is-active' : ''; ?>"
									id="emm-tab-<?php echo $cat_id; ?>"
									role="button"
									tabindex="0"
									aria-expanded="<?php echo $active ? 'true' : 'false'; ?>"
									aria-controls="emm-panel-content-<?php echo $cat_id; ?>"
									data-emm-cat="<?php echo $cat_id; ?>"
								>
									<span class="emm-mega__cat-icon">
										<?php echo $icons->render( $cat['icon'] ?? '', $cat['icon_url'] ?? '', 'emm-icon emm-icon--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
									<span class="emm-mega__cat-text">
										<span class="emm-mega__cat-title"><?php echo esc_html( $cat['title'] ); ?></span>
										<?php if ( ! empty( $cat['description'] ) ) : ?>
											<span class="emm-mega__cat-desc"><?php echo esc_html( $cat['description'] ); ?></span>
										<?php endif; ?>
									</span>
									<span class="emm-mega__cat-chevron" aria-hidden="true">
										<svg width="14" height="14" viewBox="0 0 12 12"><path d="M2.5 4.5L6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</span>
								</div>

								<div
									class="emm-mega__panel<?php echo $active ? ' is-active' : ''; ?>"
									id="emm-panel-content-<?php echo $cat_id; ?>"
									role="region"
									aria-labelledby="emm-tab-<?php echo $cat_id; ?>"
									data-emm-panel="<?php echo $cat_id; ?>"
									<?php echo $active ? '' : 'hidden'; ?>
								>
									<?php if ( ! empty( $cat['panel_title'] ) ) : ?>
										<h3 class="emm-mega__panel-title">
											<?php if ( ! empty( $cat['panel_url'] ) ) : ?>
												<a href="<?php echo esc_url( $cat['panel_url'] ); ?>"><?php echo esc_html( $cat['panel_title'] ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $cat['panel_title'] ); ?>
											<?php endif; ?>
										</h3>
									<?php endif; ?>

									<?php echo $this->render_category_columns( $cat ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</section>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Normalize category groups (columns), including legacy flat links.
	 *
	 * @param array $cat Category data.
	 * @return array
	 */
	private function category_groups( $cat ) {
		if ( ! empty( $cat['groups'] ) && is_array( $cat['groups'] ) ) {
			return $cat['groups'];
		}

		if ( ! empty( $cat['links'] ) && is_array( $cat['links'] ) ) {
			return array(
				array(
					'id'    => 'legacy',
					'title' => '',
					'links' => $cat['links'],
				),
			);
		}

		return array();
	}

	/**
	 * Render column groups inside a category panel.
	 *
	 * @param array $cat Category data.
	 * @return string
	 */
	private function render_category_columns( $cat ) {
		$groups = $this->category_groups( $cat );
		if ( empty( $groups ) ) {
			return '';
		}

		$count = count( $groups );
		ob_start();
		?>
		<div class="emm-mega__columns" style="--emm-col-count: <?php echo (int) $count; ?>">
			<?php foreach ( $groups as $group ) : ?>
				<div class="emm-mega__col">
					<?php if ( ! empty( $group['title'] ) ) : ?>
						<h4 class="emm-mega__col-title">
							<?php if ( ! empty( $group['url'] ) ) : ?>
								<a href="<?php echo esc_url( $group['url'] ); ?>"><?php echo esc_html( $group['title'] ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $group['title'] ); ?>
							<?php endif; ?>
						</h4>
					<?php endif; ?>
					<ul class="emm-mega__col-list">
						<?php foreach ( ( $group['links'] ?? array() ) as $link ) : ?>
							<?php echo $this->render_col_link( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render one column list link.
	 *
	 * @param array $link Link data.
	 * @return string
	 */
	private function render_col_link( $link ) {
		$icons     = EMM_Icons::instance();
		$has_icon  = ! empty( $link['icon'] ) || ! empty( $link['icon_url'] );
		ob_start();
		?>
		<li class="emm-mega__col-item">
			<a class="emm-mega__col-link<?php echo $has_icon ? ' has-icon' : ''; ?>" href="<?php echo esc_url( $link['url'] ?: '#' ); ?>">
				<?php if ( $has_icon ) : ?>
					<span class="emm-mega__col-icon">
						<?php echo $icons->render( $link['icon'] ?? '', $link['icon_url'] ?? '', 'emm-icon emm-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				<?php endif; ?>
				<span class="emm-mega__col-label"><?php echo esc_html( $link['label'] ); ?></span>
			</a>
		</li>
		<?php
		return ob_get_clean();
	}

	/**
	 * Features mega: simple multi-column icon + label grid (no sidebar).
	 *
	 * @param array $item     Mega item.
	 * @param array $settings Settings.
	 * @return string
	 */
	private function render_features_panel( $item, $settings ) {
		$links = $item['links'] ?? array();
		$uid   = esc_attr( $item['id'] ?? uniqid( 'p' ) );
		$cols  = max( 1, (int) ( $item['columns'] ?? 2 ) );

		ob_start();
		?>
		<div class="emm-mega emm-mega--features" id="emm-panel-<?php echo $uid; ?>" hidden data-emm-mega-panel data-emm-style="features">
			<div class="emm-mega__inner emm-mega__inner--features">
				<ul class="emm-mega__grid emm-mega__grid--features" style="--emm-grid-cols: <?php echo (int) $cols; ?>">
					<?php foreach ( $links as $link ) : ?>
						<?php echo $this->render_grid_link( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render one grid link row.
	 *
	 * @param array $link Link data.
	 * @return string
	 */
	private function render_grid_link( $link ) {
		$icons = EMM_Icons::instance();
		ob_start();
		?>
		<li class="emm-mega__grid-item">
			<a class="emm-mega__grid-link" href="<?php echo esc_url( $link['url'] ?: '#' ); ?>">
				<span class="emm-mega__grid-icon">
					<?php echo $icons->render( $link['icon'] ?? '', $link['icon_url'] ?? '', 'emm-icon emm-icon--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<span class="emm-mega__grid-label"><?php echo esc_html( $link['label'] ); ?></span>
			</a>
		</li>
		<?php
		return ob_get_clean();
	}
}