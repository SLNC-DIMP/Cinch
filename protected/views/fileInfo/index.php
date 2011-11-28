<?php
$this->breadcrumbs=array(
	'File Information',
);

$this->menu=array(
	array('label'=>'Manage File Information', 'url'=>array('admin')),
);
?>

<h1>File Information</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
