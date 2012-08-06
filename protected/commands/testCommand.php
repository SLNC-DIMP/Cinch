<?php
class testCommand extends CConsoleCommand {
	public $mail_user;
	
	public function __construct() {
		$this->mail_user = new MailUser;
	} 
	
	public function run() {
		echo $this->mail_user->UserMail(3, 'hello', 'stuff') . "\r\n";
		echo Yii::app()->params['adminEmail'] . "\r\n";
	}
}