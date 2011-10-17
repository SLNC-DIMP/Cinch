<?php
$this->breadcrumbs=array(
	'File Infos',
);

$this->menu=array(
	array('label'=>'Create FileInfo', 'url'=>array('create')),
	array('label'=>'Manage FileInfo', 'url'=>array('admin')),
);
?>

<h1>File Infos</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
