<?php $this->pageTitle=Yii::app()->name; ?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<p>Congratulations on choosing Cinch!</p>

<p>You may change the content of this page by modifying the following two files:</p>
<ul>
	<li>View file: <tt><?php echo __FILE__; ?></tt></li>
	<li>Layout file: <tt><?php echo $this->getLayoutFile('main'); ?></tt></li>
</ul>

<p>A project to develop a bulk download and FTP service to a central repository that will maintain original file timestamps, extract file level metadata, create file checksums and periodically validate checksums for continued file integrity. Users merely need to upload a list of URLs to download or an FTP address and when the process completes they can download the requested files and file metadata to their local environment. Funding for the CINCH: Capture, Ingest, & Checksum tool is made possible through an IMLS Sparks! Ignition grant.</p>