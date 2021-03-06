<?
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

/*
 * auto include classes
 */
function __autoload($class_name) {
	if (class_exists ( $class_name )) {
		return true;
	}
	
	$file = ENGINE_PATH . "classes/" . $class_name . ".class.php";
	if (file_exists ( $file )) {
		require_once ($file);
	} elseif (isset ( $_GET ['module'] ) && file_exists ( ROOT_PATH . "/scripthub/apps/" . $_GET ['module'] . "/models/" . $class_name . ".class.php" )) {
		require_once ROOT_PATH . "/scripthub/apps/" . $_GET ['module'] . "/models/" . $class_name . ".class.php";
	} elseif (isset ( $_GET ['module'] ) && isAdministration ( $_GET ['module'] ) && isset ( $_GET ['m'] ) && file_exists ( ROOT_PATH . "/scripthub/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php" )) {
		//auto include for administration
		require_once ROOT_PATH . "/scripthub/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php";
	} elseif (isset ( $_GET ['module'] ) && isAdministration ( $_GET ['module'] ) && isset ( $_GET ['m'] ) && file_exists ( ROOT_PATH . "/scripthub/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php" )) {
		require_once ROOT_PATH . "/scripthub/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php";
	} /*else {
				require_once ROOT_PATH . "classes/" . $class_name . ".class.php";
	}*/
	
	return true;
}

