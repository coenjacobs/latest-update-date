<?php

/*
Plugin Name: Latest Update Date
Version: 1.0-beta1
Description: Will show the date that the site has been updates for the last time (in the footer and optionally in posts/pages via shortcode).
Author: Coen Jacobs
Author URI: http://coenjacobs.me/
*/

// We don't need this in backend, so we only make a new instance of this class on frontend
if ( ! is_admin() ) {
	global $cj_latest_update_date;
	$cj_latest_update_date = new CJ_Latest_Update_Date();
}

/**
 * Main plugin class
 *
 * @since 1.0
 */
class CJ_Latest_Update_Date {
	private $date_format;		// Format of the date to display
	private $before_element;	// Element shown before the date (and text)
	private $after_element;		// Element shown after the date (and text)
	private $before_text;		// Text shown before the date
	private $after_text;		// Text shown after the date

	/**
	 * Constructor: Set default markup variables that can be changed through filters.
	 * Sets up shortcode for use in posts/pages and add the filter for the footer output.
	 * 
	 * @return void
	 * @since 1.0
	 */
	public function __construct() {
		$this->date_format = apply_filters( 'latest_update_date_date_format', get_option( 'date_format' ) );
		$this->before_element = apply_filters( 'latest_update_date_before_element', '<p>' );
		$this->after_element = apply_filters( 'latest_update_date_after_element', '</p>' );
		$this->before_text = apply_filters( 'latest_update_date_before_text', 'Latest update date:' );
		$this->after_text = apply_filters( 'latest_update_date_after_text', '' );

		add_shortcode( 'latest_update_date', array( &$this, 'get_shortcode' ) );
		add_filter( 'wp_footer', array( &$this, 'output_footer' ) );
	}

	/**
	 * Returns the post that has the latest updated date
	 *
	 * @access private
	 * @return post object or false if no post found
	 * @since 1.0
	 */
	private function get_latest_updated_post() {
		$posts = get_posts( apply_filters( 'latest_update_date_query_args', array( 'post_type' => array( 'post', 'page' ), 'numberposts' => 1, 'orderby' => 'modified' ) ) );

		// Check if this query has at least one result and return that, or false if no results
		return ( isset( $posts[0] ) ) ? $posts[0] : false;
	}

	/**
	 * Returns the output of the shortcode use
	 *
	 * @access public
	 * @param $atts array Parameters for shortcode to override defaults
	 * @return string output of the shortcode
	 * @since 1.0
	 */
	public function get_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'date_format'    => $this->date_format,
			'before_element' => $this->before_element,
			'after_element'  => $this->after_element,
			'before_text'    => $this->before_text,
			'after_text'     => $this->after_text,
			'post_id'        => false,
		), $atts ) );

		// If post_id specified, we will use that post_id, or else get via $this->get_latest_updated_post()
		if ( ! $post_id ) {
			if ( ! $latest_updated_post = $this->get_latest_updated_post() ) {
				return;
			}
		} else {
			$latest_updated_post = get_post( absint( $post_id ) );
		}

		// Final check if we have a post, create and return the output if so
		if ( $latest_updated_post ) {
			$args = array(
				'date_format'    => $date_format,
				'before_element' => $before_element,
				'before_text'    => $before_text,
				'after_text'     => $after_text,
				'after_element'  => $after_element,
			);
			
			return $this->do_markup( $latest_updated_post, $args, false );
		}
	}

	/**
	 * Echos the output in the footer
	 *
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	public function output_footer() {
		if ( apply_filters( 'latest_update_date_show_in_footer', true ) ) {
			if ( $latest_updated_post = $this->get_latest_updated_post() ) {
				$args = array(
					'date_format'    => $this->date_format,
					'before_element' => $this->before_element,
					'before_text'    => $this->before_text,
					'after_text'     => $this->after_text,
					'after_element'  => $this->after_element,
				);
				
				$this->do_markup( $latest_updated_post, $args, true );
			}
		}
	}

	/**
	 * Prepare the output and output or return it
	 *
	 * @param post object Contains the post date 
	 * @param args array All arguments used for display
	 * @param echo_output bool Echo directly, or return if false
	 * @return string If not echoed directly, containing the prepared string, else void
	 * @since 1.0
	 */
	public function do_markup( $post, $args, $echo_output = true ) {
		$output = $args['before_element'];
		$output .= $args['before_text'];
		$output .= ' ' . mysql2date( $args['date_format'], $post->post_modified ) . ' ';
		$output .= $args['after_text'];
		$output .= $args['after_element'];

		if ( $echo_output ) {
			echo $output;
		} else {
			return $output;
		}
	}
}

/**
 * Old function called to get latest update date
 *
 * @deprecated 1.0
 */
function get_latest_update() {
	_deprecated_function( __FUNCTION__, '1.0' );
}

/**
 * Old function called to show the latest date in the footer
 *
 * @deprecated 1.0
 */
function latest_update_date_footer() {
	_deprecated_function( __FUNCTION__, '1.0' );
}

?>