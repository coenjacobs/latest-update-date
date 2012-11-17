<?php

/*
Plugin Name: Latest Update Date
Version: 0.3
Plugin URI: http://coenjacobs.net/wordpress/plugins/latest-update-date
Description: Will show the date that the site has been update for the last time (in the footer and optionally at posts/pages)
Author: Coen Jacobs
Author URI: http://coenjacobs.net/
*/

function get_latest_update() {
	$query = mysql_query("SELECT MAX(post_modified) AS maxnum FROM wp_posts WHERE post_status = 'publish'");
	$row = mysql_fetch_array($query);
	return $row['maxnum'];
}

function latest_update_date_footer() {
	$conditionals = get_option('latest-update_conditionals');
	
	if ( apply_filters( 'latest_update_date_show_in_footer', true ) ) {
		$output = apply_filters( 'latest_update_date_before_element', '<p>' );
		$output .= apply_filters( 'latest_update_date_before_text', 'Latest update date:' );
		$output .= ' ' . mysql2date( 'j-n-Y', get_latest_update() ) . ' ';
		$output .= apply_filters( 'latest_update_date_after_text', '' );
		$output .= apply_filters( 'latest_update_date_after_element', '</p>' );
		
		echo $output;
	}
}

add_filter( 'wp_footer', 'latest_update_date_footer' );

?>