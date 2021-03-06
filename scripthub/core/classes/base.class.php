<?php
/**
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

class base {
	
	public $tableName = ''; //MySQL Table Name
	public $idColumn = 'id'; //MySQL Table ID Column
	public $orderWhere = ''; //Where Statement if you need
	
	public $uploadFileDirectory = 'files/'; //Set upload file directory (Default is "files/")
	public $maxFileSize = 10485760; //Maximum file size (Deafult is 10 MB)
	public $fileExt = array('jpg', 'png', 'gif'); //Required file extensions (Default is jpg, png and gif)
	public $photoSizes = null; // Set the photo sizes to resize image
	public $lastUploadedFile = ''; //Name of the last uploaded file by the script
	public $foundRows = 0; //Count of founded rows after sql query
	public $watermarkFile = ''; //Set the watermark file
	public $watermarkPosition = 'center'; //Set the watermark position [downright or center]
	
	
	/*
	 * CLASS CONSTRUCTOR
	 * Set default values on variables
	 */
	function __construct() {
		if(isset($_GET['m'])) {
			$this->tableName = $_GET['m'];
			$this->uploadFileDirectory = $_GET['m'].'/';
		}
	}
	
	
	/*
	 * ORDER
	 */
	
	public function getNextOrderIndex() {
		global $mysql;
		
		$mysql->query("
			SELECT `order_index`
			FROM `".$this->tableName."`
			WHERE 1=1 $this->orderWhere
			ORDER BY `order_index` DESC
			LIMIT 1
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return 1;
		}
		
		$index = $mysql->fetch_array();
		$index = $index['order_index'] + 1;
		
		return $index;		
	}
	
	public function moveUp($id, $type = 'DESC') {
		global $mysql;
		
		settype ( $id, 'int' );
		
		$mysql->query ( "
			SELECT *
			FROM `" . $this->tableName . "`
			WHERE `" . $this->idColumn . "` = '$id'
			LIMIT 1
		", __FUNCTION__ );
		
		$row = $mysql->fetch_array ();
		
		$mysql->query ( "
			SELECT *
			FROM `" . $this->tableName . "`
			WHERE 1=1 " . $this->orderWhere . "
			ORDER BY `order_index` $type
			LIMIT 1
		", __FUNCTION__ );
		
		$maxIndex = $mysql->fetch_array ();
		$maxIndex = $maxIndex ['order_index'];
		
		if ($row ['order_index'] < $maxIndex) {
			
			$mysql->query ( "
				SELECT *
				FROM `" . $this->tableName . "`
				WHERE `order_index` > '" . $row ['order_index'] . "' " . $this->orderWhere . "
				ORDER BY `order_index` ASC
				LIMIT 1
			", __FUNCTION__ );
			
			if ($mysql->num_rows () == 0) {
				return false;
			}
			
			$changeRow = $mysql->fetch_array ();						
			
			$mysql->query ( "
				UPDATE `" . $this->tableName . "`
				SET `order_index` = '" . $row ['order_index'] . "'
				WHERE `" . $this->idColumn . "` = '" . $changeRow [$this->idColumn] . "'
			", __FUNCTION__ );
			
			$mysql->query ( "
				UPDATE `" . $this->tableName . "`
				SET `order_index` = '" . $changeRow ['order_index'] . "'
				WHERE `" . $this->idColumn . "` = '" . $row [$this->idColumn] . "'
			", __FUNCTION__ );
		}
		
		return true;
	}
	
	public function moveDown($id, $type='ASC') {
		global $mysql;
		
		settype ( $id, 'int' );
		
		$mysql->query ( "
			SELECT *
			FROM `" . $this->tableName . "`
			WHERE `" . $this->idColumn . "` = '$id'
			LIMIT 1
		", __FUNCTION__ );
		
		$row = $mysql->fetch_array ();
		
		$mysql->query ( "
			SELECT *
			FROM `" . $this->tableName . "`
			WHERE 1=1 " . $this->orderWhere . "
			ORDER BY `order_index` $type
			LIMIT 1
		", __FUNCTION__ );
		
		$minIndex = $mysql->fetch_array ();
		$minIndex = $minIndex ['order_index'];
		
		if ($row ['order_index'] > $minIndex) {
			
			$mysql->query ( "
				SELECT *
				FROM `" . $this->tableName . "`
				WHERE `order_index` < '" . $row ['order_index'] . "' " . $this->orderWhere . "
				ORDER BY `order_index` DESC
				LIMIT 1
			", __FUNCTION__ );
			
			if ($mysql->num_rows () == 0) {
				return false;
			}
			
			$changeRow = $mysql->fetch_array ();
			
			$mysql->query ( "
				UPDATE `" . $this->tableName . "`
				SET `order_index` = '" . $row ['order_index'] . "'
				WHERE `" . $this->idColumn . "` = '" . $changeRow [$this->idColumn] . "'
			", __FUNCTION__ );
			
			$mysql->query ( "
				UPDATE `" . $this->tableName . "`
				SET `order_index` = '" . $changeRow ['order_index'] . "'
				WHERE `" . $this->idColumn . "` = '" . $row [$this->idColumn] . "'
			", __FUNCTION__ );
		}
		
		return true;
	}	
	
	
	function saveSort($array) {
		global $mysql;
		
		if(is_array($array)) {
			$orderIndex = 1;
			foreach($array as $id) {
				
				if(is_numeric($id)) {
					$mysql->query("
						UPDATE `".$this->tableName."`
						SET `order_index` = '".intval($orderIndex)."'
						WHERE `".$this->idColumn."` = '".intval($id)."'
						LIMIT 1
					");
					if($mysql->affected_rows() > 0) {
						echo "yes";
					}
					else {
						echo "no";
					}
					$orderIndex++;
				}
				
			}
		}
		
		return true;
	}
	
	
	
	
	
	/*
	 * UPLOAD FILES
	 */
	public function upload($field='file', $directoryExtend='', $required=false, $resize=false, $watermark='', $watermarkOver=0, $watermarkPosition='') {

		$file = '';
		if (isset ( $_FILES [$field] ['name'] ) && strlen ( $_FILES [$field] ['name'] ) > 3) {

			recursive_mkdir ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend );

			$upload = new upload ( );
			$upload->upload_form_field = $field;
			$upload->out_file_dir = DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend;
			$upload->max_file_size = $this->maxFileSize;
			$upload->make_script_safe = 1;
			$upload->allowed_file_ext = $this->fileExt;
			$upload->upload_process ();

			if ($upload->error_no) {

				switch ($upload->error_no) {
					case 1 :
						// No upload
						return "error_no_file_upload";
						exit ();

					case 2 :
						// Invalid file ext
						return "error_invalid_file_ext";
						exit ();

					case 3 :
						// Too big...
						return "error_file_too_big";
						exit ();

					case 4 :
						// Cannot move uploaded file
						return "error_no_file_upload";
						exit ();
				}

			}

			$file = substr ( $upload->saved_upload_name, strlen ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend ) );
			$this->lastUploadedFile = $file;

			if($resize) {
				$this->resizeImages ( $file, $directoryExtend, $watermark, $watermarkOver, $watermarkPosition );
			}
		
		} elseif($required) {
			return 'error_file_not_set';
		}

		return $file;
	}
	
	function resizeImages($file, $directoryExtend, $watermark='', $watermarkOver=0, $watermarkPosition='') {

		if (trim ( $file ) == '') {
			return false;
		}

		if (is_array ( $this->photoSizes )) {

			require_once ENGINE_PATH . 'classes/image.class.php';
			$image = new Image ( );
			
			if($watermark != '') {
				require_once ENGINE_PATH . 'classes/watermark.class.php';
				$wm = new watermark(DATA_SERVER_PATH . 'images/' . $watermark, $watermarkPosition);
			}			

			recursive_mkdir ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend );

			foreach ( $this->photoSizes as $key => $size ) {

				$image->enlarge = true;
				$resize = false;
				if(substr($size, 0, 1) == '_') {
					$resize = true;
					$size = substr($size, 1);
				}
				
				$sizes = explode ( 'x', $size );

				if ($sizes [0] > 0) {
					if ($sizes [1] == 0) {
						$image->resize ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $file, $sizes [0], 1000, DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $key . '_' . $file );
					} else {
						if($resize) {
							$image->enlarge = false;
							$image->resize ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $file, $sizes [0], $sizes [1], DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $key . '_' . $file );
						}
						else {
							$image->crop ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $file, $sizes [0], $sizes [1], DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $key . '_' . $file );
						}
					}
					
					if($watermark != '' && $watermarkOver <= $sizes[1]) {
						$wm->addWatermark(DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $directoryExtend . $key . '_' . $file);
					}
				}

			}

		}

		return true;

	}
	
	function watermark($file) {
		
		require_once ENGINE_PATH . 'classes/watermark.class.php';
		$wm = new watermark($this->watermarkFile, $this->watermarkPosition);
		
		$wm->addWatermark($file);
		
		return true;
	}
	
}

?>