function check_email($address) {
	return (preg_match ( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+' . '@' . '([-0-9A-Z]+\.)+' . '([0-9A-Z]){2,4}$/i', trim ( $address ) ));
}

/*
 * Return execute time
 */
function execute_time() {
	global $start_time;
	return (microtime ( true ) - $start_time);
}

/*
 * smarty assign by ref - shortcut
 */
function abr($var, $value) {
	global $smarty;
	$smarty->assign_by_ref ( $var, $value );
}

/*
 * Replace variables in text
 */
function langMessageReplace($message, $vars) {
	if (preg_match_all ( '/\{\$(?P<named>[0-9A-Z_\-]*)\}/simx', $message, $result, PREG_PATTERN_ORDER )) {
		//var_dump ( $result ['named'] );
		foreach ( $result ['named'] as $v ) {
			if (isset ( $vars [$v] )) {
				//echo $v . "<br/>";
				$message = str_ireplace ( '{$' . $v . '}', $vars [$v], $message );
			}
		}
	}
	unset ( $vars );
	return $message;
}


/*
 * Get URL variable
 */
function get_id($level) {
	global $config;
	
	if($config['use_language']) {
		$level = $level + 1;
	}
	
	if (! isset ( $_GET ['array_url'] [$level] )) {
		return false;
	}
	
	if (strstr ( $_GET ['array_url'] [$level], "." )) {
		$all = explode ( ".", $_GET ['array_url'] [$level] );
	} else {
		$all [0] = $_GET ['array_url'] [$level];
	}
	return $all [0];
	
	return $_GET ['array_url'] [$level];
}

/*
 * Create text editor
 */
function createTextAreaEditor($params, &$smarty) {
	global $config;
	
	require_once $config ['data_server_path'] . '/js/fckeditor/fckeditor.php';
	
	$params ['name'] = str_replace ( "\\'", '', $params ['name'] );
	
	$oFCKeditor = new FCKeditor ( $params ['name'] );
	$oFCKeditor->BasePath = $config ['data_server'] . '/js/fckeditor/';
	$oFCKeditor->Width = $params ['width'];
	$oFCKeditor->Height = $params ['height'];
	$oFCKeditor->Value = $params ['value'];
	$oFCKeditor->ToolbarSet = 'e7Toolbar';
	$oFCKeditor->AutoDetectPasteFromWord = true ;	
	$oFCKeditor->Create ();
}

function remove_whitespace($str) {
	
	$str = str_replace ( ' ', '+', $str );
	$str = str_replace ( '%20', '+', $str );
	$str = str_replace ( '%', '', $str );
	$str = str_replace ( '/', '+', $str );
	return $str;
}

//Refresh page with or without message
function refresh($url = '', $message = '', $type = 'succes') {
	global $sitemapClass;
	
	if ($url == '') {
		$_SERVER ['REQUEST_URI'] = str_ireplace ( "//", "/", $_SERVER ['REQUEST_URI'] );
		$url = 'http://' . $_SERVER ["SERVER_NAME"] . '' . $_SERVER ['REQUEST_URI'];
	}
	
	if ($message != '') {
		$_SESSION ['temp'] ['message_title'] = $message;
	}
	
	$_SESSION ['temp'] ['message_type'] = $type;
	
	//if is define sitemap class
	if(isset($sitemapClass) && $_GET['module'] == 'admin' && (isset($_POST['add']) || isset($_POST['edit']))) {
		$sitemapClass->regenerateSiteMap();
	}
	
	
	header ( "Location: $url" );
	die ();
}

//Redirect to login
function login_redirect($url, $fromUrl = '', $message='') {
	if ($fromUrl == '')
		$_SESSION ['redirectUrl'] = '/' . @$_GET ['url'];
	else
		$_SESSION ['redirectUrl'] = $fromUrl;
	
	if ($message != '') {
		$_SESSION ['temp'] ['message_title'] = $message;
		$_SESSION ['temp'] ['message_type'] = 'error';
	}
		
	header ( "Location: $url" );
	die ();
}

function getRefreshMessage() {
	
	if (isset ( $_SESSION ['temp'] ['message_title'] )) {
		$message['title'] = $_SESSION ['temp'] ['message_title'];
		unset ( $_SESSION ['temp'] ['message_title'] );
		
		if (isset ( $_SESSION ['temp'] ['message_text'] )) {
			$message['text'] = $_SESSION ['temp'] ['message_text'];
			unset ( $_SESSION ['temp'] ['message_text'] );
		}
		else {
			$message['text'] = '';
		}
				
		if (isset ( $_SESSION ['temp'] ['message_type'] )) {
			$message['type'] = $_SESSION ['temp'] ['message_type'];
			unset ( $_SESSION ['temp'] ['message_type'] );
		}
		else {
			$message['type'] = '';
		}
				
		return $message;
	}
	
	return false;
}

function truncate($str, $length = 10, $trailing = '...') {
	/*
** $str -String to truncate
** $length - length to truncate
** $trailing - the trailing character, default: "..."
*/
	// take off chars for the trailing
	$length -= mb_strlen ( $trailing );
	if (mb_strlen ( $str ) > $length) {
		// string exceeded length, truncate and add trailing dots
		return mb_substr ( $str, 0, $length ) . $trailing;
	} else {
		// string was already short enough, return the string
		$res = $str;
	}
	return $res;
}



function addErrorMessage($title, $msg = "", $type = "notice") {
	global $smarty;
	
/*	$msg = $smarty->get_template_vars ( 'errorMessage' ) . '
	<div class="errorMessage errorMessage_' . $type . '">
		<div class="close"></div>
		<div class="errorContent">
			<div class="em-title">' . $title . '</div>
			' . $msg . '
		</div>
	</div>';*/
		
	switch($type) {
		case 'notice':
			$class = 'warning';
			break;
		case 'error':
			$class = 'error';
			break;
		case 'info':
			$class = 'info';
			break;

		default:
			$class = 'success';
			break;
	}
	
		$msg = $smarty->get_template_vars ( 'errorMessage' ) . '
			<div class="box box-'.$class.'">'.$title.' '.$msg.'</div>';
		
	
	$smarty->assign_by_ref ( "errorMessage", $msg );
	
	return true;
}

function check_login($type = 'user') {
	global $_SESSION, $langArray;
	
	if (! isset ( $_SESSION [$type] )) {
		refresh ( '/sign_in/' );
	}
	
	return true;
}

function check_login_bool($type = 'user') {
	global $_SESSION;
	
	if (! isset ( $_SESSION [$type] )) {
		return false;
	}
	return true;
}

/*
 * Check admin session
 */
function admin_login() {
	global $config, $languageURL;
	
	if (! isset ( $_SESSION ['user'] ['is_admin'] )) {
		refresh ( "/" . $languageURL . adminURL . "/login/?" . __FUNCTION__ );
		die ( 'Do not access !!!' );
	}
	
	return true;
}

function admin_login_bool() {
	global $_SESSION;
	
	if (! isset ( $_SESSION ['user'] ['is_admin'] )) {
		return false;
	}
	return true;
}

function url($url) {
	$url = strip_tags ( $url );
	$url = trim ( $url );
	$url = preg_replace ( '%[.,:\'"/\\\\[\]{}\%\-_!?]%simx', ' ', $url );
	$url = str_ireplace ( " ", "-", $url );
	
	return $url;
}

function isAdministration($module) {
	if ($module == adminURL) {
		return true;
	} else {
		return false;
	}
}

function recursive_mkdir($folder) {
	//echo $folder;
	$folder = explode ( DIRECTORY_SEPARATOR, $folder );
	//var_dump($folder);
	$mkfolder = '';
	for($i = 0; isset ( $folder [$i] ); $i ++) {
		if ($folder [$i] != "") {
			//echo $folder[$i]."-folder<BR>";
			$mkfolder .= $folder [$i];
			if (!@is_dir ( $mkfolder )) {
				@mkdir ( $mkfolder, 0775 );
			}
			$mkfolder .= DIRECTORY_SEPARATOR;
			//echo $mkfolder ."-mkfolder<BR>";
		} else {
			$mkfolder .= DIRECTORY_SEPARATOR;
		}
	}
	return true;
}

function recursive_rmdir($dir, $DeleteMe = true) {
	if (! $dh = @opendir ( $dir )) {
		return;
	}
	
	while ( false !== ($obj = readdir ( $dh )) ) {
		if ($obj == '.' || $obj == '..') {
			continue;
		}
		
		if (! @unlink ( $dir . '/' . $obj )) {
			recursive_rmdir ( $dir . '/' . $obj, true );
		}
	}
	
	closedir ( $dh );
	if ($DeleteMe) {
		@rmdir ( $dir );
	}
}

function check_debug() {
	global $config;
	
	if (in_array ( $_SERVER ['REMOTE_ADDR'], $config ['debug_ips'] ) && $config ['debug'] === true) {		
		return true;
	}
	return false;
}

function add_debug($val) {
	global $debug;
	$debug .= $val . "<BR /><BR />";
	return true;
}

?>