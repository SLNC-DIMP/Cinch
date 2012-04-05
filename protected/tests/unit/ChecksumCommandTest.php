<?php
Yii::import('application.commands.ChecksumCommand');
require_once 'vfsStream/vfsStream.php';

class ChecksumCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='Checksum';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $checksum = new ChecksumCommand($commandName,$CCRunner);
		// $this->assertTrue($checksum->run(array()));
	}
}