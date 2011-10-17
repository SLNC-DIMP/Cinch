<?php
Yii::import('application.controllers.UploadController');

class UploadTest extends CDbTestCase {
	public $fixtures = array('uploads' => 'Upload');
	
	public function testEncryptName() {
	
	}
	
	public function testGetUsrUploadDir() {
		$upload = new UploadController('uploadTest');
		$user_name = 'Guest'; // default Yii user
		
		// pulls out full path from this location
		$path = $upload->getUsrUploadDir(); 
		$unix_path = str_replace('\\', '/', $path);
		
		// switch to projects sub-path.  Clunky, but works
		$test_path = preg_replace('/^.*protected/i', '/protected', $unix_path);
		
		$this->assertEquals($test_path, '/protected/uploads/' . $user_name);
	}
	
	public function testRead() {
		$file = $this->uploads('upload1');
		$upload_id = $file->id;
		$user_id = $file->user_id;
		$file_path = $file->upload_path;
		
		$this->assertEquals(1, $upload_id);
		$this->assertEquals(1, $user_id);
		$this->assertEquals('/uploads/user_1/538f600f9f773c6baecec87e062678e3.txt', $file_path);
	}
	
	public function testDelete() {
		$file = $this->uploads('upload3');
		$upload_id = $file->id;
		$this->assertTrue($file->delete());
		
		$deleted_file = Upload::model()->findByPk($upload_id);
		$this->assertEquals(NULL, $deleted_file);
	}
}