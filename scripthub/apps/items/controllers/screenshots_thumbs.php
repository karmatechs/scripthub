<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView(__FILE__);
_setLayout("screenshots");
	
	$itemID = get_id(2);
	
	$itemsClass = new items();
	
	$item = $itemsClass->get($itemID);
	if(!is_array($item) || (check_login_bool() && $item['status'] == 'unapproved' && $item['user_id'] != $_SESSION['user']['user_id']) || $item['status'] == 'queue' || $item['status'] == 'extended_buy') {
		die();
	}
	abr('item', $item);
	
	$files = scandir(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/');
	$previewFiles = array();
	if(is_array($files)) {
		foreach($files as $f) {
			if(file_exists(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f)) {
				$fileInfo = pathinfo(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f);
				
				if( isset($fileInfo['extension']) && ( strtolower($fileInfo['extension']) == 'jpg' || strtolower($fileInfo['extension']) == 'png' ) ) {
					$previewFiles[] = $f;					
				}
			}
		}
	}
	abr('previewFiles', $previewFiles);

?>