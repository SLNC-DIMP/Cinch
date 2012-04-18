<?php
Yii::import('application.commands.virusCheckCommand');
require_once 'vfsStream/vfsStream.php';

class virusCheckCommandTest extends CDbTestCase {
	public $fixtures = array('file_info' => 'File_Info', 
							 'problem' => 'Problem_Files', 
							 'event' => 'File_Event_History');
	
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='virusCheck';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $virus = new virusCheckCommand($commandName,$CCRunner);
		 
		 $files = $virus->getFiles();
		 
		 $this->assertEquals(true, !empty($virus));
		 
		 foreach($files as $file) {
		 	$filenum[] = $file;
		 }
		 
		 $this->assertEquals(1, count($filenum));
		
	}
}