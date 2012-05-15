<?php
$this->pageTitle=Yii::app()->name . ' - FAQ';
$this->breadcrumbs=array(
	'FAQ',
);
?>
<h1>FAQ</h1>
<p>Note: This page is very much a work in progress.</p>

<h3>How can I use CINCH?</h3>
<p>We're currently in alpha testing with a small group of users.  When CINCH is released live if your institution is a library or other cultural entity in the state of North Carolina you can take advantage of our hosted service.
Please send an email to the CINCH administrative team (<a href="mailto:cinch_admin@cinch.nclive.org">CINCH Admin</a>) and we'll set you up with an account.  If your are not a North Carolina based cultural institution or would prefer to host a CINCH
instance yourself please see our <a href="http://slnc-dimp.github.com/Cinch/">GitHub page</a> for instructions on installing CINCH.</p>

<h3>How do I create an upload list?</h3>
<p>Simply create a file with a .txt or .csv extension placing the full url for each file to download on its own line like so:</p>
<ul id="no_bullets">
	<li>http://mysite.gov/pdfs/important.pdf</li>
    <li>https://agency.org/files/2012/my_file.docx</li>
    <li>http://anothersite.gov/docs/another_file.doc</li>  
</ul>

<h3>Why can my upload list only have 10,000 files listed for CINCH to collect?</h3>
<p>This is because our server has problems dealing with a large number files at one pass.  However feel free to upload as many lists as you like.  We'll get to them all.</p>

<h3>Should I save a copy of my upload list(s)?</h3>
<p>Yes, if you want to compare your upload list(s) with the file manifest contained in each zip file.  Note:  the files from any particular list might be split up over multiple zip files.</p>

<h3>Why don't all the files on my list(s) appear in the same zip file?</h3>
<p>There are several reasons for this potentially occurring:</p>
<ul>
	<li>A limit has been set on how large zip files are allowed to grow.  Otherwise they become impossible to download.  This necessiates splitting your files across multiple zip files if their total size is greater than 0.5GB.</li>
    <li>Your files are processed along with everyone elses who might have file lists uploaded.  A limit has been set to how many total files can be processed at a given time which will by neccessity splits your files up.</li>
</ul>

<h3>Why are my files deleted after 30 days?</h3>
<p>Simply put we don't have enough room on our server to keep everyones files in perpetuity.  You will receive an email 20 days after your files are processed notifying you that your files will be deleted soon.</p>

<h3>What is the life cycle of my files in CINCH?</h3>
<p>Here's a high level overview of what happens after you upload your url list:</p>
<ol>
	<li>Each url from your list is written to a database for processing.</li>
    <li>Each file is downloaded.</li>
    <li>Each file is run through a virus check.</li>
    <li>Each file's checksum is generated.</li>
    <li>Each file's metadata is extracted.</li>
    <li>Each file's metadata is added to a metadata.csv for inclusion in a zip file</li>
    <li>Any files with errors have their error information added to an error.csv for inclusion in a zip file.</li>
    <li>Each file is added to a zip file along with the metadata.csv file, errors.csv file, and a file_events_history.csv file.</li>
    <li>You receive a message that you have files are ready for download.</li>
    <li>After 20 days you receive an email that your files will be deleted soon.</li>
    <li>After 30 days your files are deleted from the system.</li>
</ol>

