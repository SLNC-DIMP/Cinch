<?php
/**
* Word_Metadata class file
*
* Writes extracted MS Word metadata to the database.
* @catagory Word_Metadata
* @package Word_Metadata
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* Writes extracted MS Word metadata to the database.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
class Word_Metadata extends FileTypeActiveRecord {
	/**
	* Writes extracted MS Word metadata to the database
	* @param array $metadata
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
			'Company' => 'company',
			'Content-Type' => 'content_type',
			'Creation-Date' => 'creationdate',
			'Keywords' => 'keywords',
			'Last-Author' => 'last_author',
			'Last-Save-Date' => 'last_modified',
			'Last-Modified' => 'last_modified', 
			'Page-Count' => 'pages',
			'Revision-Number' => 'revision_number',
			'Template' => 'template',
			'creator' => 'creator',
			'date' => 'date_create',
			'publisher' => 'publisher',
			'resourceName' => 'resourcename', 
			'subject' => 'subject',
			'title' => 'title',
			'doc_title' => 'possible_doc_title',
			'doc_keywords' => 'possible_doc_keywords',
			'file_id' => 'file_id',
			'user_id' => 'user_id'
		);
		
		$actual_fields = $this->returnedFields($possible_fields, $metadata);
		$query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
		$full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
		$bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);
		
		$sql = 'INSERT INTO Word_Metadata(' . $this->queryBuilder($query_fields) . ') 
			VALUES(' . $this->queryBuilder($query_fields, true) . ')';
		
		$write_files = Yii::app()->db->createCommand($sql);
		$done = $write_files->execute($bind_params);
	}
}