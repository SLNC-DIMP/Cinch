<?php

class UserController extends Controller
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()) {
				$this->mailUser();
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['User']))
		{
			//$_POST['User'] = array_slice($_POST['User'], 0, 2);
			$model->attributes=$_POST['User'];
			
			if($model->validate(array('username', 'email'), false) && $model->update()) {
				Yii::app()->user->setFlash('success', "User successfully updated!");
			}
		} 
		
		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Updates a user's password.  User must be logged in for this action to happen.
	 * Also restricts accessing other users' passwords
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionPass($id)
	{ 
		if($id === Yii::app()->user->id) {
			$model=$this->loadModel($id);
	
			if(isset($_POST['User']))
			{
				$model->attributes=$_POST['User'];
				
				if($model->validate(array('password', 'password_repeat'), false) && $model->update()) {
					$this->mailUser();
					Yii::app()->user->setFlash('success', "Password successfully updated!");
				}
			}
	
			$this->render('pass',array(
				'model'=>$model,
			));
		} else {
			throw new CHttpException(403,'You are not allowed to perform this action.');
		}
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
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		)); 
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

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
		$model=User::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Email user their login info.
	 * @access public
	 */
	public function mailUser() {
		$from = 'From: ' . Yii::app()->params['adminEmail'] . "\r\n" .
		$message = "Your CINCH Credentials:\r\n";
		$message .= "Username: " . $this->username . "\r\n";
		$message .= "Password: " . $this->password;
		
		mail($this->email, 'Your CINCH Credentials', $message, $from);
	}
}
