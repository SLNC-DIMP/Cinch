<?php
Yii::import('application.commands.MetadataCommand');
require_once 'vfsStream/vfsStream.php';

class MetadataCommandTest extends CDbTestCase {
	public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }
	
	public function testShellCommand() {
		 $commandName='Metadata';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $metadata = new MetadataCommand($commandName,$CCRunner);
		// $this->assertTrue($metadata->run(array()));
	}
}