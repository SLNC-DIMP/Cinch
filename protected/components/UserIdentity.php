<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Sets id of current user
	 * @var integer
	 */
	private $_id;
	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->findByAttributes(array('username' => $this->username)); 
		
		if($user === null) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		} elseif($user->password !== $user->md5_encrypt($this->username, $this->password)) {
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		} else {
			$this->_id = $user->id;
			$this->errorCode=self::ERROR_NONE;
		}
		
		return !$this->errorCode;
	}
	
	/**
	 * Getter method for user's id.
	 * @return integer current user's id.
	 */
	public function getId() {
		return $this->_id;
	}
}