<?php
class Jpg_Metadata extends FileTypeActiveRecord {
	/**
	* Writes extracted jpeg image metadata to the database
	* @param $metadata
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object.  Yii DAO object
	*/
	public function writeMetadata(array $metadata, $file_id, $user_id) {
		$possible_fields = array(
			'Color Space' => 'color_space',
			'Component 1' => 'component_one',
			'Component 2' => 'component_two', 
			'Component 3' => 'component_three',
			'Compression' => 'compression',
			'Content-Type' => 'content_type',
			'Data Precision' => 'data_precision',
			'Date/Time' => 'date_time',
			'Exif Image Height' => 'exif_image_height',
			'Exif Image Width' => 'exif_image_width',
			'Last-Modified' => 'last_modified', 
			'Number of Components' => 'number_of_components',
			'Orientation' => 'orientation',
			'Software' => 'software',
			'X Resolution' => 'x_resolution',
			'Y Resolution' => 'y_resolution',
			'resourceName' => 'resourcename', 
			'file_id' => 'file_id',
			'user_id' => 'user_id'
		);
		
		$actual_fields = $this->returnedFields($possible_fields, $metadata);
		$query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
		$full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
		$bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);
			
		$sql = 'INSERT INTO excel_metadata(' . $this->queryBuilder($query_fields) . ') 
				VALUES(' . $this->queryBuilder($query_fields, true) . ')';
			
		$write_files = Yii::app()->db->createCommand($sql);
		$done = $write_files->execute($bind_params);
	}
}