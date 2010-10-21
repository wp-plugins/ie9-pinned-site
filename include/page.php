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

class ie9PinnedSitePage {
	var $pageId;
	
	var $entryType;
	var $order;
	
	function ie9PinnedSitePage($data, $entryType, $order) {
		$this->pageId = $data->pageId;
		
		$this->entryType = $entryType;
		$this->order = $order;
	}


	function render4Task(){
    }
	
	function render4JumpList(){
		$pagg = get_page( $this->pageId );
		
		echo 'window.external.msSiteModeAddJumpListItem("' . $pagg->post_title . '", "' .  get_page_link( $this->pageId ) . '", "' . plugins_url('images/post.ico', dirname(__FILE__)) . '");'."\r\n";
	}
	
	function render4AdminPage(){
		$pagg = get_page( $this->pageId );
		$prefix = $this->entryType . '_' . $this->order;
		?>
        <li name="<?php echo $prefix ?>" id="<?php echo $prefix ?>">
			<span class="title"><?php echo $pagg->post_title ?></span>
            <span class="settings">
            	<span class="entry-type">Page</span>
                <a class="delete" value="<?php echo $this->order ?>" onClick="deleteEntry('<?php echo $this->entryType ?>', <?php echo $this->order ?>)" id="delete<?php echo $this->entryType . $this->order ?>">
                	<img src="<?php echo plugins_url('images/delete.png',dirname(__FILE__))?>" title="Delete <?php echo $this->entryType ?> Entry" alt="Delete <?php echo $this->entryType ?> Entry" class="delete">
                </a>
            </span>
			<input type="hidden" id="<?php echo $prefix ?>_type" name="<?php echo $prefix ?>_type" value="page">
			<input type="hidden" id="<?php echo $prefix ?>_pageId" name="<?php echo $prefix ?>_pageId" value="<?php echo $this->pageId ?>">
		</li>
        <?php
	}
	
	function GetSettingObject($entryType, $index){
		$pageEntry =  new stdClass();
		$pageEntry->type = 'Page';
		$pageEntry->pageId = is_numeric($_POST[$entryType . '_' . $index . '_pageId']) ? $_POST[$entryType . '_' . $index . '_pageId'] : -1;
		
		return $pageEntry;
	}
}
?>