A project to develop a bulk download service to a central repository 
that will maintain original file timestamps, extract file level metadata, 
create file checksums and periodically validate checksums for continued file integrity. 

Users merely need to upload a list of URLs to download and 
when the process completes they can download the requested files and file metadata 
to their local environment.

Currently supported file types:
 
 * PDF
 * Microsoft Word
 * Microsoft Excel
 * Microsoft PowerPoint
 * Jpeg
 * PNG
 * GIF
 * Text (e.g. files with .txt, .csv extensions, etc.)

<a href="http://digitalpreservation.ncdcr.gov/cinch/" target="_blank">Learn more about CINCH</a>.

<a href="http://cinch.nclive.org/Cinch/CINCHdocumentation.pdf">Full end user instructions</a>

Funding for the CINCH: Capture, Ingest, & Checksum tool is made possible through 
an IMLS Sparks! Ignition grant.

License:  CINCH is released under the Unlicense (http://unlicense.org/)
-------------------------
Requirements

* Currently Cinch will only run on *nix systems
* PHP 5.3+ compiled with --enable-cli flag and curl module.  This is probably already setup in your package manager (PHP 5.4+ is recommended).
* MySQL
* ClamAV

NOTE: If you've previously installed Cinch (before July 17th 2012 for those not interested in the ways of git) the current master branch and tagged versions 1.2 on break backwards compatibility with any previously downloaded version.  This was needed to greatly increase the security of encrypted passwords.  If you REALLY need a previous version please grab the version tagged 1.1, though you're strongly encouraged to install a newer version.  If you installed version 1.2 or earlier you'll need to login to your CINCH db and run the following:

ALTER TABLE `file_info` ADD `events_frozen` INT( 1 ) NOT NULL DEFAULT '0' COMMENT 'File events have ended due to error or file zipped and processing complete.' AFTER `problem_file` 

Please feel free to contact us if you have a problem.

After download, you may need to run the setup.sh shell file before initially running application to set file level permissions.

Setting up Cinch on your system:

<ol>
<li>Place the Cinch files in a web accessible directory</li>
<li>Create a new database MySQL database and import the project.sql file into it.</li>
<li>Open protected/config/main.php</li>
<li>Scroll down to the db settings (line 68 or so). Set the database name, the username and password for your new Cinch database.</li>
<li>Scroll to the bottom of main.php and set 'adminEmail' email address to your email address.
<li>You'll now need to repeat steps four and five in protected/config/console.php.</li>
<li>In protected/config/console.php if the setting in date_default_timezone_set() isn't correct you should change it to your timezone setting.  For a complete list of timezone settings see: http://us2.php.net/manual/en/timezones.php.</li>
<li>Go to http://tika.apache.org/download.html and download the Apache Tika jar file.</li>
<li>Place the Apache Tika jar file at the root of the Cinch/protected directory.</li>
<li>Configure Cinch cron tasks.  See the sample cron.txt file the root of Cinch for suggestions on how you might want to configure it.
</ol>
You should now be able to login to the web interface as: admin admin.
You should then go the change password tab and update your password.

If you don't want to run Cinch via cron you can run it from the command line.  If you navigate to Cinch/protected and run the following: path/to/php yiic.php you should be presented with a list of available commands.  The general way to run a command is: path/to/php yiic.php command.
Several commands such as checksum and purgeystem have subcommands, which have to be run like so from the command line: path/to/php yiic.php command sub-command.

You should run the commands in the following order:
<ol>
<li>readfile</li>
<li>download</li>
<li>viruscheck</li>
<li>checksum create</li>
<li>metadata</li>
<li>metadatacsv</li>
<li>checksum check (optional, recalculates checksum to see if anything has changed between download and current time.)</li>
<li>errorcsv</li>
<li>zipcreation</li>
<li>purgesystem check (optional, Notifies users after 20 days that they have files marked for deletion in 10 days.)</li>
<li>purgesystem delete (optional, deletes user files older than 30 days old.  Note this deletes upload lists, and all csv file information from the database, but downloaded files, metadata, errors, and event, information is retained in the database.)</li>
</ol>

Useful Notes:  

* You should only run the zipcreation command once a day otherwise it will cause conflicts in file processing.
* Uploaded url lists are saved into protected/uploads/"user's username". With the user's directory being created on first upload and being deleted thereafter if it's empty.
* Downloaded user files are saved  into protected/curl_downloads/"user's username". With the user's directory being created on first file downloaded and being deleted thereafter if it's empty.
* CINCH API documentation can be viewed at: http://cinch.nclive.org/c_docs/packages/db_Default.html.

Adding New Users:

Currently users can't self-register (This fit our own particular needs.)

* Login as user with admin privileges.  The default "admin" user has admin privileges by default.
* Then go to Admin->User Administration->Create User and add the user. (The user will be sent an email with their username and password.  Users may then login and change their password.)
* Next go to Admin->User Rights.  Click the user's username and then select the privileges you want them to have.
* You've now successfully added a user.

If you have problems with setting up users feel free to contact us.  You might want to take a look at the documentation for the Yii Rights extension used in CINCH: http://yii-rights.googlecode.com/files/yii-rights-doc-1.2.0.pdf

-------------------------

Parts of Cinch include:

- Yii Framework <http://www.yiiframework.com>
- jQuery <http://jquery.com>
- jQuery UI <http://jqueryui.com>
- Apache Tika <http://tika.apache.org>
- ClamAV <http://www.clamav.net>