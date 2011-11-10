<?php
class MetaText extends CActiveRecord {
	/**
	* Returns correct table for the metadate being retrieved.
	* @param $meta_type
	* @access private
	* @return string
	*/
	private function findMetaTable($meta_type) {
		switch($meta_type) {
			case 1:
				$table = 'pdf_metadata';
				break;
			case 2:
			case 3:
				$table = 'word_metadata';
				break;
			case 7:
				$table = 'text_metadata';
				break;
			default:
				$table = 'pdf_metadata';
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
	public function getMetadata($meta_type, $file_id, $user_id) {
		$table = $this->findMetaTable($meta_type);
		$sql = "SELECT * FROM $table WHERE file_id = ? AND user_id = ?";
		$metadata = Yii::app()->db->createCommand($sql)
			->execute(array($file_id, $user_id));
	}
	
	/**
	* Gets table fields for column headers for metadata CSV files
	* Removes id fields
	* See http://stackoverflow.com/questions/5428262/php-pdo-get-the-columns-name-of-a-table
	* @param $table_name
	* @access protected
	* @return array
	*/
	public function getFieldnames($table_name) {
		$sql = "DESCRIBE " . $table_name;
		$fields = Yii::app()->db->createCommand($sql)
			->queryAll(PDO::FETCH_COLUMN);
		
		$columns = array();
		
		foreach($fields as $field) {
			$col_name = $field['Field'];
			if(!preg_match('/(id|id$)/i', $col_name)) {
			//	echo $col_name . "\r\n";
				$columns[] = $col_name;
			}
		}
		
		return $columns;
	}
	
	/**
	* Writes returned metadata to a file
	* @param $metadata
	* @access public
	* @return object.  Yii DAO object
	*/
	public function write($metadata) {
		$fh = fopen($file_path, 'ab');
		
		foreach($metadata as $row) {
			fputcsv($fh, $row);
		}
		
		fclose($fh);
	}
}