<?php
/**
 * WordPress Widget: Easy Mega Menu.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EMM_Widget
 *
 * Displays a mega menu in any widget area.
 */
class EMM_Widget extends WP_Widget {

	/**
	 * Sets up the widget.
	 */
	public function __construct() {
		parent::__construct(
			'emm_widget',
			__( 'Easy Mega Menu', 'easy-mega-menu' ),
			array(
				'description' => __( 'Display a mega menu in any widget area.', 'easy-mega-menu' ),
				'classname'   => 'emm-widget',
			)
		);
	}

	/**
	 * Outputs the widget content on the frontend.
	 *
	 * @param array $args     Display arguments.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$menu_id = ! empty( $instance['menu_id'] ) ? $instance['menu_id'] : '';

		if ( ! $menu_id ) {
			return;
		}

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo EMM_Frontend::instance()->render( $menu_id );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form in the admin Widgets screen.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$menu_id = ! empty( $instance['menu_id'] ) ? $instance['menu_id'] : '';
		$menus   = EMM_Data::instance()->get_all();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'easy-mega-menu' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'menu_id' ) ); ?>">
				<?php esc_html_e( 'Mega Menu:', 'easy-mega-menu' ); ?>
			</label>
			<select
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'menu_id' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'menu_id' ) ); ?>"
			>
				<option value=""><?php esc_html_e( '— Select a menu —', 'easy-mega-menu' ); ?></option>
				<?php foreach ( $menus as $id => $menu ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $menu_id, $id ); ?>>
						<?php echo esc_html( $menu['title'] ?: $id ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Previous settings.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']   = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['menu_id'] = sanitize_text_field( $new_instance['menu_id'] ?? '' );

		return $instance;
	}
}

/**
 * Register the widget.
 */
function emm_register_widget() {
	register_widget( 'EMM_Widget' );
}
add_action( 'widgets_init', 'emm_register_widget' );
