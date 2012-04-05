<?php
Yii::import('application.commands.MetadataCsvCommand');

class MetadataCsvCommandTest extends CDbTestCase {
	public function testShellCommand() {
		 $commandName='MetadataCsv';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $metadatacsv = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($metadatacsv->run(array()));
	}
}