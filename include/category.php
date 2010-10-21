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

class ie9PinnedSiteCategory {
	var $termId;
	
	var $entryType;
	var $order;
	
	function ie9PinnedSiteCategory($data, $entryType, $order) {
		$this->termId = $data->termId;
		
		$this->entryType = $entryType;
		$this->order = $order;
	}
	
	function render4Task(){
	}
	
	function render4JumpList(){
		$category = &get_term($this->termId, 'category');
		
		echo 'window.external.msSiteModeAddJumpListItem("' . $category->name  . '", "' .  get_category_link( $this->termId ) . '", "' . plugins_url('images/post.ico', dirname(__FILE__)) . '");'."\r\n";
	}
	
	function render4AdminPage(){
		$category = &get_term($this->termId, 'category');
		$prefix = $this->entryType . '_' . $this->order;
		
		?>
        <li name="<?php echo $prefix ?>" id="<?php echo $prefix ?>">
        	<span class="title"><?php echo $category->name ?></span>
            <span class="settings">
            	<span class="entry-type">Category</span>
                <a class="delete" value="<?php echo $this->order ?>" onClick="deleteEntry('<?php echo $this->entryType ?>', <?php echo $this->order ?>)" id="<?php echo $this->entryType . $this->order ?>">
                	<img src="<?php echo plugins_url('images/delete.png',dirname(__FILE__))?>" title="Delete <?php echo $this->entryType ?> Entry" alt="Delete <?php echo $this->entryType ?> Entry" class="delete">
                </a>
            </span>
			<input type="hidden" id="<?php echo $prefix ?>_type" name="<?php echo $prefix ?>_type" value="Category">
			<input type="hidden" id="<?php echo $prefix ?>_termId" name="<?php echo $prefix ?>_termId" value="<?php echo $this->termId ?>">
		</li>
        <?php
	}
	
	function GetSettingObject($entryType, $index){
		$categoryEntry =  new stdClass();
		$categoryEntry->type = 'Category';
		$categoryEntry->termId = is_numeric($_POST[$entryType . '_' . $index . '_termId']) ? (int)$_POST[$entryType . '_' . $index . '_termId'] : -1;
		
		return $categoryEntry;
	}
}
?>