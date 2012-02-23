<?php
class PPT_Metadata extends FileTypeActiveRecord {
	/**
	* Writes extracted Microsoft PowerPoint metadata to the database
	* @param $metadata
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object.  Yii DAO object
	*/
	public function writeMetadata(array $metadata, $file_id, $user_id) {
		$possible_fields = array(
			'Application-Name' => 'app_name',
			'Application-Version' => 'app_version',
			'Author' => 'author',
			'Comments' => 'comments',
			'Content-Type' => 'content_type',
			'Creation-Date' => 'creationdate',
			'Last-Author' => 'last_author',
			'Last-Modified' => 'last_modified',
			'Last-Save-Date' => 'last_save_date',
			'Keywords' => 'keywords',
			'Slide-Count' => 'slide_count',
			'Template' => 'template',
			'publisher' => 'publisher',
			'resourceName' => 'resource_name', 
			'subject' => 'subject',
			'title' => 'title',
			'xmpTPg' => 'pages',
			'doc_title' => 'possible_doc_title',
			'doc_keywords' => 'possible_doc_keywords',
			'file_id' => 'file_id',
			'user_id' => 'user_id'
		);
		
		$actual_fields = $this->returnedFields($possible_fields, $metadata);
		$query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
		$full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
		$bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);
		
		$sql = 'INSERT INTO PPT_Metadata(' . $this->queryBuilder($query_fields) . ') 
			VALUES(' . $this->queryBuilder($query_fields, true) . ')';
		
		$write_files = Yii::app()->db->createCommand($sql);
		$done = $write_files->execute($bind_params);
	}
}