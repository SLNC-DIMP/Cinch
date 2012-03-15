<?php
Yii::import('application.commands.DownloadCommand');

class DownloadCommandTest extends CDbTestCase {
	 function testShellCommand() {
		 $commandName='Download';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $download = new DownloadCommand($commandName,$CCRunner);
		// $this->assertTrue($download->run(array()));
     }

}