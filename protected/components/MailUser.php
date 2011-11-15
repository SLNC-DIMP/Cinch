<?php
class MailUser {
	protected function getUser($user_id) {
		$user_info = Yii::app()->db->createCommand()
			->select('username, email')
			->from('user')
			->where(':id = id', array(':id' => $user_id))
			->queryRow();
		
		return $user_info[0];
	}
	
	public function mailUser($user_id) {
		$user = $this->getUser($user_id);
		
		$to = $user['email'];
		$subject = 'Your Cinch files are ready';
		$message = 'You have files ready for download from Cinch.  Please login at cinch.nclive.org to retrieve your files.';
		$headers = 'From: webmaster@example.com' . "\r\n";
		
		$mail_sent = mail($to, $subject, $message, $headers);
		
		if($mail_sent == false) {
			
		}
	}
}