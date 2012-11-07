<?php
// Can't use Yii::import here as there are multiple classes in bagit.php
require_once Yii::app()->Basepath . '/lib/BagItPHP/lib/bagit.php';
Yii::import('application.models.Utils');

class BagitCommand extends CConsoleCommand {
    public function getFiles() {
        $get_file_list = Yii::app()->db->createCommand()
            ->select('file_info.id,
                file_info.user_id,
                temp_file_path,
                jp2,
                pdfa,
                pdfa_convert,
                checksum_type')
            ->from('file_info')
            ->join('upload', 'file_info.upload_file_id=upload.id')
            ->where('and', array('download_type' => 2,
                    'temp_file_path != ""',
                    'temp_file_path IS NOT NULL',
                    'zipped != 1',
                    'events_frozen != 1'))
            ->queryAll();

        return $get_file_list;
    }

	public function createBag($user_zip_path) {
		return new BagIt($user_zip_path);
	}
	
	public function addFiles(BagIt $bag, $file) {
        $bag_filename = $this->bagitName($file);
		$bag->addFile($file, $bag_filename);
	}

    private function bagitName($file) {
        return substr_replace(strrchr($file, '/'), '', 0, 1);
    }
	
	public function ActionCreate($user_zip_path) {
		$files = $this->getFiles();
		if(empty($files)) { echo "No files to bag\n"; exit; }
		
		foreach($files as $file) {
            $bag_filename = bagitName($file);

			$new_bag = $this->createBag($user_zip_path);
			$new_bag->addFile($file, $bag_filename);
			$new_bag->update();
		}
	}
	
	public function ActionCheck() {
		$bags = 'Bag query here';
		if(empty($bags)) { echo "No bags to check\n"; exit; }
		
		foreach($bags as $bag) {
			$bag_check = $this->createBag($bag['path-to-bag']);
			if(!$bag_check->isValid()) {
				Utils::writeEvent($file_id, $event_id); // separate bag error report?  Currently this won't really work
			}
		}
	}
}