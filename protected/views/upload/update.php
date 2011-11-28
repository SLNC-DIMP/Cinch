<?php
$this->breadcrumbs=array(
	'Uploads'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Upload', 'url'=>array('index')),
	array('label'=>'Create Upload', 'url'=>array('create')),
	array('label'=>'View Upload', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Upload', 'url'=>array('admin')),
);
?>

<h1>Update Upload <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>