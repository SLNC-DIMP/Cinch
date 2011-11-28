<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1>Administration Panel</h1>
<?php
$admin_menu = array(
				array('label'=>'User Administration', 'url'=>array('/user')),
				array('label'=>'Uploaded Files', 'url'=>array('/upload/admin')),
			//	array('label'=>'Downloaded Files', 'url'=>array('/fileInfo')),
				array('label'=>'Zip Files', 'url'=>array('/zipGzDownloads')),
			); 

$this->widget('zii.widgets.CMenu',array(
			'items'=>$admin_menu));
?>