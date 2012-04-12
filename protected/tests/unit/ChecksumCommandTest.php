<?php
Yii::import('application.commands.ChecksumCommand');
require_once 'vfsStream/vfsStream.php';

class ChecksumCommandTest extends CDbTestCase {
	private $root;
	public $fixtures = array('checksum' => 'FileInfo');
	
	public function setUp() {
        $this->root = vfsStream::setup('user', NULL, array('file.pdf'=>'')); // file is an empty pdf
    }
	
	public function testShellCommand() {
		 $commandName='Checksum';
		 $CCRunner=new CConsoleCommandRunner();			
		 $checksum = new ChecksumCommand($commandName,$CCRunner);
		 
		 $sha1 = $checksum->createChecksum(vfsStream::url('user/file.pdf')); 
		 $md5 = $checksum->createChecksum(vfsStream::url('user/file.pdf'), 'md5');
		 
		 $this->assertEquals('da39a3ee5e6b4b0d3255bfef95601890afd80709', $sha1);
		 $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $md5);
		 $this->assertFalse(false, $sha1);
		 $this->assertFalse(false, $md5);
		 
		 $bogus_file_sha1 = $checksum->createChecksum('user/test.txt');
		 $bogus_file_md5 = $checksum->createChecksum('user/test.txt', 'md5');
		 
		 $this->assertEquals(false, $bogus_file_sha1);
		 $this->assertEquals(false, $bogus_file_md5);
	}
}