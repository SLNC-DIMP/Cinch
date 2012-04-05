<?php
Yii::import('application.commands.EventCsvCommand');

class EventCsvCommandTest extends CDbTestCase {
	public function testShellCommand() {
		 $commandName='EventCsv';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $event = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($event->run(array()));
	}
}