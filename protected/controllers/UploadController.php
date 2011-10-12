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
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionIndex() {
		$dir = Yii::getPathOfAlias('application.uploads');
		$uploaded = false;
		$model=new Upload();
		if(isset($_POST['Upload'])) {
			$model->attributes = $_POST['Upload'];
			$file = CUploadedFile::getInstance($model,'file');
			if($model->validate()){
				$mod_name = $this->encryptName($file->getName());
				$uploaded = $file->saveAs($dir . '/' . $mod_name);
			}
		}
		
		$this->render('index', array(
			'model' => $model,
			'uploaded' => $uploaded,
			'dir' => $dir,
		));
	}
	
	/*
	* Converts a file's name into an MD5 hash so users' can't mess with their own files.
	* Requires PHP 5.3 to work correctly
	* @return string of file name
	*/
	public function encryptName($file) {
		$file_extension = strrchr($file, '.');
		$file_name = substr($file, 0, -4);
		$encrypted_name = md5($file_name);
		
		return $encrypted_name . $file_extension;
	}
	
	/**
	* Placeholder should be moved into a component.  Not sure how to do that. Not used at the moment
	*/
	public function cleanInput($values) {
		if(is_array($values)) {
			foreach($values as $key => $value) {
				$clean_values[$key] = CHtml::encode(trim($value));
			}
			
			return $clean_values;
		} else {
			return CHtml::encode(trim($values));
		}
	}
}