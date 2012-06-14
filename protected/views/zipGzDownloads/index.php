<?php
$this->breadcrumbs=array(
	'Download Your Files',
);

if(Yii::app()->user->checkAccess('deleteUser')) {
	$this->menu=array(
		array('label'=>'Manage Downloads', 'url'=>array('admin')),
	);
}
?>

<h1>Your Available Downloads</h1>
<h5>Note: the contents of each zip file should be unique</h5>
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
