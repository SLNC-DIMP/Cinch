<?php
// see http://www.imagemagick.org/script/jp2.php
class Jpeg2000Command extends CConsoleCommand {
	public function getImages() {
		$get_images = Yii::app()->db->createCommand()
			->select('id, temp_file_path, user_id, upload_file_id')
			->from('file_info')
			->where(array('and', 'jp2_convert = 1', 'virus_check = 1',
					array('or', "temp_file_path != ''", 'temp_file_path IS NOT NULL')))
			->limit(10000)
			->queryAll();
			
		return $get_images;
	}
	
	/**
	* Converts image to JP2 format.  
	* Result is saved in the same dir as the uncoverted image.
	* Both images will be included in download.
	* @param $image_path
	* @return integer
	*/
	public function createJpeg2000($image_path) {
		$command = "convert $image_path -define numrlvls=6 -define jp2:tilewidth=1024 -define jp2:tileheight=1024 -define jp2:rate=1.0 -define jp2:lazy -define jp2:prg=rlcp -define jp2:ilyrrates='0.015625,0.01858,0.0221,0.025,0.03125,0.03716,0.04419,0.05,0.0625, 0.075,0.088,0.1,0.125,0.15,0.18,0.21,0.25,0.3,0.35,0.4,0.5,0.6,0.7,0.84' -define jp2:mode=int $image_path.jp2";
		passthru(escapeshellcmd($command), $retval);
		
		return $retval;
	}
	
	public function run() {
	
	}
}