<?php

register_activation_hook(dirname( __FILE__ ) . '/IE9-Pinned-site.php','ie9_pinned_site_install'); 
register_deactivation_hook(dirname( __FILE__ ) . '/IE9-Pinned-site.php', 'ie9_pinned_site_uninstall' );

add_action('admin_menu', 'ie9_pinned_site_create_menu');
add_action('admin_init', 'ie9_pinned_site_admin_init' );

function ie9_pinned_site_install(){
	add_option("ie9_pinned_site_post_count", '5', '', 'yes');
	add_option("ie9_pinned_site_category_count", '5', '', 'yes');
	add_option("ie9_pinned_site_tag_count", '5', '', 'yes');
	add_option("ie9_pinned_site_jump_list_type", 'posts', '', 'yes');
}

function ie9_pinned_site_remove(){
	delete_option('ie9_pinned_site_post_count');
	delete_option('ie9_pinned_site_category_count');
	delete_option('ie9_pinned_site_tag_count');
	delete_option('ie9_pinned_site_jump_list_type');
}

function ie9_pinned_site_create_menu() {
    $page = add_options_page('IE9 Pinned Site Settings', 'IE9 Pinned Site', 'administrator', 'IE9-Pinned-Site', 'ie9_pinned_site_settings_page');
	add_action("admin_print_scripts-$page", 'ie9_pinned_site_admin_styles');
}

function ie9_pinned_site_admin_init() {
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_post_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_category_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_tag_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_jump_list_type' );
	
	wp_register_style('ie9-pinned-site', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/stylesheet.css');
}

function ie9_pinned_site_admin_styles(){
	wp_enqueue_script('postbox');
	wp_enqueue_script('ie9-pinned-site');
}

function ie9_pinned_site_settings_page() {
?>
<div class="wrap">
<h2>IE9 Pinned Site</h2>

<div class="postbox-container" style="width:100%;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div id="ie9_pinned_site_settings" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Settings</span></h3>
					<div class="inside">
						<form method="post" action="options.php">
							<?php settings_fields( 'ie9-pinned-site-settings-group' ); ?>
							<?php $jump_list_type = get_option('ie9_pinned_site_jump_list_type'); ?>

							<table class="form-table">
								<tr valign="top">
									<th scope="row">Jump list type</th>
									<td>
										<select name="ie9_pinned_site_jump_list_type">
											<option value="none"<?php if ('none' == $jump_list_type) echo ' SELECTED';?>>None</option>
											<option value="posts"<?php if ('posts' == $jump_list_type) echo ' SELECTED';?>>Posts</option>
											<option value="categories"<?php if ('categories' == $jump_list_type) echo ' SELECTED';?>>Categories</option>
											<option value="tags"<?php if ('tags' == $jump_list_type) echo ' SELECTED';?>>Tags</option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Number of post to display</th>
									<td><input type="text" name="ie9_pinned_site_post_count" value="<?php echo get_option('ie9_pinned_site_post_count'); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row">Number of category to display</th>
									<td><input type="text" name="ie9_pinned_site_category_count" value="<?php echo get_option('ie9_pinned_site_category_count'); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row">Number of tag to display</th>
									<td><input type="text" name="ie9_pinned_site_tag_count" value="<?php echo get_option('ie9_pinned_site_tag_count'); ?>" /></td>
								</tr>	
							</table>
							
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
							</p>
						</form>
					</div>
				</div>
				
				<div id="ie9_pinned_site_news" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>News</span></h3>
					<div class="inside">
						<?php 
						require_once(ABSPATH.WPINC.'/rss.php');
						if ( $rss = fetch_rss( 'http://feeds.feedburner.com/IE9-Pinned-Site' ) ) {
							$content = '<ul>';
							$rss->items = array_slice( $rss->items, 0, 3 );
							foreach ( (array) $rss->items as $item ) {
								$content .= '<li>';
								$content .= '<a href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
								$content .= '</li>';
							}
							$content .= '<li class="rss"><a href="http://feeds.feedburner.com/IE9-Pinned-Site">Subscribe with RSS</a></li>';
							$content .= '<li class="email"><a href="http://feedburner.google.com/fb/a/mailverify?uri=IE9-Pinned-Site&amp;loc=en_US">Subscribe by email</a></li>';
							
							echo $content;
						}else{
							echo 'Nothing to say...';
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>