<?php
$this->breadcrumbs=array(
	'Zip Gz Downloads'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ZipGzDownloads', 'url'=>array('index')),
	array('label'=>'Create ZipGzDownloads', 'url'=>array('create')),
	array('label'=>'View ZipGzDownloads', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ZipGzDownloads', 'url'=>array('admin')),
);
?>

<h1>Update ZipGzDownloads <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>