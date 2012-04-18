<?php
class ChecksumTest extends CDbTestCase {
	public $fixtures = array('file_info' => 'File_Info', 'problem' => 'Problem_Files');
	public $checksum;
	
	public function setup() {
		$this->checksum = new Checksum;
	}
	
	public function testgetFileList() {
		$file_list = $this->checksum->getFileList();
		$this->assertTrue(true, !empty($file_list));
	}
}