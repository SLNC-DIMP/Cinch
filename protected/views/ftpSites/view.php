<?php
$this->breadcrumbs=array(
	'Ftp Sites'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List FtpSites', 'url'=>array('index')),
	array('label'=>'Create FtpSites', 'url'=>array('create')),
	array('label'=>'Update FtpSites', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete FtpSites', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage FtpSites', 'url'=>array('admin')),
);
?>

<h1>View FtpSites #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'path',
		'username',
		'password',
		'user_id',
	),
)); ?>
