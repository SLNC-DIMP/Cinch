<?php
$this->breadcrumbs=array(
	'File Infos'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List File Information', 'url'=>array('index')),
	array('label'=>'Manage File Information', 'url'=>array('admin')),
);
?>

<h1>Create FileInfo</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>