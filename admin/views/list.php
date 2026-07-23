<?php
/**
 * Admin: menu list view.
 *
 * @package Easy_Mega_Menu
 *
 * @var array $menus All menus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap emm-wrap">
	<div class="emm-admin-header">
		<div>
			<h1><?php esc_html_e( 'Easy Mega Menu', 'easy-mega-menu' ); ?></h1>
			<p class="emm-admin-intro"><?php esc_html_e( 'Build professional mega menus visually — no coding needed. Edit a menu, then place it on your site with a shortcode.', 'easy-mega-menu' ); ?></p>
		</div>
		<button type="button" class="button button-primary button-hero emm-create-menu">
			<span class="dashicons dashicons-plus-alt2" style="margin-top:4px"></span>
			<?php esc_html_e( 'Create New Menu', 'easy-mega-menu' ); ?>
		</button>
	</div>

	<?php if ( empty( $menus ) ) : ?>
		<div class="emm-empty-state">
			<div class="emm-empty-state__icon">☰</div>
			<h2><?php esc_html_e( 'No menus yet', 'easy-mega-menu' ); ?></h2>
			<p><?php esc_html_e( 'Create your first mega menu to get started. A demo menu is available after activating the plugin.', 'easy-mega-menu' ); ?></p>
			<button type="button" class="button button-primary emm-create-menu"><?php esc_html_e( 'Create New Menu', 'easy-mega-menu' ); ?></button>
		</div>
	<?php else : ?>
		<div class="emm-menu-cards">
			<?php foreach ( $menus as $id => $menu ) : ?>
				<div class="emm-menu-card" data-menu-id="<?php echo esc_attr( $id ); ?>">
					<div class="emm-menu-card__body">
						<h2 class="emm-menu-card__title"><?php echo esc_html( $menu['title'] ?: __( 'Untitled', 'easy-mega-menu' ) ); ?></h2>
						<p class="emm-menu-card__meta">
							<?php
							$count = count( $menu['items'] ?? array() );
							printf(
								/* translators: %d: number of top-level items */
								esc_html( _n( '%d menu item', '%d menu items', $count, 'easy-mega-menu' ) ),
								(int) $count
							);
							?>
						</p>
						<div class="emm-menu-card__shortcode">
							<code>[easy_mega_menu id="<?php echo esc_attr( $id ); ?>"]</code>
							<button type="button" class="button button-small emm-copy-shortcode" data-shortcode='[easy_mega_menu id="<?php echo esc_attr( $id ); ?>"]' title="<?php esc_attr_e( 'Copy shortcode', 'easy-mega-menu' ); ?>">
								<?php esc_html_e( 'Copy', 'easy-mega-menu' ); ?>
							</button>
						</div>
					</div>
					<div class="emm-menu-card__actions">
						<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=easy-mega-menu&edit=' . rawurlencode( $id ) ) ); ?>">
							<?php esc_html_e( 'Edit Menu', 'easy-mega-menu' ); ?>
						</a>
						<button type="button" class="button emm-delete-menu" data-menu-id="<?php echo esc_attr( $id ); ?>">
							<?php esc_html_e( 'Delete', 'easy-mega-menu' ); ?>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="emm-help-box">
		<h3><?php esc_html_e( 'How to use', 'easy-mega-menu' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'Create or edit a menu in this panel.', 'easy-mega-menu' ); ?></li>
			<li><?php esc_html_e( 'Set a top-level item to “Mega Menu”, then pick “With category bar” or “Simple columns (no category bar)”.', 'easy-mega-menu' ); ?></li>
			<li><?php esc_html_e( 'Copy the shortcode and paste it into a page, header template, or widget.', 'easy-mega-menu' ); ?></li>
			<li><?php esc_html_e( 'Or add this in your theme: <?php emm_render_menu( \'menu_demo\' ); ?>', 'easy-mega-menu' ); ?></li>
		</ol>
	</div>
</div>
