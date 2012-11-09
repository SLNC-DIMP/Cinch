<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/cinch.css" />

    <?php Yii::app()->clientScript->registerCoreScript('jquery');  ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
    	<div><?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/cinch-header.png'); ?></div>
	<!--	<div id="logo"><?php // echo CHtml::encode(Yii::app()->name); ?></div> -->
        
	</div><!-- header -->

	<div id="mainmenu">
    	<?php
			$menu_items = array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'About the Tool', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'About the Project', 'url'=>array('/site/page', 'view'=>'project')),
				array('label'=>'FAQ', 'url'=>array('/site/page', 'view'=>'faq')),
				array('label'=>'Contact', 'url'=>array('/site/contact')),
			);
			if(!Yii::app()->user->isGuest) {
				$menu_items[] =  array('label'=>'Upload', 'url'=>array('/upload'));
                $menu_items[] =  array('label'=>'FTP', 'url'=>array('/ftpSites'));
				$menu_items[] =  array('label'=>'Download Files', 'url'=>array('/zipGzDownloads'));
				$menu_items[] =  array('label'=>'Change Password', 'url'=>array('/user/pass/' . Yii::app()->user->id));
			}
			if(Yii::app()->authManager->checkAccess('Admin', Yii::app()->user->id)) {
				$menu_items[] =  array('label'=>'Admin', 'url'=>array('/admin'));
			}
			$menu_items[] = array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest);
			$menu_items[] = array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 
			'visible'=>!Yii::app()->user->isGuest);
		?>
		
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>$menu_items)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div id="footer">

	</div><!-- footer -->

</div><!-- page -->

</body>
</html>