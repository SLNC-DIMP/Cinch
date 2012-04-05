<?php
Yii::import('application.commands.virusCheckCommand');
require_once 'vfsStream/vfsStream.php';

class virusCheckCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='virusCheck';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $download = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($download->run(array()));
	}
}