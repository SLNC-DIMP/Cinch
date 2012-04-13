<?php
/**
* Blows up command if not explcitly called.  I believe because MakeCsv isn't named MakeCsvCommand
* Don't want it named that as would show up as a command instead of merely being clase for others to descend from.
*/
Yii::import('application.models.MakeCsv');
Yii::import('application.models.Utils');

class MetadataCsvCommand extends CConsoleCommand {
	public $compare = '/(id|id$)/i';
	public $checksum;
	public $makecsv;
	
	public function __construct() {
		$this->checksum = new Checksum;
		$this->makecsv = new MakeCsv;
	}
	
	/**
	* Gets all downloaded files that have been fully processed.
	* @todo get this working "AND virus_check != 0"
	* Ignores all error files
	* Want to add these files
	* @access public
	* @return object Yii DAO object
	*/
	public function getFiles() {
		$sql = "SELECT id, checksum, file_type_id, user_id 
		FROM file_info 
		WHERE (temp_file_path IS NOT NULL OR temp_file_path != '')
		AND virus_check = 1
		AND checksum_run = 1
		AND metadata = 1
		AND zipped != 1";
		
		$user_files = Yii::app()->db->createCommand($sql)
			->queryAll();
	
		return $user_files;
	}
	
	/**
	* Gets fulltext status for file.
	* @access public
	* @return string
	*/
	public function getFulltext($file_id) {
		$full_text = Yii::app()->db->createCommand()
			->select('fulltext_available')
			->from('file_info')
			->where('id = :id', array(':id' => $file_id))
			->queryColumn();
		
		$text_extractable = ( $full_text[0] == 1) ? 'Yes' : 'No';
		
		return $text_extractable;
	}
	
	/**
	* Returns correct table for the metadate being retrieved.
	* @param $meta_type
	* @access public
	* @return string
	*/
	public function findMetaTable($meta_type) {
		switch($meta_type) {
			case 1:
				$table = 'PDF_Metadata';
				break;
			case 2:
			case 3:
				$table = 'Word_Metadata';
				break;
			case 5:
				$table = 'Jpg_Metadata';
				break;
			case 6:
				$table = 'Gif_Metadata';
				break;
			case 7:
				$table = 'Text_Metadata';
				break;
			case 8:
			case 9:
				$table = 'Excel_Metadata';
				break;
			case 10:
				$table = 'PNG_Metadata';
				break;
			case 11:
			case 12:
				$table = 'PPT_Metadata';
				break;
			default:
				$table = false;
				break;
		}
		
		return $table;
	}
	
	/**
	* Returns extracted metadata for a file
	* @param $meta_type
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object.  Yii DAO object
	*/
	public function findMetadata($table_name, $file_id, $user_id) {
		$metadata = Yii::app()->db->createCommand()
			->select('*')
			->from("$table_name")
			->where(':file_id = file_id and :user_id = user_id',
			  array(':file_id' => $file_id, ':user_id' => $user_id))
			->queryAll();		
		
		$fields = array();
		foreach($metadata as $key => $value) {
			$fields[$key] = $value;
		}
		return $fields;
	}
	
	/**
	* Gets table fields for column headers for metadata CSV files
	* Removes id fields
	* Add file checksum to the array
	* See http://stackoverflow.com/questions/5428262/php-pdo-get-the-columns-name-of-a-table
	* @param $table_name
	* @access protected
	* @return array
	*/
	protected function findFieldnames($table_name) {
		$sql = "DESCRIBE " . $table_name;
		$fields = Yii::app()->db->createCommand($sql)
			->queryAll(PDO::FETCH_COLUMN);
		
		$columns = array();
		
		foreach($fields as $field) {
			$col_name = $field['Field'];
			if(!preg_match($this->compare, $col_name)) {
				$columns[] = $col_name;
			}
		}
		$columns[] = 'Checksum';
		$columns[] = 'Fulltext';
		
		return $columns;
	}
	
	/**
	* Writes column headers, returned metadata and checksum to a .csv file
	* @param $metadata
	* @access public
	*/
	public function write($user_path, $table_name, $metadata, $user_id) {
		$file_path = $user_path . '/' . $table_name . '_' . date('Y_m_d') . '.csv';
		$column_headers = array();
		
		if(!file_exists($file_path)) {
			$column_headers = $this->findFieldnames($table_name);
		}
		
		$fh = fopen($file_path, 'ab');
		
		if(!empty($column_headers)) {
			fputcsv($fh, $column_headers);
			$this->makecsv->addPath($user_id, $file_path);
		}
		
		foreach($metadata as $row) {
			$row['checksum'] = $this->checksum->getOneFileChecksum($row['file_id']);
			$row['fulltext'] = $this->getFulltext($row['file_id']);
			
			foreach($row as $key => $value) {
				if(preg_match($this->compare, $key)) {
					unset($row[$key]);
				}
			}
			
			fputcsv($fh, $row);
		}
		
		fclose($fh);
	}
	
	public function actionIndex() {
		$files = $this->getFiles();
	
		if(!empty($files)) {  
			foreach($files as $file) {
				$csv_path = $this->makecsv->getUserPath($file['user_id']);
				$metadata_table = $this->findMetaTable($file['file_type_id']);
				if($metadata_table) {
					$metadata = $this->findMetadata(
						$metadata_table, 
						$file['id'], 
						$file['user_id']
					);
					
					$this->write($csv_path, $metadata_table, $metadata, $file['user_id']);
					echo "File " . $file['id'] . " added to $metadata_table to \r\n";
					echo "$csv_path\r\n";
				}
			}
		}
	}
}