<?php
/*
Plugin Name: IE9 Pinned Site
Plugin URI: http://www.enterprisecoding.com/blog/projects/ie9-pinned-site
Description: Adds support for Internet explorer 9 pinned site features to your wordpress blog
Version: 1.0.1
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
*/
add_action( 'wp_head', 'ie9_pinned_site_head' );
add_action( 'init', 'ie9_pinned_site_init' );

$ie9_pinned_site_config_file = dirname( __FILE__ ) . '/IE9-Pinned-site-config.php';
@include($ie9_pinned_site_config_file);

function ie9_pinned_site_init() {
    $currentLocale = get_locale();
    if(!empty($currentLocale)) {
        $moFile = dirname( __FILE__ ) . '/languages/IE9-Pinned-Site-' . $currentLocale . ".mo";
        if( @file_exists( $moFile ) && is_readable( $moFile ) )
			load_textdomain( 'IE9-Pinned-Site', $moFile );
    }
}

function ie9_pinned_site_head() {
	echo "\r\n" . '<!-- IE9 Pinned Site 1.0.1 by Fatih Boy -->'."\r\n";
	echo '<!-- info : http://www.enterprisecoding.com/blog/projects/ie9-pinned-site -->'."\r\n";
	echo '<meta name="IE9 Pinned Site" content="1.0.1" />'."\r\n";
	
	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) !== FALSE ) {
		echo '<meta name="application-name" content="' . get_bloginfo( 'name' ) . '" />'."\r\n";
		echo '<meta name="msapplication-tooltip" content="' . get_bloginfo( 'description' ) . '" />'."\r\n";
		echo '<meta name="msapplication-starturl" content="' . get_bloginfo( 'url' ) . '" />'."\r\n";
		
		echo '<meta name="msapplication-task" content="name=Home Page;action-uri=' . get_bloginfo( 'url' ) . ';icon-uri=' . plugins_url('media/home.ico', __FILE__) . '" />'."\r\n";
		
		if ( is_user_logged_in() ) {				
			if( current_user_can( 'publish_posts' ) ) {
				echo '<meta name="msapplication-task" content="name=' . __( 'Write a post', 'IE9-Pinned-Site' ) . ';action-uri=' . admin_url() . 'post-new.php;icon-uri=' . plugins_url('media/post.ico', __FILE__) . '" />'."\r\n";
			}
			if( current_user_can( 'moderate_comments' ) ) {
				echo '<meta name="msapplication-task" content="name=' . __( 'Moderate comments', 'IE9-Pinned-Site' ) . ';action-uri=' . admin_url() . 'edit-comments.php?comment_status=moderated;icon-uri=' . plugins_url('media/comment.ico', __FILE__) . '" />'."\r\n";
			}
			if( current_user_can( 'upload_files' ) ) {
				echo '<meta name="msapplication-task" content="name=' . __( 'Upload new media', 'IE9-Pinned-Site' ) . ';action-uri=' . admin_url() . 'media-new.php;icon-uri=' . plugins_url('media/media.ico', __FILE__) . '" />'."\r\n";
			}
		}else{
			$pages = get_pages("sort_column='menu_order'");
			foreach($pages as $pagg) {
			   echo '<meta name="msapplication-task" content="name=' . $pagg->post_title . ';action-uri=' . get_page_link($pagg->ID) . ';icon-uri=' . plugins_url('media/post.ico', __FILE__) . '" />'."\r\n";
			}
		}
		
		$jump_list_type = get_option('ie9_pinned_site_jump_list_type');
		
		if ('none' != $jump_list_type){
			echo '<script type="text/javascript">' . "\r\n";
			echo 'if (window.external.msIsSiteMode()){' . "\r\n";
			
			if ('posts' == $jump_list_type){
				echo 'window.external.msSiteModeCreateJumplist("' . __( 'Recent posts', 'IE9-Pinned-Site' ) . '");'."\r\n";

				$lastestposts = get_posts('numberposts=' . get_option('ie9_pinned_site_post_count'));
				foreach($lastestposts as $post) {
					echo 'window.external.msSiteModeAddJumpListItem("' . $post->post_title . '", "' . get_permalink( $post->ID ) . '", "' . plugins_url('media/post.ico', __FILE__) . '");'."\r\n";
				}
			}elseif ('categories' == $jump_list_type){
				echo 'window.external.msSiteModeCreateJumplist("' . __( 'Categories', 'IE9-Pinned-Site' ) . '");'."\r\n";
				$categories = get_categories('number=' . get_option('ie9_pinned_site_category_count'));
				foreach($categories as $category) {
					echo 'window.external.msSiteModeAddJumpListItem("' . $category->cat_name . '", "' . get_category_link( $category->term_id ) . '", "' . plugins_url('media/post.ico', __FILE__) . '");'."\r\n";
				}
			}elseif ('tags' == $jump_list_type){
				echo 'window.external.msSiteModeCreateJumplist("' . __( 'Tags', 'IE9-Pinned-Site' ) . '");'."\r\n";
				$tags = get_tags('number=' . get_option('ie9_pinned_site_tag_count'));
				foreach($tags as $tag) {
					echo 'window.external.msSiteModeAddJumpListItem("' . $tag->name  . '", "' . get_tag_link($tag->term_id) . '", "' . plugins_url('media/post.ico', __FILE__) . '");'."\r\n";
				}
			}
			
			echo 'window.external.msSiteModeShowJumplist();'."\r\n";
			echo '}'."\r\n";
			echo '</script>'."\r\n";
		}
	}
	
	echo '<!-- /IE9 Pinned Site -->'."\r\n";
}