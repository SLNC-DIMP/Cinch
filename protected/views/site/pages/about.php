<?php
$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>
<h1>About Cinch</h1>

<p>
A project to develop a bulk download and FTP service to a central repository that will maintain original file timestamps, extract file level metadata, create file checksums and periodically validate checksums for continued file integrity. Users merely need to upload a list of URLs to download or an FTP address and when the process completes they can download the requested files and file metadata to their local environment. Funding for the CINCH: Capture, Ingest, &amp; Checksum tool is made possible through an IMLS Sparks! Ignition grant.
</p>