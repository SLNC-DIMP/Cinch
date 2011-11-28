<?php
$this->breadcrumbs=array(
	'File File Information'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List File Information', 'url'=>array('index')),
	array('label'=>'View File Information', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage File Information', 'url'=>array('admin')),
);
?>

<h1>Update FileInfo <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>