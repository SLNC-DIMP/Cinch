<?php
$this->breadcrumbs=array(
	'Zip Gz Downloads',
);

$this->menu=array(
	array('label'=>'Create ZipGzDownloads', 'url'=>array('create')),
	array('label'=>'Manage ZipGzDownloads', 'url'=>array('admin')),
);
?>

<h1>Your Available Downloads</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
