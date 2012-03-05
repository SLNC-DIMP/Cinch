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
	
	  /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if(isset($_POST['Upload']))
        {
            $model->attributes=$_POST['Upload'];
			
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new Upload('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Upload']))
            $model->attributes=$_GET['Upload'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Upload::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='upload-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
	
	public function actionIndex() {
		$user_upload_dir = $this->getUsrUploadDir();
		$uploaded = false;
		$model = new Upload;
		
		if(isset($_POST['Upload'])) {
			$file = CUploadedFile::getInstance($model,'path');
			
			if($model->validate()){
				if(!is_dir($user_upload_dir)) {
					mkdir($user_upload_dir);
				}
				
				$mod_name = $this->encryptName($file->getName());
				$user_id = Yii::app()->user->id;
				$upload_path = $user_upload_dir . '/' . $mod_name;
				$model->attributes = array('user_id' => $user_id, 'path' => $upload_path);
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