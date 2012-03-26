<?php
/**
* @todo this should probably be done last
* @todo update file_info table.  DON'T DELETE record.
*/
class purgeSystemCommand extends CConsoleCommand {
	public $file_info = 'file_info';
	public $error_list;
	public $mail_user;
	
	public function __construct() {
		$this->error_list = Yii::getPathOfAlias('application.messages') . '/' . 'error_list_' . date('Y-m-d') . '.txt';
		$this->mail_user = new MailUser;
	}
	
	/**
	* Gets files that are more than 30 days old for deletion
	* Trying to keep it DB agnostic, would use DATE_SUB with MySQL
	* @access public
	* @return object Yii DAO object
	*/
	public function filesToDelete() { 
		$sql = "SELECT id, temp_file_path, file_type_id FROM file_info
			WHERE download_time <= :download_time
			AND   virus_check = :virus_check
			AND   checksum_run = :checksum_run
			AND   metadata = :metadata
			AND   temp_file_path != :path";
		
		$files = Yii::app()->db->createCommand($sql)
			->bindParam(':download_time', $this->timeOffset(1))
			->bindValue(':virus_check', 1)
			->bindValue(':checksum_run', 1)
			->bindValue(':metadata', 1) 
			->bindValue(':path', '')
			->limit(7500)
			->queryAll();
		
		return $files;
	}
	
	/**
	*
	* @param $file_type_id
	* @access private
	* @return string
	*/
	private function getMetataTable($file_type_id) {
		switch($file_type_id) {
			case 1:
				return 'PDF_Metadata';
			case 2:
			case 3:
				return 'Word_Metadata';
			case 4:
				return 'Tiff_Metadata';
			case 5:
				return 'Jpg_Metadata';
			case 6:
				return 'Gif_Metadata';
			case 7:
				return 'Text_Metadata';
			case 8:
			case 9:
				return 'Excel_Metadata';
			case 10:
				return 'PNG_Metadata';
			case 11:
			case 12:
				return 'PPT_Metadata';
		}
		
	}
	
	/**
	* Get generated csv and zip files to delete
	* creationdate is used with csv and zip files
	* Get user upload lists
	* Used process_time
	* @param $table
	* @access public
	* @return object Yii DAO object
	*/
	public function generatedFiles($table) {
		$field = ($table == 'upload') ? 'process_time' : 'creationdate';
		
		$sql = "SELECT id, path, user_id FROM $table WHERE $field <= :timeoffset";
		$generated_files = Yii::app()->db->createCommand($sql)
			->bindParam(':timeoffset', $this->timeOffset(30))
			->queryAll();
		
		return $generated_files;
	}
	
	/**
	* Get list of users with zip files that are at least 20 days old to send them notices of impending file deletions.
	* @access public
	* @return object Yii DAO object
	*/
	public function getUserReminders() {
		$sql = "SELECT id, user_id FROM zip_gz_downloads
			WHERE deletion_reminder = :deletion_reminder 
			AND creationdate <= :creationdate 
			GROUP BY user_id";
		
		$user_list = Yii::app()->db->createCommand($sql)
			->bindValue(':deletion_reminder', 0)
			->bindParam(':creationdate', $this->timeOffset(20))
			->queryAll();
		
		return $user_list;
	}
	
