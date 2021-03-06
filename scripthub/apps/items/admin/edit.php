<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView ( __FILE__ );
_setTitle ( $langArray ['edit'] );

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '';
	}	
	
	$cms = new items();
	
	require_once ROOT_PATH.'/scripthub/apps/users/models/users.class.php';
	$usersClass = new users();
	
	$data = $cms->get($_GET['id'], false);
	$data['user'] = $usersClass->get($data['user_id']);
	abr('data', $data);
	
#LOAD ATTRIBUTES
	require_once ROOT_PATH.'/scripthub/apps/attributes/models/attributes.class.php';
	$attributesClass = new attributes();
	
	if(isset($data['categories'][0]) && is_array($data['categories'][0])) {
		$first_category = array_shift($data['categories'][0]);
	} else {
		$first_category = 0;
	}
	
	$attributes = $attributesClass->getAllWithCategories(" `visible` = 'true' AND `categories` LIKE '%,".(int)$first_category.",%' ");
	abr('attributes', $attributes);
	
	if(isset($_POST['edit'])) {
		$status = $cms->edit ($_GET['id'], true);
		if ($status !== true) {
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=list&p=".$_GET['p'], $langArray ['edit_complete'] );
		}
	}
	else {
		$_POST = $data;
	}	
	
#LOAD CATEGORIES
	require_once ROOT_PATH.'/scripthub/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	if(!isset($_POST['categories'])) {
		$_POST['categories'] = array();
	}
	
	$tmp = array();
	if(isset($_POST['category'])) {
		$tmp = (array)$_POST['category'];
	} else {
		foreach($_POST['categories'] AS $row => $categories1) {
			$cid = end($categories1);
			$tmp[$cid] = $cid;
		} 
	}

	
	$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
	$categoriesSelect = $categoriesClass->generateSelect($allCategories, $tmp, (int)$first_category);
	abr('categoriesSelect', $categoriesSelect);
	
?>