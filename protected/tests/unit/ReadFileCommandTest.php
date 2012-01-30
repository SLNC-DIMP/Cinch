<?php
Yii::import('application.commands.ReadFileCommand');

class ReadFileCommandTest extends CDbTestCase {
	public function testGetLists() {
		$class = new ReadFileCommand();
		$lists = $class->getLists();
		
		$this->assertEquals(1, $lists);
	}
}