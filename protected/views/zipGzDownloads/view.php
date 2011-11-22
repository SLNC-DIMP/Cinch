<?php
$this->breadcrumbs=array(
	'Zip Gz Downloads'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ZipGzDownloads', 'url'=>array('index')),
	array('label'=>'Create ZipGzDownloads', 'url'=>array('create')),
	array('label'=>'Update ZipGzDownloads', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ZipGzDownloads', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ZipGzDownloads', 'url'=>array('admin')),
);
?>

<h1>View ZipGzDownloads #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_id',
		'archive_path',
		'downloaded',
		'creationdate',
	),
)); ?>
