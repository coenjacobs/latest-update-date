<?php

/*
Plugin Name: Latest Update Date
Version: 0.3
Plugin URI: http://coenjacobs.net/wordpress/plugins/latest-update-date
Description: Will show the date that the site has been update for the last time (in the footer and optionally at posts/pages)
Author: Coen Jacobs
Author URI: http://coenjacobs.net/
*/

if ( ! is_admin() ) {
	global $cj_latest_update_date;
	$cj_latest_update_date = new CJ_Latest_Update_Date();
}

class CJ_Latest_Update_Date {
	public function __construct() {
		add_filter( 'wp_footer', array( &$this, 'output_footer' ) );
	}

	private function get_latest_updated_post() {
		$posts = get_posts( apply_filters( 'latest_update_date_query_args', array( 'post_type' => array( 'post', 'page' ), 'numberposts' => 1, 'orderby' => 'modified' ) ) );

		return ( isset( $posts[0] ) ) ? $posts[0] : false;
	}

	public function output_footer() {
		if ( apply_filters( 'latest_update_date_show_in_footer', true ) ) {
			if ( $latest_updated_post = $this->get_latest_updated_post() ) {
				$output = apply_filters( 'latest_update_date_before_element', '<p>' );
				$output .= apply_filters( 'latest_update_date_before_text', 'Latest update date:' );
				$output .= ' ' . mysql2date( 'j-n-Y', $latest_updated_post->post_modified ) . ' ';
				$output .= apply_filters( 'latest_update_date_after_text', '' );
				$output .= apply_filters( 'latest_update_date_after_element', '</p>' );
				
				echo $output;
			}
		}
	}
}

?>