<?php
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

//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class ie9PinnedSitePublishPost {	
	var $entryType;
	var $order;
	
	function ie9PinnedSitePublishPost($data, $entryType, $order) {		
		$this->entryType = $entryType;
		$this->order = $order;
	}
	
	function render4Task(){
		if ( is_user_logged_in() && current_user_can( 'publish_posts' )) {
			echo '<meta name="msapplication-task" content="name=' . __( 'Write a post', 'IE9-Pinned-Site' ) . ';action-uri=' . admin_url() . 'post-new.php;icon-uri=' . plugins_url('images/post.ico', __FILE__) . '" />'."\r\n";
		}
	}
	
	function render4AdminPage(){
		$prefix = $this->entryType . '_' . $this->order;
		?>
        <li name="<?php echo $prefix ?>" id="<?php echo $prefix ?>">
        	<span class="title">Publish Post</span>
            <span class="settings">
            	<span class="entry-type">Custom Task</span>
                <a value="<?php echo $this->order ?>" onclick="deleteEntry('<?php echo $this->entryType ?>', <?php echo $this->order ?>)" id="delete<?php echo $this->entryType . $this->order ?>">
                	<img src="<?php echo plugins_url('images/delete.png', dirname(__FILE__))?>" title="Delete <?php echo $this->entryType ?> Entry" alt="Delete <?php echo $this->entryType ?> Entry" class="delete">
                </a>
            </span>
  			<input type="hidden" id="<?php echo $prefix ?>_type" name="<?php echo $prefix ?>_type" value="PublishPost">
		</li>
        <?php
	}
	
	function GetSettingObject($entryType, $index){
		$moderateCommentsEntry =  new stdClass();
		$moderateCommentsEntry->type = 'PublishPost';
		
		return $moderateCommentsEntry;
	}
}
?>