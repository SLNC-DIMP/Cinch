<?php
$this->breadcrumbs=array(
	'Ftp Sites'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FtpSites', 'url'=>array('index')),
	array('label'=>'Create FtpSites', 'url'=>array('create')),
	array('label'=>'View FtpSites', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FtpSites', 'url'=>array('admin')),
);
?>

<h1>Update FtpSites <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>