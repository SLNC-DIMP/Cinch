<?php
Yii::import('application.commands.ReadFileCommand');

class ReadFileCommandTest extends CDbTestCase {
	function testShellCommand() {
		 $commandName='ReadFile';
		 $CCRunner=new CConsoleCommandRunner();
		 $readfile = new ReadFileCommand($commandName,$CCRunner);
		 
		 // get lists to process
		 $lists = count($readfile->getLists());
		 $file_count = $readfile->writeFileCount(4, 1);
		
		 $this->assertEquals(2, $lists);
		 $this->assertNotEquals(4, $lists);
		 
		 //
     }

}