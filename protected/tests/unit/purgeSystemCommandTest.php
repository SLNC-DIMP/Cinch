<?php
Yii::import('application.commands.purgeSystemCommand');
require_once 'vfsStream/vfsStream.php';

class purgeSystemCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='purgeSystem';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $purge = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($purge->run(array()));
	}
}