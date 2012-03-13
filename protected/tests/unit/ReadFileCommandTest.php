<?php
Yii::import('application.commands.ReadFileCommand');

class ReadFileCommandTest extends CDbTestCase {
	/*public function testGetLists() {
		$class = new ReadFileCommand();
		$lists = $class->getLists();
		
		$this->assertEquals(1, $lists);
	} */
	
	 function testShellCommand() {
		 $commandName='ReadFile';
		 $CCRunner=new CConsoleCommandRunner();
					
		 $readfile = new ReadFileCommand($commandName,$CCRunner);
		 $this->assertTrue($readfile->run(array()));
     }

}