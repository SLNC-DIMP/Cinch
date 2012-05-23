<?php
$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>
<h1>About CINCH</h1>

<p>
A project to develop a bulk download service to a central repository that will maintain original file timestamps, extract file level metadata, create file checksums and periodically validate checksums for continued file integrity. Users merely need to upload a list of URLs to download and when the process completes they can download the requested files and file metadata to their local environment.</p>

<p>CINCH currently works with the following file types:</p>
<ul>
	<li>PDF</li>
    <li>Microsoft Word</li>
    <li>Microsoft Excel</li>
    <li>Microsoft PowerPoint</li>
    <li>Jpeg</li>
    <li>PNG</li>
    <li>Gif</li>
    <li>Text files (e.g. files with .txt or .csv extensions)</li>
</ul>

<p>Learn more at <a href="http://digitalpreservation.ncdcr.gov/cinch/">http://digitalpreservation.ncdcr.gov/cinch/</a>.</p>

<p>Funding for the CINCH: Capture, Ingest, &amp; Checksum tool is made possible through an IMLS Sparks! Ignition grant.</p>