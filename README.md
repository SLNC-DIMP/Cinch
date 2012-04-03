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

Learn more at: http://digitalpreservation.ncdcr.gov/cinch/.

Funding for the CINCH: Capture, Ingest, & Checksum tool is made possible through 
an IMLS Sparks! Ignition grant.

<p xmlns:dct="http://purl.org/dc/terms/" xmlns:vcard="http://www.w3.org/2001/vcard-rdf/3.0#">
  <a rel="license"
     href="http://creativecommons.org/publicdomain/zero/1.0/">
    <img src="http://i.creativecommons.org/p/zero/1.0/80x15.png" style="border-style: none;" alt="CC0" />
  </a>
  <br />
  To the extent possible under law,
  <span rel="dct:publisher" resource="[_:publisher]">the person who associated CC0</span>
  with CINCH has waived all copyright and related or neighboring rights to
  <span property="dct:title">CINCH</span>.
This work is published from:
<span property="vcard:Country" datatype="dct:ISO3166"
      content="US" about="[_:publisher]">
  United States</span>.
</p>

-------------------------
Requirements

* Currently Cinch will only run on *nix systems
* PHP 5.3+ (PHP 5.4+ is recommended)
* MySQL or SQLite

After download, you may need to run the setup.sh shell file before initially running application.

-------------------------

Parts of Cinch include:

- Yii Framework <http://www.yiiframework.com>
- jQuery <http://jquery.com>
- jQuery UI <http://jqueryui.com>
- Apache Tika <http://tika.apache.org>
- ClamAV <http://www.clamav.net>