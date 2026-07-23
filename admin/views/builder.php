<?php
/**
 * Admin: visual mega menu builder.
 *
 * @package Easy_Mega_Menu
 *
 * @var string $edit_id Menu ID being edited.
 * @var array  $menus   All menus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$menu = $menus[ $edit_id ];
$menu['settings'] = wp_parse_args( $menu['settings'] ?? array(), EMM_Data::default_settings() );
?>
<div class="wrap emm-wrap emm-builder-wrap">
	<div class="emm-builder-topbar">
		<a class="emm-back" href="<?php echo esc_url( admin_url( 'admin.php?page=easy-mega-menu' ) ); ?>">
			← <?php esc_html_e( 'All Menus', 'easy-mega-menu' ); ?>
		</a>
		<div class="emm-builder-topbar__center">
			<label class="screen-reader-text" for="emm-menu-title"><?php esc_html_e( 'Menu name', 'easy-mega-menu' ); ?></label>
			<input type="text" id="emm-menu-title" class="emm-menu-title-input" value="<?php echo esc_attr( $menu['title'] ); ?>" placeholder="<?php esc_attr_e( 'Menu name', 'easy-mega-menu' ); ?>" />
		</div>
		<div class="emm-builder-topbar__actions">
			<span class="emm-save-status" aria-live="polite"></span>
			<button type="button" class="button button-primary button-hero emm-save-menu">
				<?php esc_html_e( 'Save Menu', 'easy-mega-menu' ); ?>
			</button>
		</div>
	</div>

	<div class="emm-builder" id="emm-builder"
		data-menu-id="<?php echo esc_attr( $edit_id ); ?>"
		data-menu="<?php echo esc_attr( wp_json_encode( $menu ) ); ?>">

		<aside class="emm-builder__sidebar">
			<section class="emm-builder-panel">
				<h2><?php esc_html_e( 'Top Menu Items', 'easy-mega-menu' ); ?></h2>
				<p class="description"><?php esc_html_e( 'These appear in the header bar. Drag to reorder.', 'easy-mega-menu' ); ?></p>
				<ul class="emm-sortable emm-nav-items" id="emm-nav-items"></ul>
				<button type="button" class="button emm-add-nav-item">
					+ <?php esc_html_e( 'Add Menu Item', 'easy-mega-menu' ); ?>
				</button>
			</section>

			<section class="emm-builder-panel">
				<h2><?php esc_html_e( 'Call-to-Action Button', 'easy-mega-menu' ); ?></h2>
				<label class="emm-field">
					<span><?php esc_html_e( 'Show button', 'easy-mega-menu' ); ?></span>
					<input type="checkbox" id="emm-cta-show" <?php checked( ! empty( $menu['cta']['show'] ) ); ?> />
				</label>
				<label class="emm-field">
					<span><?php esc_html_e( 'Button text', 'easy-mega-menu' ); ?></span>
					<input type="text" id="emm-cta-label" value="<?php echo esc_attr( $menu['cta']['label'] ?? '' ); ?>" />
				</label>
				<label class="emm-field">
					<span><?php esc_html_e( 'Button URL', 'easy-mega-menu' ); ?></span>
					<span class="emm-wp-link">
						<input type="text" id="emm-cta-url" class="emm-url-field" value="<?php echo esc_attr( $menu['cta']['url'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Search or type URL', 'easy-mega-menu' ); ?>" />
						<button type="button" class="button emm-pick-url" title="<?php esc_attr_e( 'Select / edit link', 'easy-mega-menu' ); ?>" data-emm-url="#emm-cta-url" data-emm-text="#emm-cta-label">
							<span class="dashicons dashicons-admin-links" aria-hidden="true"></span>
						</button>
					</span>
				</label>
			</section>

			<section class="emm-builder-panel emm-design-panel">
				<h2><?php esc_html_e( 'Design', 'easy-mega-menu' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Pick a preset, then fine-tune colors and layout. Preview updates live.', 'easy-mega-menu' ); ?></p>

				<?php
				$settings = wp_parse_args( $menu['settings'] ?? array(), EMM_Data::default_settings() );
				$presets  = EMM_Data::design_presets();
				?>

				<div class="emm-field">
					<span><?php esc_html_e( 'Style preset', 'easy-mega-menu' ); ?></span>
					<div class="emm-preset-grid" id="emm-preset-grid">
						<?php foreach ( $presets as $key => $preset ) : ?>
							<?php $v = $preset['values']; ?>
							<button
								type="button"
								class="emm-preset-card<?php echo ( $settings['preset'] === $key ) ? ' is-active' : ''; ?>"
								data-preset="<?php echo esc_attr( $key ); ?>"
								data-values="<?php echo esc_attr( wp_json_encode( $v ) ); ?>"
								title="<?php echo esc_attr( $preset['label'] ); ?>"
							>
								<span class="emm-preset-card__swatches" aria-hidden="true">
									<span style="background:<?php echo esc_attr( $v['header_bg'] ); ?>"></span>
									<span style="background:<?php echo esc_attr( $v['sidebar_bg'] ); ?>"></span>
									<span style="background:<?php echo esc_attr( $v['accent'] ); ?>"></span>
								</span>
								<span class="emm-preset-card__label"><?php echo esc_html( $preset['label'] ); ?></span>
							</button>
						<?php endforeach; ?>
						<button type="button" class="emm-preset-card<?php echo ( 'custom' === $settings['preset'] ) ? ' is-active' : ''; ?>" data-preset="custom" disabled>
							<span class="emm-preset-card__swatches emm-preset-card__swatches--custom" aria-hidden="true">
								<span></span><span></span><span></span>
							</span>
							<span class="emm-preset-card__label"><?php esc_html_e( 'Custom', 'easy-mega-menu' ); ?></span>
						</button>
					</div>
					<input type="hidden" id="emm-preset" value="<?php echo esc_attr( $settings['preset'] ); ?>" />
				</div>

				<label class="emm-field">
					<span><?php esc_html_e( 'Category bar position', 'easy-mega-menu' ); ?></span>
					<select id="emm-layout">
						<option value="sidebar-left" <?php selected( $settings['layout'], 'sidebar-left' ); ?>><?php esc_html_e( 'Left (classic Platforms)', 'easy-mega-menu' ); ?></option>
						<option value="sidebar-right" <?php selected( $settings['layout'], 'sidebar-right' ); ?>><?php esc_html_e( 'Right', 'easy-mega-menu' ); ?></option>
						<option value="stacked" <?php selected( $settings['layout'], 'stacked' ); ?>><?php esc_html_e( 'Top (horizontal tabs)', 'easy-mega-menu' ); ?></option>
					</select>
					<span class="description"><?php esc_html_e( 'Only applies to Platforms mega items. For a simple 2/3-column mega with no category bar, open Edit Mega Content and choose “Simple columns”.', 'easy-mega-menu' ); ?></span>
				</label>

				<label class="emm-field">
					<span><?php esc_html_e( 'Nav link alignment', 'easy-mega-menu' ); ?></span>
					<select id="emm-nav-align">
						<option value="left" <?php selected( $settings['nav_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'easy-mega-menu' ); ?></option>
						<option value="center" <?php selected( $settings['nav_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'easy-mega-menu' ); ?></option>
						<option value="right" <?php selected( $settings['nav_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'easy-mega-menu' ); ?></option>
					</select>
				</label>

				<label class="emm-field">
					<span><?php esc_html_e( 'Default grid columns', 'easy-mega-menu' ); ?></span>
					<select id="emm-grid-cols">
						<?php for ( $i = 2; $i <= 4; $i++ ) : ?>
							<option value="<?php echo (int) $i; ?>" <?php selected( (int) $settings['grid_columns'], $i ); ?>><?php echo (int) $i; ?></option>
						<?php endfor; ?>
					</select>
					<span class="description"><?php esc_html_e( 'Fallback default. Each mega item can override columns in Edit Mega Content.', 'easy-mega-menu' ); ?></span>
				</label>

				<details class="emm-design-more" open>
					<summary><?php esc_html_e( 'Top menu item colors', 'easy-mega-menu' ); ?></summary>
					<label class="emm-field">
						<span><?php esc_html_e( 'Menu link color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-nav-link-color" value="<?php echo esc_attr( $settings['nav_link_color'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Menu hover / open color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-nav-hover-color" value="<?php echo esc_attr( $settings['nav_hover_color'] ); ?>" />
					</label>
				</details>

				<details class="emm-design-more" open>
					<summary><?php esc_html_e( 'Panel & brand colors', 'easy-mega-menu' ); ?></summary>
					<label class="emm-field">
						<span><?php esc_html_e( 'Header background', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-header-bg" value="<?php echo esc_attr( $settings['header_bg'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Sidebar background', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-sidebar-bg" value="<?php echo esc_attr( $settings['sidebar_bg'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Active category background', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-active-bg" value="<?php echo esc_attr( $settings['active_bg'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Panel background', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-panel-bg" value="<?php echo esc_attr( $settings['panel_bg'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Accent / button color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-accent" value="<?php echo esc_attr( $settings['accent'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Button text color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-cta-text" value="<?php echo esc_attr( $settings['cta_text'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Panel text color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-text-color" value="<?php echo esc_attr( $settings['text_color'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Muted / description color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-muted-color" value="<?php echo esc_attr( $settings['muted_color'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Border color', 'easy-mega-menu' ); ?></span>
						<input type="text" class="emm-color" id="emm-border-color" value="<?php echo esc_attr( $settings['border_color'] ); ?>" />
					</label>
				</details>

				<details class="emm-design-more">
					<summary><?php esc_html_e( 'Layout & style', 'easy-mega-menu' ); ?></summary>
					<label class="emm-field emm-field--toggle">
						<span><?php esc_html_e( 'Full width mega menu', 'easy-mega-menu' ); ?></span>
						<input type="checkbox" id="emm-full-width" <?php checked( ! empty( $settings['full_width'] ) ); ?> />
					</label>
					<label class="emm-field" id="emm-panel-width-field">
						<span><?php esc_html_e( 'Panel width (px)', 'easy-mega-menu' ); ?></span>
						<input type="number" id="emm-panel-width" min="640" max="1400" step="20" value="<?php echo esc_attr( $settings['panel_width'] ); ?>" <?php disabled( ! empty( $settings['full_width'] ) ); ?> />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Sidebar width (px)', 'easy-mega-menu' ); ?></span>
						<input type="number" id="emm-sidebar-width" min="180" max="420" step="10" value="<?php echo esc_attr( $settings['sidebar_width'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Corner radius (px)', 'easy-mega-menu' ); ?></span>
						<input type="number" id="emm-border-radius" min="0" max="24" step="1" value="<?php echo esc_attr( $settings['border_radius'] ); ?>" />
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Shadow', 'easy-mega-menu' ); ?></span>
						<select id="emm-shadow">
							<option value="none" <?php selected( $settings['shadow'], 'none' ); ?>><?php esc_html_e( 'None', 'easy-mega-menu' ); ?></option>
							<option value="soft" <?php selected( $settings['shadow'], 'soft' ); ?>><?php esc_html_e( 'Soft', 'easy-mega-menu' ); ?></option>
							<option value="medium" <?php selected( $settings['shadow'], 'medium' ); ?>><?php esc_html_e( 'Medium', 'easy-mega-menu' ); ?></option>
							<option value="strong" <?php selected( $settings['shadow'], 'strong' ); ?>><?php esc_html_e( 'Strong', 'easy-mega-menu' ); ?></option>
						</select>
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'CTA button shape', 'easy-mega-menu' ); ?></span>
						<select id="emm-cta-style">
							<option value="square" <?php selected( $settings['cta_style'], 'square' ); ?>><?php esc_html_e( 'Square', 'easy-mega-menu' ); ?></option>
							<option value="rounded" <?php selected( $settings['cta_style'], 'rounded' ); ?>><?php esc_html_e( 'Rounded', 'easy-mega-menu' ); ?></option>
							<option value="pill" <?php selected( $settings['cta_style'], 'pill' ); ?>><?php esc_html_e( 'Pill', 'easy-mega-menu' ); ?></option>
						</select>
					</label>
					<label class="emm-field">
						<span><?php esc_html_e( 'Panel title alignment', 'easy-mega-menu' ); ?></span>
						<select id="emm-panel-title-align">
							<option value="left" <?php selected( $settings['panel_title_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'easy-mega-menu' ); ?></option>
							<option value="center" <?php selected( $settings['panel_title_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'easy-mega-menu' ); ?></option>
						</select>
					</label>
					<label class="emm-field emm-field--toggle">
						<span><?php esc_html_e( 'Uppercase category titles', 'easy-mega-menu' ); ?></span>
						<input type="checkbox" id="emm-uppercase-cats" <?php checked( ! empty( $settings['uppercase_cats'] ) ); ?> />
					</label>
					<label class="emm-field emm-field--toggle">
						<span><?php esc_html_e( 'Show category descriptions', 'easy-mega-menu' ); ?></span>
						<input type="checkbox" id="emm-show-cat-desc" <?php checked( ! empty( $settings['show_cat_desc'] ) ); ?> />
					</label>
				</details>
			</section>

			<section class="emm-builder-panel emm-builder-panel--hint">
				<h2><?php esc_html_e( 'Shortcode', 'easy-mega-menu' ); ?></h2>
				<code class="emm-shortcode-block">[easy_mega_menu id="<?php echo esc_attr( $edit_id ); ?>"]</code>
			</section>
		</aside>

		<main class="emm-builder__main">
			<div class="emm-editor-chrome">
				<p class="emm-editor-chrome__label"><?php esc_html_e( 'Live Preview — click items below to edit', 'easy-mega-menu' ); ?></p>
			</div>

			<!-- Preview + inline editor -->
			<div class="emm-preview" id="emm-preview"></div>

			<!-- Detail editor for selected mega item -->
			<div class="emm-mega-editor" id="emm-mega-editor" hidden>
				<div class="emm-mega-editor__header">
					<h2 id="emm-mega-editor-title"><?php esc_html_e( 'Edit Mega Menu', 'easy-mega-menu' ); ?></h2>
					<button type="button" class="button emm-close-mega-editor"><?php esc_html_e( 'Done', 'easy-mega-menu' ); ?></button>
				</div>

				<div class="emm-mega-style-picker" id="emm-mega-style-picker">
					<p class="emm-mega-style-picker__label"><?php esc_html_e( 'Choose layout for this mega menu', 'easy-mega-menu' ); ?></p>
					<div class="emm-mega-style-cards">
						<button type="button" class="emm-mega-style-card" data-style="platforms">
							<span class="emm-mega-style-card__preview emm-mega-style-card__preview--platforms" aria-hidden="true">
								<span></span><span></span>
							</span>
							<strong><?php esc_html_e( 'With category bar', 'easy-mega-menu' ); ?></strong>
							<small><?php esc_html_e( 'Left/right sidebar categories (SPEND, SIGN…) + links grid', 'easy-mega-menu' ); ?></small>
						</button>
						<button type="button" class="emm-mega-style-card" data-style="features">
							<span class="emm-mega-style-card__preview emm-mega-style-card__preview--features" aria-hidden="true">
								<span></span><span></span><span></span><span></span>
							</span>
							<strong><?php esc_html_e( 'Simple columns (no category bar)', 'easy-mega-menu' ); ?></strong>
							<small><?php esc_html_e( 'Just icon + links in 2, 3, or 4 columns — like a Features mega menu', 'easy-mega-menu' ); ?></small>
						</button>
					</div>
					<label class="emm-field emm-mega-columns-field">
						<span><?php esc_html_e( 'Columns for this mega menu', 'easy-mega-menu' ); ?></span>
						<select id="emm-mega-item-columns">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
						</select>
					</label>
				</div>

				<!-- Platforms layout editor -->
				<div class="emm-mega-editor__layout" id="emm-editor-platforms">
					<div class="emm-mega-editor__cats">
						<h3><?php esc_html_e( 'Left Sidebar Categories', 'easy-mega-menu' ); ?></h3>
						<p class="description"><?php esc_html_e( 'Like SPEND, SIGN, HR — icon + title + description.', 'easy-mega-menu' ); ?></p>
						<ul class="emm-sortable emm-cat-list" id="emm-cat-list"></ul>
						<button type="button" class="button emm-add-category">+ <?php esc_html_e( 'Add Category', 'easy-mega-menu' ); ?></button>
					</div>

					<div class="emm-mega-editor__cat-detail" id="emm-cat-detail">
						<p class="emm-placeholder"><?php esc_html_e( 'Select a category on the left to edit its columns and links.', 'easy-mega-menu' ); ?></p>
					</div>
				</div>

				<!-- Features grid layout editor -->
				<div class="emm-mega-editor__features" id="emm-editor-features" hidden>
					<h3><?php esc_html_e( 'Feature Links', 'easy-mega-menu' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Add icon + label links. They appear in a simple column grid like a Features mega menu.', 'easy-mega-menu' ); ?></p>
					<ul class="emm-sortable emm-link-list" id="emm-features-link-list"></ul>
					<button type="button" class="button emm-add-feature-link">+ <?php esc_html_e( 'Add Link', 'easy-mega-menu' ); ?></button>
				</div>
			</div>
		</main>
	</div>
</div>

<div id="emm-icon-modal" class="emm-modal" hidden>
	<div class="emm-modal__backdrop"></div>
	<div class="emm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="emm-icon-modal-title">
		<div class="emm-modal__header">
			<h2 id="emm-icon-modal-title"><?php esc_html_e( 'Choose an icon', 'easy-mega-menu' ); ?></h2>
			<button type="button" class="emm-modal__close" aria-label="<?php esc_attr_e( 'Close', 'easy-mega-menu' ); ?>">&times;</button>
		</div>
		<div class="emm-modal__body">
			<div class="emm-icon-grid" id="emm-icon-grid"></div>
			<div class="emm-icon-upload">
				<button type="button" class="button emm-upload-icon"><?php esc_html_e( 'Upload custom icon', 'easy-mega-menu' ); ?></button>
				<button type="button" class="button-link emm-clear-icon"><?php esc_html_e( 'Clear icon', 'easy-mega-menu' ); ?></button>
			</div>
		</div>
	</div>
</div>
