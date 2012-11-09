<?php
// Can't use Yii::import here as there are multiple classes in bagit.php
require_once Yii::app()->Basepath . '/lib/BagItPHP/lib/bagit.php';
Yii::import('application.models.Utils');

class BagItCommand extends CConsoleCommand {

    public $download_path;

    public function __construct($download_path) {
        $this->download_path = $download_path;
    }

    /**
     * Gets users file to add to BagIt container.
     * @access public
     * @return mixed
     */
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
            ->where(array('and', 'download_type = 2',
                    'temp_file_path != ""',
                    'temp_file_path IS NOT NULL',
                    'zipped != 1',
                    'events_frozen != 1'))
            ->queryAll();

        return $get_file_list;
    }

    /**
     * Gets users' bags to check for errors
     * @access public
     * @return mixed
     */
    public function checkBags() {
        return Yii::app()->db->createCommand()
            ->select('*')
            ->from('zip_gz_downloads')
            ->where('download_type=2')
            ->queryAll();
    }

    /**
     * Creates a new BagIt object
     * @param $user_zip_path
     * @access public
     * @return BagIt
     */
    public function createBag($user_zip_path) {
		return new BagIt($user_zip_path);
	}
	
/*	A bit redundant.  Probably will delete
	public function addFiles(BagIt $bag, $file) {
        $bag_filename = $this->bagitName($file);
		$bag->addFile($file, $bag_filename);
	} */

    /**
     * Creates the filename to pass to BagIt
     * @param $file
     * @access private
     * @return string
     */
    private function bagItName($file) {
        return substr_replace(strrchr($file, '/'), '', 0, 1);
    }

    /**
     * Creates new BagIt containers.
     */
    public function ActionCreate() {
		$files = $this->getFiles();
		if(empty($files)) { echo "No files to bag\n"; exit; }


        $new_bag = $this->createBag('user_zip_path?');
		foreach($files as $file) {
            $bag_filename = $this->bagItName($file);

			$new_bag->addFile($file, $bag_filename);
			$new_bag->update();
		}

        $new_bag->package('');
	}

    /**
     * Checks existing BagIt containers for errors.
     * Writes an error to the database on Bag corruption.
     */
    public function ActionCheck() {
		$bags = $this->checkBags();
		if(empty($bags)) { echo "No  bags to check\n"; exit; }
		
		foreach($bags as $bag) {
			$bag_check = $this->createBag($bag['path']);
			if(!$bag_check->isValid()) {
				Utils::writeEvent($bag['id'], 'bag_error_id?'); // separate bag error report?  Currently this won't really work
			}
		}
	}
}