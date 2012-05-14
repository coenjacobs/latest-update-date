<?php

/*
Plugin Name: Latest Update Date
Version: 0.3
Plugin URI: http://coenjacobs.net/wordpress/plugins/latest-update-date
Description: Will show the date that the site has been update for the last time (in the footer and optionally at posts/pages)
Author: Coen Jacobs
Author URI: http://coenjacobs.net/
*/

function latest_update_admin_menu() {
	add_options_page('Latest Update Date', 'Latest Update Date', 8, 'Latest Update Date', 'latest_update_submenu');
}

add_action('admin_menu', 'latest_update_admin_menu');

function latest_update_message($message) {
	echo "<div id=\"message\" class=\"updated fade\"><p>$message</p></div>\n";
}

function latest_update_submenu() {
	if (isset($_REQUEST['save']) && $_REQUEST['save']) {
		check_admin_referer('latest-update-config');
		
		foreach ( array('before_text', 'after_text', 'before_style', 'after_style', 'date_format') as $val ) {
			if ( !$_POST[$val] )
				update_option( 'latest-update_'.$val, '');
			else
				update_option( 'latest-update_'.$val, $_POST[$val] );
		}
		
		$conditionals = Array();
		if (!$_POST['conditionals'])
			$_POST['conditionals'] = Array();
		
		$curconditionals = get_option('latest-update_conditionals');
		
		if (!array_key_exists('plugin_active',$curconditionals)) {
			$curconditionals['plugin_active'] = false;
		}
		if (!array_key_exists('show_footer',$curconditionals)) {
			$curconditionals['show_footer'] = false;
		}
		
		foreach($curconditionals as $condition=>$toggled)
			$conditionals[$condition] = array_key_exists($condition, $_POST['conditionals']);
			
		update_option('latest-update_conditionals', $conditionals);

		latest_update_message(__("Saved changes.", 'latest-update'));
	}
	
	/**
	 * Display options.
	 */
	?>
	<form action="<?php echo attribute_escape( $_SERVER['REQUEST_URI'] ); ?>" method="post">
	<?php
		if ( function_exists('wp_nonce_field') )
			wp_nonce_field('latest-update-config');
	?>

	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e("Latest Update Date Options", 'latest-update'); ?></h2>
		
		<div style="float: right; width: 25%; background: #DEDEDE; color: #000">
			<div style="padding: 10px;">
				<b>About this plugin</b>
				<p style="line-height: 160%;">The Latest Update Date is developed by <a href="http://coenjacobs.net/">Coen Jacobs</a>. If you like it, please consider making a small <a href="http://coenjacobs.net/donate"><b>donation</b> to the author</a> or if you're on Twitter; <a href="http://twitter.com/coenjacobs">follow <b>@coenjacobs</b></a>!</p>
				<b>Need support?</b>
				<p style="line-height: 160%;">Please use the special <a href="http://wordpress.org/tags/latest-update-date?forum_id=10">support forum</a> for this plugin at WordPress.org for support.</p>
			</div>
		</div>
		
		<div style="float: left; width: 75%;">
		<table class="form-table">
		<tr>
			<th scope="row" valign="top">
				<?php _e("Plugin status:", "latest-update"); ?>
			</th>
			<td>
				<?php
				$conditionals = get_option('latest-update_conditionals');
				?>
				<input type="checkbox" name="conditionals[plugin_active]"<?php echo ($conditionals['plugin_active']) ? ' checked="checked"' : ''; ?> /> <?php _e("Activate the plugin", 'latest-update'); ?><br/>
				<input type="checkbox" name="conditionals[show_footer]"<?php echo ($conditionals['show_footer']) ? ' checked="checked"' : ''; ?> /> <?php _e("Show latest update date in the footer", 'latest-update'); ?><br/>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">
				<?php _e("Before text:", "latest-update"); ?>
			</th>
			<td>
				<?php
					if(attribute_escape(stripslashes(get_option('latest-update_before_text'))) == null)
					{
						$form_title = "Latest update:";
					} else {
						$form_title = attribute_escape(stripslashes(get_option('latest-update_before_text')));
					}
				?>
				<?php _e("Change the text displayed before the date.", 'latest-update'); ?><br/>
				<input size="80" type="text" name="before_text" value="<?php echo $form_title; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">
				<?php _e("After text:", "latest-update"); ?>
			</th>
			<td>
				<?php
					if(attribute_escape(stripslashes(get_option('latest-update_after_text'))) == null)
					{
						$form_title = ".";
					} else {
						$form_title = attribute_escape(stripslashes(get_option('latest-update_after_text')));
					}
				?>
				<?php _e("Change the text displayed after the date.", 'latest-update'); ?><br/>
				<input size="80" type="text" name="after_text" value="<?php echo $form_title; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">
				<?php _e("Date format:", "latest-update"); ?>
			</th>
			<td>
				<?php
					if(attribute_escape(stripslashes(get_option('latest-update_date_format'))) == null)
					{
						$form_title = "j-n-Y";
					} else {
						$form_title = attribute_escape(stripslashes(get_option('latest-update_date_format')));
					}
				?>
				<?php _e("Change format of the date according to the <a href=\"http://codex.wordpress.org/Formatting_Date_and_Time\">documentation on date formatting</a>.", 'latest-update'); ?><br/>
				<input size="80" type="text" name="date_format" value="<?php echo $form_title; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">
				<?php _e("Styling", "latest-update"); ?>
			</th>
			<td>
				<?php
					if(attribute_escape(stripslashes(get_option('latest-update_before_style'))) == null)
					{
						$before_style = "<p>";
					} else {
						$before_style = attribute_escape(stripslashes(get_option('latest-update_before_style')));
					}
				?>
				<?php _e("Element before the date:", 'latest-update'); ?>
				<input size="80" type="text" name="before_style" value="<?php echo $before_style; ?>" /><br />
				<?php
					if(attribute_escape(stripslashes(get_option('latest-update_after_style'))) == null)
					{
						$after_style = "</p>";
					} else {
						$after_style = attribute_escape(stripslashes(get_option('latest-update_after_style')));
					}
				?>
				<?php _e("Element after the date:", 'latest-update'); ?>
				<input size="80" type="text" name="after_style" value="<?php echo $after_style; ?>" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<span class="submit"><input name="save" value="<?php _e("Save Changes", 'latest-update'); ?>" type="submit" /></span>
			</td>
		</tr>
	</table>
	</div>


	</div>
	</form>
	<?php
}

function latest_update_plugin_actions( $links, $file ){
	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
	
	if ( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=Latest Update Date">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}
add_filter( 'plugin_action_links', 'latest_update_plugin_actions', 10, 2 );

function get_latest_update()
{
	$query = mysql_query("SELECT MAX(post_modified) AS maxnum FROM wp_posts WHERE post_status = 'publish'");
	$row = mysql_fetch_array($query);
	return $row[maxnum];
}

function latest_update_date_footer()
{
	$conditionals = get_option('latest-update_conditionals');
	
	if($conditionals['plugin_active'] && $conditionals['show_footer']) {
		$date = get_latest_update();
		$date = mysql2date('j-n-Y', $date);
		
		$line .= stripslashes(get_option('latest-update_before_style'));
		$line .= attribute_escape(stripslashes(get_option('latest-update_before_text')));
		$line .= " ".$date." ";
		$line .= attribute_escape(stripslashes(get_option('latest-update_after_text')));
		$line .= stripslashes(get_option('latest-update_after_style'));
		
		echo $line;
	}
}

add_filter('wp_footer', 'latest_update_date_footer');

?>