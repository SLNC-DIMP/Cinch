<?php
Yii::import('application.controllers.UploadController');

class UploadTest extends CTestCase {
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
}