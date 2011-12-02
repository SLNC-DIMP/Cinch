<?php
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1>Administration Panel</h1>
<?php
$admin_menu = array(
				array('label'=>'User Administration', 'url'=>array('/user')),
				array('label'=>'Uploaded File Lists', 'url'=>array('/upload/admin')),
				array('label'=>'Downloaded Files', 'url'=>array('/fileInfo/admin')),
				array('label'=>'Metadata', 'url'=>array('/metadata')),
				array('label'=>'Zip Files', 'url'=>array('/zipGzDownloads')),
			); 

$this->widget('zii.widgets.CMenu',array(
			'items'=>$admin_menu));
?>