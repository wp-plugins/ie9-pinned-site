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
	
	add_action("admin_head-$page", 'ie9_pinned_site_admin_head');
	add_action("admin_print_scripts-$page", 'ie9_pinned_site_scripts' );
}

function ie9_pinned_site_admin_init() {
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_post_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_category_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_tag_count' );
	register_setting( 'ie9-pinned-site-settings-group', 'ie9_pinned_site_jump_list_type' );
}

function ie9_pinned_site_scripts(){
}

function ie9_pinned_site_admin_head(){
	wp_enqueue_script('postbox');
	
	echo '<link rel="stylesheet" type="text/css" href="' . WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/stylesheet.css" />';
}

function ie9_pinned_site_settings_page() {
?>

<div class="wrap">
  <h2>IE9 Pinned Site</h2>
  <form method="post" action="options.php">
    <?php settings_fields( 'ie9-pinned-site-settings-group' ); ?>
    <?php $jump_list_type = get_option('ie9_pinned_site_jump_list_type'); ?>
    <div id="options-left" style="display: block;">
      <div class="options-left-inside">
        <div class="metabox-holder">
          <div id="ie9_pinned_site_meta" class="postbox">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
            <h3 class="hndle"><span>Meta Data</span></h3>
            <div class="inside">
              <table class="form-table">
                <tr valign="top">
                  <th scope="row">Application Name</th>
                  <td><input type="text" name="ie9_pinned_site_app_name" value="<?php echo get_option('ie9_pinned_site_app_name'); ?>" size="50"/>
                    <br/>
                    <span class="description">Leave empty to use blog name : '<?php echo get_bloginfo( 'name' )?>'</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Tooltip</th>
                  <td><input type="text" name="ie9_pinned_site_tooltip" value="<?php echo get_option('ie9_pinned_site_tooltip'); ?>" size="50"/>
                    <br/>
                    <span class="description">Leave empty to use blog description : '<?php echo get_bloginfo( 'description' )?>'</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Startup URL</th>
                  <td><input type="text" name="ie9_pinned_site_start_url" value="<?php echo get_option('ie9_pinned_site_start_url'); ?>" size="50"/>
                    <br/>
                    <span class="description">Leave empty to use blog homepage : '<?php echo get_bloginfo( 'url' )?>'</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Enable Jump List</th>
                  <td><input type="checkbox" name="ie9_pinned_site_jmplst_enabled" value="enabled"<?php if ('enabled' == get_option('ie9_pinned_site_jmplst_enabled')) echo ' CHECKED';?> /></td>
                </tr>
              </table>
            </div>
          </div>
          <div id="ie9_pinned_site_jmp_lst" class="postbox">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
            <h3 class="hndle"><span>Jump List</span></h3>
            <div class="inside"> Content </div>
          </div>
          <div id="ie9_pinned_site_news" class="postbox">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
            <h3 class="hndle"><span>News</span></h3>
            <div class="inside">
              <?php 
							require_once(ABSPATH.WPINC.'/rss.php');
							if ( $rss = fetch_rss( 'http://feeds.feedburner.com/IE9-Pinned-Site' ) ) {
								$content = '<ul>';
								$rss->items = array_slice( $rss->items, 0, 3 );
								foreach ( (array) $rss->items as $item ) {
									$content .= '<li class="bubble">';
									$content .= '<a href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
									$content .= '</li>';
								}
								$content .= '<li class="rss"><a href="http://feeds.feedburner.com/IE9-Pinned-Site">Subscribe with RSS</a></li>';
								$content .= '<li class="email"><a href="http://feedburner.google.com/fb/a/mailverify?uri=IE9-Pinned-Site&amp;loc=en_US">Subscribe by email</a></li>';
								$content .= '<li class="twitter"><a href="http://twitter.com/fatihboy">Author on Twitter</a></li>';
								
								echo $content;
							}else{
								echo 'Nothing to say...';
							}
						?>
            </div>
          </div>
        </div>
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </div>
    </div>
    <div id="options-right" style="display: block;">
      <h2 class="heading">Options</h2>
      <div class="metabox-holder">
        <div id="ie9_pinned_site_mode" class="postbox">
          <div class="handlediv" title="Click to toggle"><br />
          </div>
          <h3 class="hndle"><span>Pinned Site Mode</span></h3>
          <div class="box_inside"> <span>
            <label>Enable</label>
            <input type="radio" value="true" name="enable_site_mode" />
            <label>Disable</label>
            <input type="radio" checked="checked" value="false" name="enable_site_mode" />
            </span> </div>
        </div>
        <div id="ie9_pinned_site_settings" class="postbox">
          <div class="handlediv" title="Click to toggle"><br />
          </div>
          <h3 class="hndle"><span>Settings</span></h3>
          <div class="inside">
            <table class="form-table">
              <tr valign="top">
                <th scope="row">Jump list type</th>
                <td><select name="ie9_pinned_site_jump_list_type">
                    <option value="none"<?php if ('none' == $jump_list_type) echo ' SELECTED';?>>None</option>
                    <option value="posts"<?php if ('posts' == $jump_list_type) echo ' SELECTED';?>>Posts</option>
                    <option value="categories"<?php if ('categories' == $jump_list_type) echo ' SELECTED';?>>Categories</option>
                    <option value="tags"<?php if ('tags' == $jump_list_type) echo ' SELECTED';?>>Tags</option>
                  </select></td>
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
          </div>
        </div>
        <div id="ie9_pinned_site_categories" class="postbox">
          <div class="handlediv" title="Click to toggle"><br />
          </div>
          <h3 class="hndle"><span>Add a Category</span></h3>
          <div class="box_inside">
            <ul style="display: block;" id="categories" class="list">
            <?php
				$categories = get_categories('');
				foreach($categories as $category) {
					echo '<li>
              <span class="title">'. $category->cat_name .'</span> <img alt="Add to Jump List" title="Add to Jump List" src="http://www.enterprisecoding.com/blog/wp-content/themes/headlines/functions/images/ico-add.png" />
              </li>'."\r\n";
				}
            ?>
            </ul>
          </div>
        </div>
        <div id="ie9_pinned_site_settings" class="postbox">
          <div class="handlediv" title="Click to toggle"><br />
          </div>
          <h3 class="hndle"><span>Add a Custom Url</span></h3>
          <div class="box_inside">
            <table class="form-table">
              <tr valign="top">
                <th scope="row">Url</th>
                <td><input type="text" name="custom_url" value="http://" /></td>
              </tr>
              <tr valign="top">
                <th scope="row">Menu Text</th>
                <td><input type="text" name="custom_text" /></td>
              </tr>
              <tr valign="top">
                <th scope="row">Icon Url</th>
                <td><input type="text" name="custom_icon" value="http://" /></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<?php } ?>