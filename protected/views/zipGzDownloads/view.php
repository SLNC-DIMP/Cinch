<?php
$this->breadcrumbs=array(
	'Download Your Files'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Downloads', 'url'=>array('index')),
	array('label'=>'Update Downloads', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Downloads', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Downloads', 'url'=>array('admin')),
);
?>

<h1>View Your Downloads</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_id',
		'path',
		'downloaded',
		'creationdate',
	),
)); ?>
