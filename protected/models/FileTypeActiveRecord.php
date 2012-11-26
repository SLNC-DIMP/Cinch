<?php
/**
 * Base model class for file metadata insertion
 *
 * Builds queries and writes them to the database.
 * @category Metadata Queries
 * @package Metadata Queries
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */

/**
 * Builds queries and writes them to the database.
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */
abstract class FileTypeActiveRecord extends CActiveRecord {
	/**
	* Should be an abstract write method, but Yii doesn't seem to care for abstract methods
	* @abstract
    * @param array $metadata
    * @param $file_id
    * @param $user_id
	* @access public
	*/
	 public function writeMetadata(array $metadata, $file_id, $user_id) {}
	
	/**
	* Returned metadata fields vary by document, not just doc type.
	* This finds the intersection of returned metadata with file type table fields.
	* @param array $possible_query_fields
	* @param array $metadata
	* @access public
	* @return array
	*/
	public function returnedFields(array $possible_query_fields, array $metadata) {
		return array_intersect_key($possible_query_fields, $metadata);
	}
	
	/**
	* Flattens table fields into a string for query building.
	* Adds : if creating prepared statement bindings.
	* @param array $fields
	* @param $prepare
	* @access public
	* @return string
	*/
	public function queryBuilder(array $fields, $prepare = false) {
		if($prepare != false) {
			foreach($fields as $key => $field) {
				$fields[$key] = '?';
			}
		}
		return implode(',', $fields);
	}
	
	/**
	* Generates bind parameters on queries and cleans field values for insertion.
	* Each tika value starts with : so need to remove it.
	* @param array $fields
	* @param array $metadata
	* @access public
	* @return array
	*/
	public function bindValuesBuilder(array $fields, array $metadata) {
		$params = array();

		foreach($metadata as $key => $value) {
			if(array_key_exists($key, $fields)) {
				$param_value = preg_replace('/^:\s/', '', strip_tags(trim($value)));
				$params[] = $param_value;
			} else {
				continue;
			}
		}
		
		return $params;
	}
	
	/**
	* Merges file and/or user id info onto the end of the metadata array
	* @param array $metadata_fields
	* @param $id_values
	* @access public
	* @return array
	*/
	public function addIdInfo(array $metadata_fields, $id_values) {
		if(is_array($id_values)) {
			return $field_list = array_merge($metadata_fields, $id_values);
		} else {
			return $metadata_fields[$id_values] = $id_values;
		}
	}
}