<?php
Yii::import('application.commands.ReadFileCommand');
require_once 'vfsStream/vfsStream.php';

class ReadFileCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	function testShellCommand() {
		 $commandName='ReadFile';
		 $CCRunner=new CConsoleCommandRunner();
		 $readfile = new ReadFileCommand($commandName,$CCRunner);
		 
		 // get lists to process
		 $lists = count($readfile->getLists());
		 
		 $this->assertEquals(2, $lists);
		 $this->assertNotEquals(4, $lists);
		 
		 //write initial file count
		 $file_count = $readfile->writeFileCount(4, 1);
		 $this->assertEquals(true, $file_count);
		 
		 //update list of uploaded urls to procesed
		 $update = $readfile->updateFileList(1);
		 $this->assertEquals(true, $update);
     }

}