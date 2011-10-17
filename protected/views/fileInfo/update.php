<?php
$this->breadcrumbs=array(
	'File Infos'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FileInfo', 'url'=>array('index')),
	array('label'=>'Create FileInfo', 'url'=>array('create')),
	array('label'=>'View FileInfo', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FileInfo', 'url'=>array('admin')),
);
?>

<h1>Update FileInfo <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>