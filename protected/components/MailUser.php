<?php
class MailUser {
	/**
	* Gets a users information
	* @param $user_id
	* @access public
	* @return object Yii DAO
	*/
	protected function getUser($user_id) {
		$user_info = Yii::app()->db->createCommand()
			->select('username, email')
			->from('user')
			->where(':id = id', array(':id' => $user_id))
			->queryRow();
		
		return $user_info;
	}
	
	/**
	* Mails a user that their files are ready.
	* Error writes ISO 8601 date, ex. 2004-02-12T15:19:21+00:00, and error to log.
	* @param $user_id
	* @access public
	* @return boolean
	*/
	public function UserMail($user_id, $subject, $message) {
		$user = $this->getUser($user_id);
		$to = $user['email'];
		$headers = 'From: cinch_admin@nclive.org' . "\r\n";
		
		$mail_sent = mail($to, $subject, $message, $headers);
		
		if($mail_sent == false) {
			$username = $user['username'];
			$error = date('c') . " Email could not be sent to: $username, regarding their downloads.\r\n";
			Yii::log($message, 'system.console.CConsoleCommand', 'warning');
		} else {
			echo 'Mail sent to: ' . $user['email'] . "\n";
		}
		
		return $mail_sent;
	}
}