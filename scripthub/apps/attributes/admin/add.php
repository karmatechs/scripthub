<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView ( __FILE__ );
_setTitle ( $langArray ['add'] );

	$cms = new categories ( );
	
	if (isset ( $_POST ['add'] )) {
		$status = $cms->add ();
		if ($status !== true) {
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=list", $langArray ['add_complete'] );
		}
	}
	else {
		$_POST['visible'] = 'true';
	}
	
#LOAD MAIN CATEGORIES
	$mysql->query("
		SELECT *
		FROM `categories`
		WHERE `sub_of` = '0'
		ORDER BY `order_index` ASC
	", __FUNCTION__ );
	
	if($mysql->num_rows() > 0) {
		while($d = $mysql->fetch_array()) {
			$categories[$d['id']] = $d;
		}		
		abr('categories', $categories);
	}
	
?>