<?php
Yii::import('application.commands.zipCreationCommand');
require_once 'vfsStream/vfsStream.php';

class zipCreationCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='zipCreation';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $zip = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($zip->run(array()));
	}
}