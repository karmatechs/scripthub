<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

_setView ( __FILE__ );
_setTitle ( $langArray ['add'].' '.$langArray['country'] );

	$cms = new countries ( );
	
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
		
?>