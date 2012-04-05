<?php
Yii::import('application.commands.ErrorCsvCommand');

class ErrorCsvCommandTest extends CDbTestCase {
	public function testShellCommand() {
		 $commandName='ErrorCsv';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $errorcsv = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($errorcsv->run(array()));
	}
}