	/**
	* Update zip file list to show that file has been accounted for in email deletion reminders 
	* and user doens't need to be reminded again about this file.
	* @param $file_id
	* @access protected
	* @return object Yii DAO object
	*/
	protected function reminderSent($file_id) {
		$sql = "UPDATE zip_gz_downloads SET deletion_reminder = 1 WHERE id = ?";
		$reminder_sent = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Delete processed download lists, url lists, and ftp lists from the database.
	* These records aren't linked to a file on the server
	* 1 = processed
	* @param $table
	* @access protected
	* @return object Yii DAO object
	*/
	protected function clearLists($table) {
		$sql = "DELETE FROM $table WHERE processed = ?";
	
		$clear = Yii::app()->db->createCommand($sql)
			->execute(array(1));
	}
	
	/**
	* Get current date/time in ISO 8601 date format
	* @access protected
	* @return string
	*/
	protected function getDateTime() {
		return date('c');
	}
	
	/**
	* Doing it this way so it'll work in SQLite and MySQL.  SQLite doesn't have DATE_SUB() function
	* Returns something like 2012-03-04 14:23:46
	* @param $offset
	* @access protected
	* @return string
	*/
	protected function timeOffset($offset = 30) {
		$time = time() - ($offset * 24 * 60 * 60);
		
		return date('Y-m-d H:i:s', $time);
	}
	
	/**
	* Update file_info table if an expired file is successfully deleted
	* @param $file_id
	* @access private
	* @return object Yii DAO object
	*/
	private function updateFileInfo($file_id) {
		$sql = "UPDATE file_info SET temp_file_path = '', expired_deleted = 1 WHERE id = ?";
		$clear = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Update a generated file table: zip, csv, metadata
	* @param $table
	* @param $file_id
	* @access private
	* @return object Yii DAO object
	*/
	private function updateGenerated($table, $file_id) {
		$sql = "DELETE FROM $table WHERE id = ?";
		$delete = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Remove file from the file system
	* @param $file_path
	* @param $file_id
	* @param $table
	* @access public
	* @return boolean
	*/
	public function removeFile($file_path, $file_id, $table = 'file_info') {
		$delete_file = @unlink($file_path);
			
		if($delete_file == false) {
			$this->logError($this->getDateTime() . " - $file_id, with path: $file_path could not be deleted.");
		} elseif($table == 'file_info') {
			$this->updateFileInfo($file_id);
		} else {
			$this->updateGenerated($table, $file_id);
		}
	}
	
	/**
	* Remove directory from the file system if empty
	* RecursiveDirectoryIterator should account for . and .. files.
	* This should look at files beneath each user's root folder.
	* @param $dir_path
	* @access public
	* @return boolean
	*/
/*	public function removeDir($dir_path) {
		$dir_list = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$dir_path,
					RecursiveIteratorIterator::SELF_FIRST |
					FilesystemIterator::SKIP_DOTS |
					FilesystemIterator::UNIX_PATHS
			)
		);
		
		
		foreach($dir_list as $dir) {
			if( $dir->isDir()) {
				echo $dir;
			}
			
			if($dir->isDir() && empty($dir)) {
				$delete_dir = @rmdir($dir);
				
				if($delete_dir == false) {
					$this->logError($this->getDateTime() . " - Directory: $dir could not be deleted.");
				}
			}

		}
		
		
	} */
	
	/**
	* http://stackoverflow.com/questions/4747905/how-can-you-find-all-immediate-sub-directories-of-the-current-dir-on-linux
	*/
	public function removeDir($dir_path) {
		exec(escapeshellcmd('find ' . $dir_path . ' -type d'), $dirs);
		unset($dirs[0]); // this is the base dir for the downloads/uploads so leave it there
		
		foreach($dirs as $dir) {
			$count = count(scandir($dir));
			
			if($count == 2) { // check for . and .. files
				$delete_dir = @rmdir($dir);
				
				if($delete_dir == false) {
					$this->logError($this->getDateTime() . " - Directory: $dir could not be deleted.");
				}
			} 

		}
	}
	
	/**
	* Loop through the list of files to delete, remove them
	* @param $files
	* @access public
	*/
	public function fileProcess($files) {
		if(is_array($files) && !empty($files)) {
			foreach($files as $file) {
				$this->removeFile($file['path'], $file['id']);
			}
		}
	}
	
	/**
	* Writes file and directory deletion failures to file.
	* @param $error_text
	* @access protected
	*/
	protected function logError($error_text) {
		$fh = fopen($this->error_list, 'ab');
		fwrite($fh, $error_text . ",\r\n");
		fclose($fh);
	}
	
	/**
	* Mails file and directory deletion failures to sys. admin
	* @access protected
	*/
	protected function mailError() {
		if(file_exists($this->error_list)) {
			$to = 'webmaster@example.com';
			$subject = 'Cinch file and directory deletion errors';
			$from = 'From: webmaster@example.com' . "\r\n";
			
			$message = "The following deletion errors occured:\r\n";
			$message .= file_get_contents($this->error_list);
			
			mail($to, $subject, $message, $headers);
		} else {
			return false;
		}
	}
	
	/**
	* Mails user a reminder that they have files what will be deleted in 10 days.
	* @access public
	*/
	public function actionCheck() {
		$users = $this->getUserReminders();
		if(empty($users)) { exit; }
		
		$subject = 'You have files on Cinch! marked for deletion';
			
		$message = "You have files marked for deletion from Cinch!\r\n";
		$message .= "They will be deleted 10 days from now.\r\n";
		$message .= "If you haven't done so please retrieve your downloads soon from http://cinch.nclive.org.\r\n";
		$message .= "\r\n";
		$message .= "Thanks, from your Cinch administrators";
		
		foreach($users as $user) {
			$mail_sent = $this->mail_user->UserMail($user['user_id'], $subject, $message);
			if($mail_sent) {
				$this->reminderSent($user['id']);
			}
		}
	}
	
	public function actionDelete() {
		$zip_files = $this->generatedFiles('zip_gz_downloads');
		$this->fileProcess($zip_files);
		
		$csv_files = $this->generatedFiles('csv_meta_paths');
		$this->fileProcess($csv_files);
		
		$this->clearLists('files_for_download'); 
		
		$upload_lists = $this->generatedFiles('upload'); 
		$this->fileProcess($upload_lists);
		
		$downloaded_files = $this->filesToDelete();
		if(is_array($downloaded_files) && !empty($downloaded_files)) {
			foreach($files as $file) {
				$this->removeFile($downloaded_file['temp_file_path'], $downloaded_file['id']);
				$table = $this->getMetataTable($downloaded_file['file_type_id']);
				$this->updateGenerated($table, $downloaded_file['id']);
			}
		} 
		
		$user_dirs = array('uploads', 'curl_downloads'); // remove empty directories
		foreach($user_dirs as $user_dir) {
			$this->removeDir(Yii::getPathOfAlias('application.' . $user_dir));
		}
		
		$this->mailError();
	}
}