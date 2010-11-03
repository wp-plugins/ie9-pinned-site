<?php
/*
Plugin Name: IE9 Pinned Site
Plugin URI: http://www.enterprisecoding.com/blog/projects/ie9-pinned-site
Description: Adds support for Internet explorer 9 pinned site features to your wordpress blog
Version: 1.1.2
Author: Fatih Boy
Author URI: http://www.enterprisecoding.com/
License: GPL2
*/

/*

    Copyright 2010  Fatih Boy (email : fatih@enterprisecoding.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	
	***************************************************************************
	email.png, rss.png, twitter.png images are designed by DsynFLO Creations
	(http://bharathp666.deviantart.com/)
	
	bubble.png image designed by PixelMixer (http://pixel-mixer.com/)
	***************************************************************************
*/

//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

@include( dirname( __FILE__ ) . '/include/customUrl.php');
@include( dirname( __FILE__ ) . '/include/publishPost.php');
@include( dirname( __FILE__ ) . '/include/moderateComments.php');
@include( dirname( __FILE__ ) . '/include/uploadFiles.php');
@include( dirname( __FILE__ ) . '/include/tag.php');
@include( dirname( __FILE__ ) . '/include/category.php');
@include( dirname( __FILE__ ) . '/include/page.php');

class ie9PinnedSitePlugin {
	var $version;
	var $encoding;
	var $nonceField;
	
	var $options;
	var $jumpListOptions;
	var $taskOptions;
	
	function ie9PinnedSitePlugin() {
		$this->version = '1.1.2';
		$this->encoding = get_option('blog_charset');
		$this->nonceField = 'ie9PinnedSite-updatesettings';
		
		register_activation_hook( __FILE__ ,array(&$this, 'on_activate')); 
		register_deactivation_hook(  __FILE__ , array(&$this, 'on_deactivate') );
		
		add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);

		add_action('init', array(&$this, 'on_init'));
		add_action('wp_head', array(&$this, 'on_head'));
		add_action('admin_menu', array(&$this, 'on_admin_menu'));
		
		wp_register_style('ie9-pinned-site', plugins_url('ie9-pinned-site.css', __FILE__), array(), $this->version, 'all');
		wp_register_style('jquery-ui-button', plugins_url('css/jquery-ui-button.css', __FILE__), array(), '1.8.5', 'all');
		
		wp_register_script( 'jquery-autocomplete', plugins_url('js/jquery.autocomplete.js', __FILE__));
		wp_register_script( 'jquery-tmpl', plugins_url('js/jquery.tmpl.js', __FILE__));
		wp_register_script( 'jquery-ui-effect-transfer', plugins_url('js/jquery-ui-effect-transfer.js', __FILE__));
		wp_register_script( 'ie9-pinned-site-admin', plugins_url('js/ie9-pinned-site.js', __FILE__));
		
		$this->options = get_option('ie9_pinned_site');
		
