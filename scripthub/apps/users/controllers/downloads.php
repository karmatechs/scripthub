<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView(__FILE__);
_setTitle($langArray['downloads_setTitle']);

if(!check_login_bool()) {
	$_SESSION['temp']['golink'] = '/'.$languageURL.'download/';
	refresh('/'.$languageURL.'sign_in/');
}

	require_once ROOT_PATH.'/scripthub/apps/items/models/orders.class.php';
	$ordersClass = new orders();

#DOWNLOAD ITEM
	$itemID = get_id(2);
	if(is_numeric($itemID)) {
		
		require_once ROOT_PATH.'/scripthub/apps/items/models/items.class.php';
		$itemsClass = new items();
		
		$item = $itemsClass->get($itemID);
		if(!is_array($item) || (check_login_bool() && $item['status'] == 'unapproved' && $item['user_id'] != $_SESSION['user']['user_id']) || $item['status'] == 'queue') {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
		}
		
		if(isset($_POST['rating'])) {
			$_GET['rating'] = $_POST['rating'];
		}
			
		if(isset($_GET['rating'])) {
			if(!isset($_GET['rating']) || !is_numeric($_GET['rating']) || $_GET['rating'] > 5) {
				$_GET['rating'] = 5;
			}
			elseif($_GET['rating'] < 1) {
				$_GET['rating'] = 1;
			}
			
			$item = $itemsClass->rate($itemID, $_GET['rating']);
			
			$stars = '';
			for($i=1;$i<6;$i++) {
				if($item['rating'] >= $i) {
					$stars .= '<img src="/static/img/star-on.png" alt="" />';
				}
				else {
					$stars .= '<img src="/static/img/star-off.png" alt="" />';
				}
			}
			
			die('
				jQuery("#stars_div_'.$itemID.'").html(\''.$stars.'\');
			');
		}
		elseif(isset($_GET['certificate'])) {
			if($ordersClass->isBuyed($item['id'])) {
				header('Content-Type: text/plain; charset=UTF-8');
				header('Content-Disposition: attachment; filename="item_licence.txt"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				@ob_clean();
				@flush();
				
				if($ordersClass->row['extended'] == 'true') {
					$licence = $langArray['one_extended_licence'];
				}
				else {
					$licence = $langArray['one_regular_licence'];
				}
				
				$usersClass = new users();
				$user = $usersClass->get($item['user_id']);				
				
				echo langMessageReplace($langArray['licence_text'], array(
																'DOMAIN' => $config['domain'],
																'LICENCE' => $licence,
																'USERNAME' => $user['username'],
																'FIRSTNAME' => $_SESSION['user']['firstname'],
																'LASTNAME' => $_SESSION['user']['lastname'],
																'ITEMNAME' => $item['name'],
																'ITEMID' => $item['id'],
																'LANGUAGEURL' => $languageURL,
																'ORDERID' => $ordersClass->row['id']				
															));
				die();
			}
			else {
				refresh('/'.$languageURL.'download/', $langArray['error_certificate'], 'error');
			}
		}
		if($ordersClass->isBuyed($item['id']) || $item['free_file'] == 'true') {
			if(file_exists(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file'])) {				
				$fileInfo = pathinfo(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']);
				
				$mimeTypes = array (
						'zip' => 'application/zip'
				);
				
				if(isset($mimeTypes[$fileInfo['extension']])) {
					header('Content-Type: '.$mimeTypes[$fileInfo['extension']]);
				} else {
					header('Content-Type: application/octet-stream');
				}
				
				header('Content-Disposition: attachment; filename="'.$item['main_file_name'].'"');
				header("Content-Length:".filesize(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']));
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Content-Description: '.$config['domain'].' Download');
				@ob_clean();
				@flush();
				
				readfile(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']) or die("ERROR!");
				die();
			}
			
		}
		else {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
		}
		
	}

#LOAD ITEMS
	$items = $ordersClass->getAllBuyed(" `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `paid` = 'true' ");
	abr('items', $items);
	
	
	$ratedItems = $itemsClass->getRates(str_replace('`id`', '`item_id`', $ordersClass->whereQuery));
	abr('ratedItems', $ratedItems);
	
#BREADCRUMB	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a> \ <a href="/'.$languageURL.'users/downloads/" title="">'.$langArray['downloads'].'</a>');		
	

?>