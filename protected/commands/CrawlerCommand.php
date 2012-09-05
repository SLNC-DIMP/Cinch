<?php
class CrawlerCommand extends CConsoleCommand {
	public function crawlSite($url) {
		$retval = 1;
		$command = 'anemone url-list' . ' ' . "$url";
		system(escapeshellcmd($command), $retval);
	
	//  this option probably the better one	
	//	$command = 'anemone cron' . ' ' . "$url";
	//	system(escapeshellcmd($command), $output);
		
		return $output;
	}
	
	public function run() {
	
	}
}