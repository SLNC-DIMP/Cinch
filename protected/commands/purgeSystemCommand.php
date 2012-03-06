<?php
/**
* @todo this should probably be done last
* @todo delete user files as well as generated csv and zip files
* @todo delete enteries from files for download, user upload lists, zip and csv paths from table
* @todo update file_info table.  DON'T DELETE record.
*/
class purgeSystemCommand extends CConsoleCommand {
	public $file_info = 'file_info';
	public $error_list;
	public $mail_user;
	
	public function __construct() {
		$this->error_list = Yii::getPathOfAlias('application.messages') . '\/' . 'error_list_' . date('Y-m-d') . '.txt';
		$this->mail_user = new mailUser;
	}
	
	/**
	* Gets files that are more than 30 days old for deletion
	* Trying to keep it DB agnostic, would use DATE_SUB with MySQL
	* @access public
	* @return object Yii DAO object
	*/
	public function filesToDelete() {
		$files = Yii::app()->db->createCommand()
			->select('id, temp_file_path')
			->from($this->file_info)
			->where(':download_time <= download_time', array(':download_time' => $this->timeOffset()))
			->queryAll();
		
		return $files;
	}
	
	/**
	* Get generated csv and zip files to delete
	* Get user upload lists
	* @param $table
	* @param $email_reminder
	* @access public
	* @return object Yii DAO object
	*/
	public function generatedFiles($table) {
		$field = ($table == 'upload') ? 'process_time' : 'creationdate';
			
		$generated_files = Yii::app()->db->createCommand()
			->select('id, path, user_id')
			->from($table)
			->where(':' . $field . '<=' . $field, array(':'.$field => $this->timeOffset()))
			->queryAll();
		
		return $files;
	}
	
	public function getUserReminders() {
		$sql = "SELECT user_id FROM zip_gz_downloads
			WHERE creationdate <= '" . $this->timeOffset(20) .
		  "' GROUP BY user_id";
		
		$user_list = Yii::app()->db->createCommand($sql)
			->queryAll();
		
		return $user_list;
	}
	
	/**
	* Delete processed download lists, url lists, and ftp lists from the database.
	* These records aren't linked to a file on the server
	* 1 = processed
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
	* @access protected
	* @return string
	*/
	protected function timeOffset($offset = 30) {
		return date('Y-m-d H:i:s', time() - ($offset * 24 * 60 * 60));
	}
	
	/**
	* Update file_info table if an expired file is successfully deleted
	* @access private
	* @return object Yii DAO object
	*/
	private function updateFileInfo($file_id) {
		$sql = "UPDATE file_info SET temp_file_path = '', expired_deleted = 1 WHERE id = ?";
		$clear = Yii::app()->db->createCommand($sql)
			->execute(array($file_id));
	}
	
	/**
	* Remove file from the file system
	* @param $file_path
	* @access public
	* @return boolean
	*/
	public function removeFile($file_path, $file_id) {
		$delete_file = @unlink($file_path);
			
		if($delete_file == false) {
			$this->logError($this->getDateTime() . " - $file_id, with path: $file_path could not be deleted.");
		} else {
			$this->updateFileInfo($file_id);
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
	public function removeDir($dir_path) {
		$dir_list = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir_path, FilesystemIterator::SKIP_DOTS));
		
		foreach($dir_list as $dir) {
			if($dir->isDir() && empty($dir)) {
				$delete_dir = @rmdir($dir);
				
				if($delete_dir == false) {
					$this->logError($this->getDateTime() . " - Directory: $dir_path could not be deleted.");
				}
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
			$this->mail_user->UserMail($user['user_id'], $subject, $message);
		}
	}
	
	public function actionDelete() {
		$files = $this->filesToDelete();
		if(empty($files)) { exit; }
		
		$this->clearLists('upload');
		$this->clearLists('files_for_download');
		
		foreach($files as $file) {
			$this->removeFile($file['temp_file_path'], $file['id']);
		}
		
		$this->removeDir(Yii::getPathOfAlias('application.uploads'));
		$this->mailError();
	}
}