<?php

class ZipGzDownloadsController extends Controller
{
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

		if(isset($_POST['ZipGzDownloads']))
		{
			$model->attributes=$_POST['ZipGzDownloads'];
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('ZipGzDownloads');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ZipGzDownloads('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ZipGzDownloads']))
			$model->attributes=$_GET['ZipGzDownloads'];

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
		$model=ZipGzDownloads::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='zip-gz-downloads-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	* Courtesy of phpnet at holodyn dot com http://us3.php.net/manual/en/function.header.php
	* @param $id id of file to download
	*/
	public function actionDownload($id) {
		$model = $this->loadModel($id);
		$fullPath = $model['path'];
		
		// Must be fresh start
		if( headers_sent() ) {
			die('Headers Sent');
		}
	  
		// Required for some browsers
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
	  
		// File Exists?
		if(file_exists($fullPath) && $model['user_id'] == Yii::app()->user->id) {
			// Parse Info / Get Extension
			$path_parts = pathinfo($fullPath);
			   
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false); // required for certain browsers
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($fullPath));
			ob_clean();
			flush();
			readfile($fullPath);
		} elseif(file_exists($fullPath) && $model['user_id'] != Yii::app()->user->id) {
			throw new CHttpException(403,'Permissions error. You do not have access to this file.');
		} else {
			throw new CHttpException(400,'The requested file does not exist.');
		}
	} 
}
