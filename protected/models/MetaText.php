<?php
class MetaText {
	public $compare = '/(id|id$)/i';
	/**
	* Returns correct table for the metadate being retrieved.
	* @param $meta_type
	* @access public
	* @return string
	*/
	public function findMetaTable($meta_type) {
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
	public function findMetadata($table_name, $file_id, $user_id) {
		$metadata = Yii::app()->db->createCommand()
			->select('*')
			->from("$table_name")
			->where(':file_id = file_id and :user_id = user_id',
			  array(':file_id' => $file_id, ':user_id' => $user_id))
			->queryAll();
			
		
		foreach($metadata as $key => $value) {
			$fields[$key] = $value;
		}
		return $fields;
	}
	
	/**
	* Gets table fields for column headers for metadata CSV files
	* Removes id fields
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
		
		return $columns;
	}
	
	/**
	* Writes column headers and returned metadata to a .csv file
	* @param $metadata
	* @access public
	*/
	public function write($user_path, $table_name, $metadata) {
		$file_path = $user_path . '/' . $table_name . '.csv';
		$column_headers = array();
		
		if(!file_exists($file_path)) {
			$column_headers = $this->findFieldnames($table_name);
		}
		
		$fh = fopen($file_path, 'ab');
		
		if(!empty($column_headers)) {
			fputcsv($fh, $column_headers);
		}
		
		foreach($metadata as $row) {
			foreach($row as $key => $value) {
				if(preg_match($this->compare, $key)) {
					unset($row[$key]);
				}
			}
			
			fputcsv($fh, $row);
		}
		fclose($fh);
	}
}