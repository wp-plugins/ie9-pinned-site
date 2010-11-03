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

class ie9PinnedSiteCustomUrl {
	var $url;
	var $text;
	var $iconUrl;
	
	var $entryType;
	var $order;
	
	function ie9PinnedSiteCustomUrl($data, $entryType, $order) {
		$this->url = $data->url;
		$this->text = $data->text;
		$this->iconUrl = $data->iconUrl;
		
		$this->entryType = $entryType;
		$this->order = $order;
	}
	
	function render4Task(){
		echo '<meta name="msapplication-task" content="name=' . $this->text . ';action-uri=' . $this->url . ';icon-uri=' . $this->iconUrl . '" />'."\r\n";
	}
	
	function render4JumpList(){
		echo 'window.external.msSiteModeAddJumpListItem("' . $this->text  . '", "' .  $this->url . '", "' . $this->iconUrl . '");'."\r\n";
	}
	
	function render4AdminPage(){
		$prefix = $this->entryType . '_' . $this->order;
	
		?>
        <li name="<?php echo $prefix ?>" id="<?php echo $prefix ?>">
        	<span class="title"><?php echo$this->text ?></span>
            <span class="settings">
            	<span class="entry-type">url</span>
                <a value="<?php echo $this->order ?>" onclick="deleteEntry('<?php echo $this->entryType ?>', <?php echo $this->order ?>)" id="delete<?php echo $this->entryType . $this->order ?>">
                	<img src="<?php echo plugins_url('images/delete.png',dirname(__FILE__))?>" title="Delete <?php echo $this->entryType ?> Entry" alt="Delete <?php echo $this->entryType ?> Entry" class="delete">
                </a>
          </span>
          <input type="hidden" id="<?php echo $prefix ?>_type" name="<?php echo $prefix ?>_type" value="CustomUrl">
          <input type="hidden" id="<?php echo $prefix ?>_url" name="<?php echo $prefix ?>_url" value="<?php echo $this->url ?>">
          <input type="hidden" id="<?php echo $prefix ?>_text" name="<?php echo $prefix ?>_text" value="<?php echo $this->text ?>">
          <input type="hidden" id="<?php echo $prefix ?>_icon" name="<?php echo $prefix ?>_icon" value="<?php echo $this->iconUrl ?>">
		</li>
		<?php
	}
	
	function GetSettingObject($entryType, $index){
		$customUrlEntry =  new stdClass();
		$customUrlEntry->type = 'CustomUrl';
		$customUrlEntry->url = $_POST[$entryType . '_' . $index . '_url'];
		$customUrlEntry->text = $_POST[$entryType . '_' . $index . '_text'];
		$customUrlEntry->iconUrl = $_POST[$entryType . '_' . $index . '_icon'];
		
		return $customUrlEntry;
	}
}
?>