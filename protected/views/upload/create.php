<?php
$this->breadcrumbs=array(
	'Uploads'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Upload', 'url'=>array('index')),
	array('label'=>'Manage Upload', 'url'=>array('admin')),
);
?>

<h1>Create Upload</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>