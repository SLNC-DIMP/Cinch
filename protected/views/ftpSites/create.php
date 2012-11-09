<?php
$this->breadcrumbs=array(
	'Ftp Sites'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List FtpSites', 'url'=>array('index')),
	array('label'=>'Manage FtpSites', 'url'=>array('admin')),
);
?>

<h1>Create FtpSites</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>