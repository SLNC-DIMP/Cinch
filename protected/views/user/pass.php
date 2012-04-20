<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl."/js/clear_pass.js", CClientScript::POS_HEAD);

$this->breadcrumbs=array(
	'Change Password',
);
?>

<h1>Change the password for username: "<?php echo $model->username; ?>"</h1>
<?php echo $this->renderPartial('_formpass', array('model'=>$model)); ?>