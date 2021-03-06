<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView(__FILE__);
_setTitle($langArray['queue']);

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=queue_update', 'WRONG ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '';
	}
	
	$cms = new items ( );
	
	require_once ROOT_PATH.'/scripthub/apps/users/models/users.class.php';
	$usersClass = new users();
	
	$data = $cms->getForUpdate($_GET['id']);
	abr('data', $data);
	
	$item = $cms->get($data['item_id']);
	if(!is_array($item)) {
		refresh('?m='.$_GET['m'].'&c=queue_update', 'WRONG ID', 'error');
	}
	$item['user'] = $usersClass->get($item['user_id']);
	abr('item', $item);	
	
	if(isset($_POST['submit'])) {
		
		if($_POST['action'] == 'approve') {
			$s = $cms->approveUpdate($_GET['id']);
			if($s === true) {
				refresh("?m=".$_GET['m']."&c=queue_update&p=".$_GET['p'], $langArray['complete_approve_item_update']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}
		elseif($_POST['action'] == 'delete') {
			$s = $cms->unapproveDeleteUpdate($_GET['id']);
			if($s === true) {
				refresh("?m=".$_GET['m']."&c=queue_update&p=".$_GET['p'], $langArray['complete_delete_item_update']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}		
	}
	
?>