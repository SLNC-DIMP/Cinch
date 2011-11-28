<?php
class UploadController extends Controller {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
/**
	* @return array action filters
	*/
	public function filters()
	{
		return array(
			'rights',
		);
	}
	
	public function actionIndex() {
		$user_upload_dir = $this->getUsrUploadDir();
		$uploaded = false;
		$model = new Upload;
		
		if(isset($_POST['Upload'])) {
			$file = CUploadedFile::getInstance($model,'upload_path');
			
			if($model->validate()){
				if(!is_dir($user_upload_dir)) {
					mkdir($user_upload_dir);
				}
				
				$mod_name = $this->encryptName($file->getName());
				$user_id = Yii::app()->user->id;
				$upload_path = $user_upload_dir . '/' . $mod_name;
				$model->attributes = array('user_id' => $user_id, 'upload_path' => $upload_path);
				$uploaded = $file->saveAs($upload_path);
				
				if($uploaded) { $model->save(); }
			}
		}
		
		$this->render('index', array(
			'model' => $model,
			'uploaded' => $uploaded,
			'dir' => $user_upload_dir,
		));
	}
	
	/**
	* Converts a file's name into an MD5 hash so users can't easily mess with their own files.
	* @return string encrypted file name
	*/
	public function encryptName($file) {
		$file_extension = strrchr($file, '.');
		$file_name = substr($file, 0, -4);
		$encrypted_name = md5($file_name);
		
		return $encrypted_name . $file_extension;
	}
	
	/**
	* Finds a user's upload directory.
	* @return string
	*/
	public function getUsrUploadDir() {
		$dir = Yii::getPathOfAlias('application.uploads');
		$user_name = Yii::app()->user->name;
		$user_upload_dir = $dir . '/' . $user_name;
		
		return $user_upload_dir;
	}
}