<?php

class AdminModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		));
		
		$this->layout = 'main';
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			if(!Yii::app()->authManager->checkAccess('Admin', Yii::app()->user->id)) {
				throw new CHttpException(403, Yii::t('yii', 'Access denied.  Please contact Cinch with any questions'));
			} else {
				return true;
			}
		}
		else
			return false;
	}
}
