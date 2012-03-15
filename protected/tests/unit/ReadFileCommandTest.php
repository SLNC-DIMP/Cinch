<?php
Yii::import('application.commands.ReadFileCommand');

class ReadFileCommandTest extends CDbTestCase {
	function testShellCommand() {
		 $commandName='ReadFile';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $readfile = new ReadFileCommand($commandName,$CCRunner);
		 $lists = $readfile->getLists();
		
		 $this->assertEquals(2, count($readfile->getLists()));
		 $this->assertNotEquals(4, count($readfile->getLists()));
     }

}