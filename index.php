<?php

/**
 * Plugin Name: Profile widget
 * Plugin URI: https://geek.hellyer.kiwi/products/profile-widget/
 * Description: A widget for displaying user profile information on single post pages
 * Version: 1.1
 * Author: Ryan Hellyer
 * Author URI: https://geek.hellyer.kiwi/
 *
 * The implementation of widget code is based on work by Justin Tadlock
 * http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */


/**
 * Register the widget
 *
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function profile_widget_load_widgets() {
	register_widget( 'Profile_Widget' );
}
add_action( 'widgets_init', 'profile_widget_load_widgets' );

/**
 * Adding multilingual support
 *
 * @since 1.1
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function profile_widget_textdomain() {
	// Localization
	load_plugin_textdomain(
		'profile-widget', // Unique identifier
		false, // Deprecated abs path
		dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
	);
}
add_action( 'admin_init', 'profile_widget_textdomain' );

/**
 * Profile Widget class.
 *
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Profile_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function __construct() {

		// Widget settings.
		$widget_ops = array( 'classname' => 'profile-widget', 'description' => __( 'Widget for displaying user biographical information', 'profile-widget' ) );

		// Widget control settings.
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'profwid-widget' );

		// Create the widget.
		$this->WP_Widget(
			'profwid-widget',
			__( 'Profile Widget', 'profile-widget' ),
			$widget_ops, $control_ops
		);

	}

	/**
	 * How to display the widget on the screen.
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @global int $post
	 * @param array $args     Contains the arguments, but is disused here
	 * @param array $instance Contains the widget settings
	 */
	public function widget( $args, $instance ) {
		global $post;
		extract( $args );

		// Check if widget should be displayed here (contains additional logic to allow for overriding via themes and other plugins)
		if ( ! is_single() )
			$return = true;
		$return = apply_filters( 'profile_widget_confirm_display', $return ); // Used to modify result via plugins
		if ( true == $return )
			return;

		// Set the title
		$title = apply_filters( 'widget_title', $instance['title'] );

		// Display before widget code
		echo $before_widget;

		// Display the widget title
		if ( $title )
			echo $before_title . $title . $after_title;

		echo get_avatar( $post->post_author, $instance['size'] );
		$description = get_the_author_meta( 'description', $post->post_author );
		$description = wptexturize( $description ); // Apply texturising filter - not using the_content() due to it causing things like share icons to be displayed in the sidebar
		$description = wpautop( $description ); // Apply auto paragraph filter - not using the_content() due to it causing things like share icons to be displayed in the sidebar
		echo $description;

		// Display after widget code
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 * Sanitise data inputs
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function update( $input, $old ) {

		// Sanitise data input
		$output['title'] = wp_kses( $input['title'], '', '' );
		$output['size'] = (int) $input['size'];

		return $output;
	}

	/**
	 * Displays the form on the widget page
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function form( $instance ) {

		// Set up some default widget settings
		$defaults = array(
			'title' => __( 'Profile', 'profile-widget'),
			'size'  => '120',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'profile-widget' ); ?>:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php _e( 'Image Size', 'profile-widget' ); ?>:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" value="<?php echo esc_attr( $instance['size'] ); ?>" style="width:100%;" />
		</p><?php
	}
}
