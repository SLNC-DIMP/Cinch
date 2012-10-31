<?php
Yii::import('application.models.Utils');
/**
* MetadataCommand class file
*
* This is the command for extracting metadata from a user's files.
* @category Metadata
* @package Metadata
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* This is the command for extracting metadata from a user's files.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
class MetadataCommand extends CConsoleCommand {
    /**
     * Path to Tika jar file to perform metadata extraction.
     * @var $tika_path
     */
    private $tika_path;

	const PDF = 'application/pdf';
	const WORD = 'application/msword';
	const WORD2007 = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	const PPT = 'application/vnd.ms-powerpoint';
	const PPT2007 = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
	const EXCEL = 'application/vnd.ms-excel';
	const EXCEL2007 = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	const JPEG = 'image/jpeg';
	const PNG = 'image/png';
	const GIF = 'image/gif';
	const TEXT = 'text/plain';
	
	public function __construct() {
		$this->tika_path = Yii::getPathOfAlias('application') . '/tika-app-1.2.jar';
	}
	
	/**
	* Retrieves a list of uploaded files that need to have their metadata extracted
	* Ignores 404 and virus check error files
	* @access public
	* @return object Yii DAO
	*/
	public function getFileList() {
		$get_file_list =  Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id, upload_file_id')
			->from('file_info')
			->where(array('and', 'metadata = 0', 'virus_check = 1', 'checksum_run = 1',
					array('or', "temp_file_path != ''", 'temp_file_path IS NOT NULL')))
			->limit(10000)
			->queryAll();
			
		return $get_file_list;
	}
	
	/**
	* Save metadata to correct metadate table
	* @param $file_type
	* @param $metadata
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object Yii DAO
	*/
	public function writeMetadata($file_type, $metadata, $file_id, $user_id) {
		switch($file_type) {
			case self::PDF:
				$write = new PDF_Metadata;
				break;
			case self::WORD:
			case self::WORD2007:
				$write = new Word_Metadata;
				break;
			case self::PPT:
			case self::PPT2007:
				$write = new PPT_Metadata;
				break;
			case self::EXCEL:
			case self::EXCEL2007:
				$write = new Excel_Metadata;
				break;
			case self::GIF:
				$write = new Gif_Metadata;
				break;
			case self::JPEG:
				$write = new Jpg_Metadata;
				break;
			case self::PNG:
				$write = new PNG_Metadata;
				break;
			case self::TEXT:
				$write = new Text_Metadata;
				break;
		}
		
		$write->writeMetadata($metadata, $file_id, $user_id);
	}
	
	/**
	* Write file processed for metadata
	* @param $file_id
	* @param $field
	* @param $file_type_id
	* @access public
	* @return object Yii DAO
	*/
	public function updateFileInfo($file_id, $field = 'metadata', $file_type_id = NULL) {
		if($field == 'metadata') {
			$sql = "UPDATE file_info SET metadata = 1, file_type_id = ? WHERE id = ?";
			$values = array($file_type_id, $file_id);
		} else {
			$sql = "UPDATE file_info SET fulltext_available = 1 WHERE id = ?";
			$values = array($file_id);
		}
		
		$metadata_processed = Yii::app()->db->createCommand($sql);
		$metadata_processed->execute($values);	
	}
	
	/**
	* Writes error to database if metadata could not be extracted from a file
	* Metadata extraction error code is 4
	* @param $file_id
	* @param $error_id
	* @access protected
	* @return boolean
	*/
	protected function tikaError($file_id, $error_id) {
		Utils::writeError($file_id, $error_id);	
		Utils::setProblemFile($file_id);
		
		return false;
	}
	
	/**
	* Get list of available file types
	* @access protected
	* @return object Yii DAO
	*/
	protected function getMimeTypes() {
		$sql = "SELECT * FROM  file_type";
		$file_type_ids = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $file_type_ids;
	}
	
	/**
	* Gets the id number of the Tika file type string
	* @param $file_type
	* @access protected
	* @return integer
	*/
	protected function getFiletypeId($file_type) {
		$sql = "SELECT id FROM file_type WHERE file_type = :file_type";
		$file_id = Yii::app()->db->createCommand($sql)
			->bindParam(":file_type", $file_type, PDO::PARAM_STR)
			->queryColumn();
	
		return $file_id[0];
	}
	/********************* End of Model elements ***************************************/
	
	/**
	* Extracts file level metadata using Apache Tika
	* Or conversely extracts document text
	* Run it in server mode so it doesn't need to reload Tika each time
	* @param $file
	* @param $extract - Options metadata is default text, html, xml other possible  values
	* @access protected
	* @return array
	*/
	protected function scrapeMetadata($file, $extract = 'metadata') {
		$output = array();
		$command = 'java -jar ' . $this->tika_path . ' --' . $extract . ' ' . "$file";
		
		exec(escapeshellcmd($command), $output);
		
		return $output;	
	}
	
	/**
	* Extracts file type from metadata array via Apache Tika making a call to a Java jar file
	* Array value will be of Content-Type: whatever/whatever
	* Unsupported file types should only get this far if their extension misreports the true file type.
	* Hence the double error.
	* Code 4 Unable to extract metadata
	* Code 12 Unsupported file type
	* Code 18 file mime-type doesn't match file extension
	* @param $metadata (array)
	* @access public
	* @return mixed sring on success, array on error
	*/
	public function getTikaFileType($metadata) {
		$constants = new ReflectionClass('MetadataCommand');
		$file_types = $constants->getConstants();
		
		if(!empty($metadata)) { // || !is_null($metadata)
			$clean_file_type = $metadata['Content-Type'];
			
			if(!in_array($clean_file_type, $file_types)) {
				$clean_file_type = array(12, 18);
			}
		} else {
			$clean_file_type = array(4);
		}
		
		return $clean_file_type;
	} 
	
	/**
	* Get id of actual file extension to compare Tika reported extension id
	* Since JPEG extensions can vary reset JPG to right extension id (5)
	* Since text files vary set .csv files to correct text extension id (7)
	* @param $file_path
	* @access public
	* @return integer
	*/
	public function getExpectedMimetype($file_path) {
		$mime_types = $this->getMimeTypes();
		foreach($mime_types as $type) {
			$types[$type['id']] = $type['file_type_name'];
		}
		$types[] = 'JPG';
		$types[] = 'CSV';
		$type_count = count($types);
	
		$file_extension = @pathinfo($file_path, PATHINFO_EXTENSION);
		$file_type_id = array_search(strtoupper($file_extension), $types);
		
		if($file_type_id == $type_count) { 
			$file_type_id = 7; 
		} elseif($file_type_id == $type_count - 1) {
			$file_type_id = 5;
		} 
		
		return $file_type_id;	
	}
	
	/**
	* Takes metadata file and creates associative array of it.
	* Metadata values come in like so, Content-Type: whatever/whatever, File-Type:text/plain so need to split this out on the :
	* and make the first part the key and the second part the array value.
	* Time/date values get truncated if using strrchr, while others, notably page count format incorrectly on stristr.
	* Hence the branching. 
	* @param $file
	* @access public
	* @return array
	*/
	public function getMetadata($file) {
		$metadata = $this->scrapeMetadata($file);
		if(empty($metadata)) { return $metadata; }
		
		foreach($metadata as $metadata_value) {
			$field_name = trim(stristr($metadata_value, ':', true)); // returns portion of string before the colon
			if(preg_match('/(date|modified|created)/i', $metadata_value)) {
				$formatted_value = stristr($metadata_value, ':'); 
			} else {
				$formatted_value = strrchr($metadata_value, ':');
			}	
			$formatted_metadata[$field_name] = trim(substr_replace($formatted_value, '', 0, 1));
		}
		
		$formatted_metadata = $this->checkPageCount($formatted_metadata);

		return $formatted_metadata;
	}
	
	/**
	* resets page count to NULL if value is 0
	* @param array $formatted_metadata
	* @access protected
	* @return array
	*/
	protected function checkPageCount(array $formatted_metadata) {
		if(array_key_exists('xmpTPg', $formatted_metadata) && $formatted_metadata['xmpTPg'] == 0) { 
			$formatted_metadata['xmpTPg'] = NULL; 
		}
		
		return $formatted_metadata;
	}
	
	/**
	* Clean up extracted text and just return words longer than 3 characters and non-stop words.
	* Expects a string 
	* Stop words are a modified list of those found at: List from http://www.textfixer.com/resources/common-english-words.txt
	* @param array $tika_text
	* @access protected
	* @return array
	*/
	protected function getCleanText(array $tika_text) {
		$stop_words = array("able","about","across","after","almost","also","among","because","been","cannot","could","dear","does","either","else","ever","every","from","have","hers","however","into","just","least","like","likely","might","most","must","neither","often","only","other","rather","said","says","should","since","some","than","that","their","them","then","there","these","they","this","twas","wants","were","what","when","where","which","while","whom","will","with","would","your");
		$text = implode(' ', $tika_text);
		$words = preg_split('/\s{1,}/i', $text);
		$clean_words = array();
		
		foreach($words as $word) {
			preg_replace('/(\.|!|\?|\{|\}|\,|\"|\'|;|:\(|\).?)/i', '', $word);
			
			if(strlen($word) > 3 && !in_array($word, $stop_words) && preg_match('/^\w/i', $word)) {
				$clean_words[] = $word;
			}
		}
		
		return $clean_words;
	}
	
	/**
	* Extract top 5 keywords ie 5 words with the highest frequency count 
	* @param array $tika_text
	* @access public
	* @return string
	*/
	public function keywords(array $tika_text) {
		$clean_words = $this->getCleanText($tika_text);
		$count = array_count_values($clean_words);
		arsort($count);
		$key_words = array_keys(array_slice($count, 0, 5, true));
		
		return implode(', ', $key_words);
	}

	/**
	* Extract possible document title
	* If opening segments are empty they are skipped until text segment is it.
	* Then breaks off at next empty segment.
	* @param array $tika_text
	* @access public
	* @return string
	*/
	public function getTitle(array $tika_text) {
		$title = '';
		$segments = 0;
		foreach($tika_text as $phrase) {
			if(!empty($phrase)) {
				$title .= $phrase;
				$segments++;
			} elseif($segments == 0) {
				continue;
			} else {
				break;
			}
		}
	
		return $title;
	}
	
	/**
	* Extracts and writes file level metadata
	* Determine full text availability if not an audio, video or image file
	* Update metadata to 1 in file info for every record the metadata command iterates over.
	* If nothing needs to be done command exits
	* 8 event code is metadata extraction
	* 4 error code for can't grab metadata
	* 12 error code for unsupported file type
	* 18 error code file mime-type doesn't match file extension
	*/
	public function run() {
		$files = $this->getFileList();
		if(empty($files)) { exit; }
		
		system('java -jar ' . $this->tika_path . ' --server');
		
		foreach($files as $file) {
			$metadata = $this->getMetadata($file['temp_file_path']);
			Utils::writeEvent($file['id'], 8);
		    
			$file_type = $this->getTikaFileType($metadata);
			if(!is_array($file_type)) { // returns error code on failure
				$file_type_id  = $this->getFiletypeId($file_type);
				$self_reported_file_type = $this->getExpectedMimetype($file['temp_file_path']);
				
				if($file_type_id != $self_reported_file_type) { 
					$this->tikaError($file['id'], 18);	
				}
			} else {
				$file_type_id = '';
			}
			
			if(is_array($file_type)) {
				foreach($file_type as $error_type) {
					$this->tikaError($file['id'], $error_type);
				}
				$success = " Failed";
			} else {
				
				if(!preg_match('/(image|audio|video)/', $file_type)) {
					$fulltext = $this->scrapeMetadata($file['temp_file_path'], 'text');
					Utils::writeEvent($file['id'], 12);
					
					if(!empty($fulltext)) {
						$metadata['doc_title'] = $this->getTitle($fulltext);
						$metadata['doc_keywords'] = $this->keywords($fulltext);		
						
						$this->updateFileInfo($file['id'], 'fulltext');	
					}
				} 
				
				$this->writeMetadata($file_type, $metadata, $file['id'], $file['user_id']);
				$success = " Added";
			}
			
			$this->updateFileInfo($file['id'], 'metadata', $file_type_id);
			
			echo $file['temp_file_path'] . $success . "\r\n"; 
		} 
	}
}