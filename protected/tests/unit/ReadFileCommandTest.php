<?php
Yii::import('application.commands.ReadFileCommand');

class ReadFileCommandTest extends CDbTestCase {
	public $fixtures = array('uploads' => 'upload', 'files' => ':files_for_download');
	
	function testShellCommand() {
		 $commandName='ReadFile';
		 $CCRunner=new CConsoleCommandRunner();
		 $readfile = new ReadFileCommand($commandName,$CCRunner);
		
		 // get lists to process
		 $lists = count($readfile->getLists());
		 
		 $this->assertEquals(3, $lists);
		 $this->assertNotEquals(4, $lists);
		 
		 //write initial file count
		 $file_count = $readfile->writeFileCount(525, 1);
		 $this->assertEquals($fixtures->uploads['upload1']['urls_in_list'], $file_count);
		 
		 //update list of uploaded urls to procesed
		 $update = $readfile->updateFileList(3);
		 $this->assertEquals($fixtures->uploads['upload3']['processed'], $update);
		 
		 $values = array('http://statelibrarync.org/cinch_test/clamav.docx', 1, 1);
		 $files = $readfile->addUrls($values);
		 $this->assertEquals($fixtures->files['files_for_download6']['url'], $files);
     }
}