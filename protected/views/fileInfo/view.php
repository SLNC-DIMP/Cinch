<?php
$this->breadcrumbs=array(
	'File Infos'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List FileInfo', 'url'=>array('index')),
	array('label'=>'Update FileInfo', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete FileInfo', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage FileInfo', 'url'=>array('admin')),
);
?>

<h1>View FileInfo #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'org_file_path',
		'temp_file_path',
		'file_type_id',
		'checksum',
		'virus_check',
		'dynamic_file',
		'last_modified',
		'problem_file',
		'user_id',
		'upload_file_id',
	),
)); ?>
