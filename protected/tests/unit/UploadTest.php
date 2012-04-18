<?php
Yii::import('application.controllers.UploadController');

class UploadTest extends CDbTestCase {
	public $fixtures = array('uploads' => 'Upload');
	public $upload;
	
	public function __construct() {
		$this->upload = new UploadController('uploadTest');
	}
	
	public function testEncryptName() {
		$file = md5('file');
		$extension = '.txt';
		$file_test = $this->upload->encryptName('file.txt');
		
		$this->assertEquals($file . $extension, $file_test);
	}
	
	public function testGetUsrUploadDir() {
		$user_name = 'Guest'; // default Yii user
		
		// pulls out full path from this location
		$path = $this->upload->getUsrUploadDir(); 
		$unix_path = str_replace('\\', '/', $path);
		
		// switch to projects sub-path.  Clunky, but works
		$test_path = preg_replace('/^.*protected/i', '/protected', $unix_path);
		
		$this->assertEquals($test_path, '/protected/uploads/' . $user_name);
	}
	
	public function testRead() {
		$file = $this->uploads('upload1');
		$upload_id = $file->id;
		$user_id = $file->user_id;
		$file_path = $file->path;
		
		$this->assertEquals(1, $upload_id);
		$this->assertEquals(5, $user_id);
		$this->assertEquals('/Applications/MAMP/htdocs/cinch/protected/uploads/test/eb06cfdece63b232a8dcf07fbde6601f.txt', $file_path);
	}
	
	public function testDelete() {
		$file = $this->uploads('upload1');
		$upload_id = $file->id;
		$this->assertTrue($file->delete());
		
		$deleted_file = Upload::model()->findByPk($upload_id);
		$this->assertEquals(NULL, $deleted_file);
	}
}