		if($this->options==FALSE){
			$this->initializeOptions();
		}else{
			$this->jumpListOptions = get_option('ie9_pinned_site_jumpList');
			$this->taskOptions = get_option('ie9_pinned_site_task');
		}
	}
	
	function initializeOptions(){
		$this->options = new stdClass();
		$this->taskOptions = new stdClass();		
		$this->jumpListOptions = new stdClass();
		
		$this->options->enabled = true;
		$this->options->applicationName = get_bloginfo( 'name' );
		$this->options->tooltip = get_bloginfo( 'description' );
		$this->options->startupUrl = get_bloginfo( 'url' );
		$this->options->window->width = 0;
		$this->options->window->height = 0;
		$this->options->navButtonColor = '';
		$this->options->jumpListEnabled = false;
		
		$this->jumpListOptions->title = '';
		
		$this->jumpListOptions->entries = array();
		
		$homepageTask =  new stdClass();
		$homepageTask->type = 'CustomUrl';
		$homepageTask->url = get_bloginfo( 'url' );
		$homepageTask->text = 'Home Page';
		$homepageTask->iconUrl = plugins_url('images/home.ico', __FILE__);
		
		$publishPostTask =  new stdClass();
		$publishPostTask->type = "PublishPost";
		
		$moderateCommentsTask =  new stdClass();
		$moderateCommentsTask->type = "ModerateComments";
		
		$uploadFilesTask =  new stdClass();
		$uploadFilesTask->type = "UploadFiles";
		
		$this->taskOptions->entries = array(
			$homepageTask,
			$publishPostTask,
			$moderateCommentsTask,
			$uploadFilesTask
		);
	}
	
	function on_init(){
		$currentLocale = get_locale();
		if(!empty($currentLocale)) {
			$moFile = dirname( __FILE__ ) . '/languages/IE9-Pinned-Site-' . $currentLocale . ".mo";
			if( @file_exists( $moFile ) && is_readable( $moFile ) )
				load_textdomain( 'IE9-Pinned-Site', $moFile );
		}
	}
	
	function on_activate() {	
		add_option('ie9_pinned_site', $this->options);
		add_option('ie9_pinned_site_task', $this->taskOptions);
		add_option('ie9_pinned_site_jumpList', $this->jumpListOptions);
   	}
 
   function on_deactivate() {
      delete_option('ie9_pinned_site');
	  delete_option('ie9_pinned_site_task');
	  delete_option('ie9_pinned_site_task');
   	}

	function on_head(){
		echo "\r\n" . '<!-- IE9 Pinned Site ' . $this->version . ' by Fatih Boy -->'."\r\n";
		echo '<!-- info : http://www.enterprisecoding.com/blog/projects/ie9-pinned-site -->'."\r\n";
		echo '<meta name="IE9 Pinned Site" content="' . $this->version . '" />'."\r\n";
		
		if(!$this->options->enabled) { return; }
		
		if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) !== FALSE ) {
			echo '<meta name="application-name" content="' . $this->options->applicationName . '" />'."\r\n";
			echo '<meta name="msapplication-tooltip" content="' . $this->options->tooltip . '" />'."\r\n";
			echo '<meta name="msapplication-starturl" content="' . $this->options->startupUrl . '" />'."\r\n";
			
			$count = count($this->taskOptions->entries);
			for ($i = 0; $i < $count; $i++) {
				$dataObject = $this->taskOptions->entries[$i];
				$type = 'ie9PinnedSite'.$dataObject->type;
				$taskEntry = new $type($dataObject, 'task', $i+1);
				
				$taskEntry->render4Task();
			}
			
			if ($this->options->enabled && '' != $this->jumpListOptions->title){
				echo '<script type="text/javascript">' . "\r\n";
				echo 'if (window.external && window.external.msIsSiteMode()) {' . "\r\n";
				
				echo 'var wext=window.external;' . "\r\n";
				echo 'wext.msSiteModeCreateJumplist("' . $this->jumpListOptions->title . '");'."\r\n";
				
				$count = count($this->jumpListOptions->entries);
				for ($i = 0; $i < $count; $i++) {
					$dataObject = $this->jumpListOptions->entries[$i];
					$type = 'ie9PinnedSite'.$dataObject->type;
					$taskEntry = new $type($dataObject, 'jump_list', $i+1);
					
					$taskEntry->render4JumpList();
				}
				
				echo 'wext.msSiteModeShowJumplist();'."\r\n";
				echo '}'."\r\n";
				echo '</script>'."\r\n";
			}
		}
		
		echo '<!-- /IE9 Pinned Site -->'."\r\n";
	}
	
	function on_screen_layout_columns($columns, $screen) {
		if ($screen == $this->pagehook) {
			$columns[$this->pagehook] = 2;
		}
		return $columns;
	}
	
	function on_admin_menu() {
		$this->pagehook = add_options_page('IE9 Pinned Site Settings', 'IE9 Pinned Site', 'administrator', 'IE9-Pinned-Site', array(&$this, 'on_show_options_page'));
		
		add_action('load-'.$this->pagehook, array(&$this, 'on_load_options_page'));
		add_action('admin_print_styles-' . $this->pagehook, array(&$this, 'on_print_styles'));
	}
	
	function on_load_options_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-autocomplete');
		wp_enqueue_script('jquery-tmpl');
		wp_enqueue_script('jquery-ui-effect-transfer');
		wp_enqueue_script('ie9-pinned-site-admin');
		
		add_meta_box('ie9-pinned-site-mode-sidebox', 'Pinned Site Mode', array(&$this, 'on_pinned_site_mode_content'), $this->pagehook, 'side', 'high');
		
		add_meta_box('ie9-pinned-site-metadata-normalbox', 'Meta Data', array(&$this, 'on_metadata_content'), $this->pagehook, 'normal', 'high');
		add_meta_box('ie9-pinned-site-tasks-normalbox', 'Tasks', array(&$this, 'on_task_content'), $this->pagehook, 'normal', 'high');
		add_meta_box('ie9-pinned-site-jump_list-normalbox', 'Jump List', array(&$this, 'on_jump_list_content'), $this->pagehook, 'normal', 'high');
		add_meta_box('ie9-pinned-site-news-additionalbox', 'News', array(&$this, 'on_news_content'), $this->pagehook, 'additional', 'core');
		
		add_meta_box('ie9-pinned-site-pages-sidebox', 'Add an Existing Page', array(&$this, 'on_pages_content'), $this->pagehook, 'side', 'core');
		add_meta_box('ie9-pinned-site-categories-sidebox', 'Add a Category', array(&$this, 'on_categories_content'), $this->pagehook, 'side', 'core');
		add_meta_box('ie9-pinned-site-tags-sidebox', 'Add a Tag', array(&$this, 'on_tags_content'), $this->pagehook, 'side', 'core');
		add_meta_box('ie9-pinned-site-task-sidebox', 'Add a Custom Task', array(&$this, 'on_custom_task_content'), $this->pagehook, 'side', 'core');
		add_meta_box('ie9-pinned-site-url-sidebox', 'Add a Custom Url', array(&$this, 'on_custom_url_content'), $this->pagehook, 'side', 'core');
	}
	
	function on_print_styles(){		
		wp_enqueue_style('ie9-pinned-site');
		wp_enqueue_style('jquery-ui-button');
	}
	
	function on_show_options_page() {
		global $screen_layout_columns;
		
		if ( isset($_POST['submit']) ) {
			if (!current_user_can('manage_options')) die(__('You cannot edit the options.'));
			check_admin_referer($this->nonceField); 
			
			$this->options = new stdClass();
			$this->taskOptions = new stdClass();		
			$this->jumpListOptions = new stdClass();
			
			$this->options->enabled = $_POST['enabled'] === 'true' ? true : false;
			$this->options->applicationName = $_POST['app_name'];
			$this->options->tooltip = $_POST['tooltip'];
			$this->options->startupUrl = $_POST['url'];
			$this->options->window->width = is_numeric($_POST['window_width']) ? (int)$_POST['window_width'] : 0;
			$this->options->window->height = is_numeric($_POST['window_height']) ? (int)$_POST['window_height'] : 0;
			$this->options->navButtonColor = $_POST['navbutton_color'];
			$this->options->jumpListEnabled = $_POST['jmplst_enabled'] === 'enabled' ? true : false;
			
			$this->jumpListOptions->title = $_POST['jmplst_title'];
			
			$this->jumpListOptions->entries = array();
			
			$jumpListItems = $_POST['jump_list_order']=='' ? array() : explode('|', $_POST['jump_list_order']);
			
			foreach ($jumpListItems as $jumpListItem) {
				$typeName = $_POST[$jumpListItem . '_type'];
				$type = 'ie9PinnedSite'.$typeName;
				
				$itemIndex = substr($jumpListItem, 10);
				
				eval('$settingObject = '.$type.'::GetSettingObject(\'jump_list\', $itemIndex);');
				array_push($this->jumpListOptions->entries, $settingObject);
			}
			unset($jumpListItems);
			
			$this->taskOptions->entries = array();
			
			$taskItems = $_POST['task_order']=='' ? array() : explode('|', $_POST['task_order']);
			
			foreach ($taskItems as $taskItem) {				
				$typeName = $_POST[$taskItem . '_type'];
				$type = 'ie9PinnedSite'.$typeName;
				
				$itemIndex = substr($taskItem, 5);
				
				eval('$settingObject = '.$type.'::GetSettingObject(\'task\', $itemIndex);');
				array_push($this->taskOptions->entries, $settingObject);
			}
			unset($taskItems);
			
			update_option('ie9_pinned_site', $this->options);
			update_option('ie9_pinned_site_task', $this->taskOptions);
			update_option('ie9_pinned_site_jumpList', $this->jumpListOptions);
		}
		?>

<div class="wrap">
  <?php screen_icon('options-general'); ?>
  <h2 class="pagetitle">IE9 Pinned Site</h2>
  <form method="post" action="">
  	<?php wp_nonce_field($this->nonceField); ?>
  	<input type="hidden" name="task_count" id="task_count" value="0" />
    <input type="hidden" name="jump_list_count" id="jump_list_count" value="0" />
    
    <div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
      <div id="side-info-column" class="inner-sidebar">
        <?php do_meta_boxes($this->pagehook, 'side', ''); ?>
      </div>
      <div id="post-body" class="has-sidebar">
        <div id="post-body-content" class="has-sidebar-content">
          <?php do_meta_boxes($this->pagehook, 'normal', ''); ?>
          <?php do_meta_boxes($this->pagehook, 'additional', ''); ?>
          <p class="submit">
            <input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
          </p>
        </div>
      </div>
      <br class="clear"/>
    </div>
  </form>
</div>
<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
<?php
	}
	
	function on_metadata_content($data) {
		$blogName = htmlentities(get_bloginfo( 'name' ), ENT_COMPAT, $this->encoding);
		$blogDescription = htmlentities(get_bloginfo( 'description' ), ENT_COMPAT, $this->encoding);
		$blogUrl = htmlentities(get_bloginfo( 'url' ), ENT_COMPAT, $this->encoding);
		
		?>
        <table class="form-table">
                <tr valign="top">
                  <th scope="row">Application Name</th>
                  <td><input type="text" id="app_name" name="app_name" value="<?php echo  htmlentities($this->options->applicationName, ENT_COMPAT, $this->encoding); ?>" size="50" onblur="onInputBlur('app_name', '<?php echo $blogName ?>');" onfocus="onInputFocus('app_name', '<?php echo $blogName ?>');" <?php  if($this->options->applicationName == get_bloginfo( 'name' )) { echo 'class="inputDefault"'; } ?>/>
                    <br/>
                    <span class="description">Leave empty to use blog name</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Tooltip</th>
                  <td><input type="text" id="tooltip" name="tooltip" value="<?php echo  htmlentities($this->options->tooltip, ENT_COMPAT, $this->encoding); ?>" size="50" onblur="onInputBlur('tooltip', '<?php echo $blogDescription ?>');" onfocus="onInputFocus('tooltip', '<?php echo $blogDescription ?>');" <?php  if($this->options->tooltip == get_bloginfo( 'description' )) { echo 'class="inputDefault"'; } ?>/>
                    <br/>
                    <span class="description">Leave empty to use blog description</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Startup URL</th>
                  <td><input type="text" id="url" name="url" value="<?php echo  htmlentities($this->options->startupUrl, ENT_COMPAT, $this->encoding); ?>" size="50" onblur="onInputBlur('url', '<?php echo $blogUrl ?>');" onfocus="onInputFocus('url', '<?php echo $blogUrl ?>');" <?php  if($this->options->startupUrl == get_bloginfo( 'url' )) { echo 'class="inputDefault"'; } ?>/>
                    <br/>
                    <span class="description">Leave empty to use blog homepage</span></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Window Size</th>
                  <td>
                  	<input type="text" id="window_width" name="window_width" value="<?php if($this->options->window->width!=0) { echo $this->options->window->width; } ?>" size="8"/> X <input type="text" name="window_height" value="<?php if($this->options->window->height!=0) { echo $this->options->window->height; } ?>" size="8"/></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Navigation Button Color</th>
                  <td><input type="text" name="navbutton_color" value="<?php echo $this->options->navButtonColor; ?>" size="8"/></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Enable Jump List</th>
                  <td><input type="checkbox" name="jmplst_enabled" value="enabled"<?php if ($this->options->jumpListEnabled) echo ' CHECKED';?> /></td>
                </tr>
                <tr valign="top">
                  <th scope="row">Jump List Title</th>
                  <td><input type="text" name="jmplst_title" value="<?php echo $this->jumpListOptions->title; ?>" size="50"/></td>
                </tr>
        </table>
		<?php
	}
	
	function on_task_content($data) {
		echo '<ul id="task_entries">';
		
		$count = count($this->taskOptions->entries);
		for ($i = 0; $i < $count; $i++) {
			$dataObject = $this->taskOptions->entries[$i];
			$type = 'ie9PinnedSite'.$dataObject->type;
			$taskEntry = new $type($dataObject, 'task', $i+1);
			
			$taskEntry->render4AdminPage();
		}
		
		echo '</ul>';
		echo '<input type="hidden" name="task_index" id="task_index" value="' . $count . '">';
		echo '<input type="hidden" name="task_order" id="task_order" value="">';
	}
	
	function on_jump_list_content($data) {
		echo '<ul id="jump_list_entries">';
		
		$count = count($this->jumpListOptions->entries);
		for ($i = 0; $i < $count; $i++) {
			$dataObject = $this->jumpListOptions->entries[$i];
			$type = 'ie9PinnedSite'.$dataObject->type;
			$jumpListEntry = new $type($dataObject, 'jump_list', $i+1);
			
			$jumpListEntry->render4AdminPage();
		}
		
		echo '</ul>';
		echo '<input type="hidden" name="jump_list_index" id="jump_list_index" value="' . $count . '">';
		echo '<input type="hidden" name="jump_list_order" id="jump_list_order" value="">';
	}
	
	function on_news_content($data) {
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
	}
	
	function on_pinned_site_mode_content($data) {
		?>
		<span>
            <label>Enable</label>
            <input type="radio" name="enabled"  value="true" <?php if($this->options->enabled) { echo 'checked="checked"'; } ?>/>
            <label>Disable</label>
            <input type="radio" name="enabled" value="false" <?php if(!$this->options->enabled) { echo 'checked="checked"'; } ?>/>
        </span>
		<?php
	}
	
	function on_pages_content($data) {
		$pages = get_pages("sort_column='menu_order'");
		?>
    <script id="page_tmpl" type="text/x-jquery-tmpl">
		  <li id="${entryType}_${entryId}" name="${entryType}_${entryId}">
            <span class="title">${pageTitle}</span>
            <span class="settings">
            	<span class="entry-type">Page</span>
                <a id="delete${entryType}${entryId}" onclick="deleteEntry('${entryType}', ${entryId})" value="${entryId}" class="delete"><img class="delete" alt="Delete ${entryTypeFriendly} Entry" title="Delete ${entryTypeFriendly} Entry" src="<?php echo plugins_url('images/delete.png', __FILE__)?>"></a> 
            </span>
			<input type="hidden" name="${entryType}_${entryId}_type" id="${entryType}_${entryId}_type" value="page">
            <input type="hidden" name="${entryType}_${entryId}_pageId" id="${entryType}_${entryId}_pageId" value="${pageId}">
          </li>
	</script>    
	<script>
  		jQuery(document).ready(function($) {					
			jQuery("#pages-search").autocomplete([<?php foreach($pages as $pagg) { echo '"'.$pagg->post_title .'",'; }?>]);
 		});
  	</script>
    <input type="text" value="Search" id="pages-search" onblur="onInputBlur('pages-search', 'Search');" onfocus="onInputFocus('pages-search', 'Search');" autocomplete="off" class="inputDefault">
    <a onclick="doViewAll('pages');" style="cursor: pointer; display: inline;" id="show-pages">View All</a>
    <a onclick="doHideAll('pages');" style="cursor: pointer; display: none;" id="hide-pages">Hide All</a>
	<?php
		echo '<ul style="display: none;" id="pages" class="list">';
		foreach($pages as $pagg) {
			$pageTitle = htmlentities($pagg->post_title, ENT_QUOTES, $this->encoding);
			
			echo '<li><span class="title">'. $pageTitle .'</span> <a id="page'. $pagg->ID .'" onclick="appendPageToList(\'page'. $pagg->ID .'\', \'jump_list\', \''. $pageTitle .'\', ' . $pagg->ID . ');"><img alt="Add to Jump List" title="Add to Jump List" src="' . plugins_url('images/plus.png', __FILE__) .'" /></a></li>'."\r\n";
		}
		echo '</ul>';
	}
	
	function on_categories_content($data) {
		$categories = get_categories('');
		?>
    <script id="category_tmpl" type="text/x-jquery-tmpl">
		  <li id="${entryType}_${entryId}" name="${entryType}_${entryId}">
            <span class="title">${categoryName}</span>
            <span class="settings">
            	<span class="entry-type">Category</span>
                <a id="delete${entryType}${entryId}" onclick="deleteEntry('${entryType}', ${entryId})" value="${entryId}" class="delete"><img class="delete" alt="Delete ${entryTypeFriendly} Entry" title="Delete ${entryTypeFriendly} Entry" src="<?php echo plugins_url('images/delete.png', __FILE__)?>"></a> 
            </span>
			<input type="hidden" name="${entryType}_${entryId}_type" id="${entryType}_${entryId}_type" value="category">
            <input type="hidden" name="${entryType}_${entryId}_termId" id="${entryType}_${entryId}_termId" value="${termId}">
          </li>
	</script>    
	<script>
  		jQuery(document).ready(function($) {					
			jQuery("#categories-search").autocomplete([<?php foreach($categories as $category) { echo '"'.$category->cat_name .'",'; }?>]);
 		});
  	</script>
    <input type="text" value="Search" id="categories-search" onblur="onInputBlur('categories-search', 'Search');" onfocus="onInputFocus('categories-search', 'Search');" autocomplete="off" class="inputDefault">
    <a onclick="doViewAll('categories');" style="cursor: pointer; display: inline;" id="show-categories">View All</a>
    <a onclick="doHideAll('categories');" style="cursor: pointer; display: none;" id="hide-categories">Hide All</a>
	<?php
		echo '<ul style="display: none;" id="categories" class="list">';
		foreach($categories as $category) {
			$categoryName = htmlentities($category->cat_name, ENT_QUOTES, $this->encoding);
			
			echo '<li><span class="title">'. $categoryName .'</span> <a id="category'. $category->term_id .'" onclick="appendCategoryToList(\'category'. $category->term_id .'\', \'jump_list\', \''. $categoryName .'\', ' . $category->term_id . ');"><img alt="Add to Jump List" title="Add to Jump List" src="' . plugins_url('images/plus.png', __FILE__) .'" /></a></li>'."\r\n";
		}
		echo '</ul>';
	}
	
	function on_tags_content($data) {
		$tags = get_tags('');
		?>
    <script id="tag_tmpl" type="text/x-jquery-tmpl">
		  <li id="${entryType}_${entryId}" name="${entryType}_${entryId}">
            <span class="title">${tagName}</span>
            <span class="settings">
            	<span class="entry-type">Tag</span>
                <a id="delete${entryType}${entryId}" onclick="deleteEntry('${entryType}', ${entryId})" value="${entryId}" class="delete"><img class="delete" alt="Delete ${entryTypeFriendly} Entry" title="Delete ${entryTypeFriendly} Entry" src="<?php echo plugins_url('images/delete.png', __FILE__)?>"></a> 
            </span>
			<input type="hidden" name="${entryType}_${entryId}_type" id="${entryType}_${entryId}_type" value="tag">
            <input type="hidden" name="${entryType}_${entryId}_termId" id="${entryType}_${entryId}_termId" value="${termId}">
          </li>
	</script>    
	<script>
  		jQuery(document).ready(function($) {					
			jQuery("#tags-search").autocomplete([<?php foreach($tags as $tag) { echo '"'.$tag->name .'",'; }?>]);
 		});
  	</script>
    <input type="text" value="Search" id="tags-search" onblur="onInputBlur('tags-search', 'Search');" onfocus="onInputFocus('tags-search', 'Search');" autocomplete="off" class="inputDefault">
    <a onclick="doViewAll('tags');" style="cursor: pointer; display: inline;" id="show-tags">View All</a>
    <a onclick="doHideAll('tags');" style="cursor: pointer; display: none;" id="hide-tags">Hide All</a>
	<?php
		echo '<ul style="display: none;" id="tags" class="list">';
		foreach($tags as $tag) {
			$tagName = htmlentities($tag->name, ENT_QUOTES, $this->encoding);
			
			echo '<li><span class="title">'. $tagName .'</span> <a id="tag'. $tag->term_id .'" onclick="appendTagToList(\'tag'. $tag->term_id .'\', \'jump_list\', \''. $tagName .'\', ' . $tag->term_id . ');"><img alt="Add to Jump List" title="Add to Jump List" src="' . plugins_url('images/plus.png', __FILE__) .'" /></a></li>'."\r\n";
		}
		echo '</ul>';
	}
	
	function on_custom_task_content($data) {
		?>
        <script id="custom_task_tmpl" type="text/x-jquery-tmpl">
		  <li id="task_${entryId}" name="task_${entryId}">
            <span class="title">${taskName}</span>
            <span class="settings">
            	<span class="entry-type">Custom Task</span>
                <a id="deletetask${entryId}" onclick="deleteEntry('task', ${entryId})" value="${entryId}"><img class="delete" alt="Delete Task Entry" title="Delete Task Entry" src="<?php echo plugins_url('images/delete.png', __FILE__)?>"></a> 
            </span>
			<input type="hidden" name="task_${entryId}_type" id="task_${entryId}_type" value="${taskType}">
          </li>
		</script>
        <select name="customTask" id="customTask">
           	<option value="PublishPost">Publish Post</option>
            <option value="ModerateComments">Moderate Comments</option>
            <option value="UploadFiles">Upload Files</option>
         </select>
        <input type="button" class="button" name="addcustomTask" id="addcustomTask" value="Add" onclick="appendCustomTask(jQuery('#customTask').val());">
        <?php
	}
	
	function on_custom_url_content($data) {
		?>
        <script id="custom_url_tmpl" type="text/x-jquery-tmpl">
		  <li id="${entryType}_${entryId}" name="${entryType}_${entryId}">
            <span class="title">${text}</span>
            <span class="settings">
            	<span class="entry-type">url</span>
                <a id="delete${entryType}${entryId}" onclick="deleteEntry('${entryType}', ${entryId})" value="${entryId}"><img class="delete" alt="Delete ${entryType} Entry" title="Delete ${entryType} Entry" src="<?php echo plugins_url('images/delete.png', __FILE__)?>"></a> 
            </span>
			<input type="hidden" name="${entryType}_${entryId}_type" id="${entryType}_${entryId}_type" value="customUrl">
            <input type="hidden" name="${entryType}_${entryId}_url" id="${entryType}_${entryId}_url" value="${url}">
			<input type="hidden" name="${entryType}_${entryId}_text" id="${entryType}_${entryId}_text" value="${text}">
			<input type="hidden" name="${entryType}_${entryId}_icon" id="${entryType}_${entryId}_icon" value="${icon}">
          </li>
		</script>
        <table class="form-table">
              <tr valign="top">
                <th scope="row">Url</th>
                <td><input type="text" id="custom_url" name="custom_url" value="http://" /></td>
              </tr>
              <tr valign="top">
                <th scope="row">Menu Text</th>
                <td><input type="text" id="custom_text" name="custom_text" /></td>
              </tr>
              <tr valign="top">
                <th scope="row">Icon Url</th>
                <td><input type="text" id="custom_icon" name="custom_icon" value="http://" /></td>
              </tr>
        </table>
        <div style="text-align:right">
            <input type="button" class="button" name="addurl2tasks" value="Add to Tasks" id="addurl2tasks" onclick="appendURLToList('task');">
            <input type="button" class="button" name="addurl2jumplist" value="Add to Jump List" id="addurl2jumplist" onclick="appendURLToList('jump_list');">
        </div>
        <?php
	}
}

$ie9PinnedSitePlugin = new ie9PinnedSitePlugin();
?>