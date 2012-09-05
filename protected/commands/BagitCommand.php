<?php
Yii::import('application.models.Utils');

// Can't use Yii::import here as there are multiple classes in bagit.php
require_once Yii::app()->Basepath . '/lib/BagItPHP/lib/bagit.php';

class BagitCommand extends CConsoleCommand {
	public function createBag($user_zip_path) {
		return new BagIt($user_zip_path);
	}
	
	public function addFiles(BagIt $bag, $file) {
		$bag->addFile($file);
	}
	
	public function ActionCreate($user_zip_path) {
		$bags = 'Bag query here';
		if(empty($bags)) { echo "No bags to add\n"; exit; }
		
		foreach($bags as $bag) {
			$new_bag = $this->createBag($user_zip_path);
			$new_bag->addFile();
			$new_bag->update();
		}
	}
	
	public function ActionCheck() {
		$bags = 'Bag query here';
		if(empty($bags)) { echo "No bags to check\n"; exit; }
		
		foreach($bags as $bag) {
			$bag_check = $this->createBag($bag['path-to-bag']);
			if(!$bag_check->isValid()) {
				Utils::writeEvent($file_id, $event_id); // seperate bag error report?  Currently this won't really work
			}
		}
	}
}