<?php
/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 */
class User extends CActiveRecord
{
	public $password_repeat;
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, email, password_repeat', 'filter', 'filter' => 'trim'),
			array('username, email', 'unique'),
			array('username, password, password_repeat, email', 'required'),
			array('username, password, password_repeat', 'length', 'max'=>25),
			array('email', 'length', 'max'=>256),
			array('email', 'email'),
			array('password', 'compare'),
			array('password_repeat', 'safe'),
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, email', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'email' => 'Email',
			'password' => 'Password',
			'password_repeat' => 'Re-enter Password'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	* encrypt password for insertion into db
	* @access public
	*/
	public function afterValidate() {
		parent::afterValidate();
		$this->mailUser();
		$this->password = $this->md5_encrypt($this->username, $this->password);
	}
	
	/**
	* wrapper for the PHP md5() function
	* Doesn't really use md5 anymore.  Changed to use Blowfish encryption.
	* Returned string should always be at least 13 characters.
	* See http://us.php.net/crypt for details.
	* @access public
	* @return string
	*/
	public function md5_encrypt($username, $password) {
		$encrypted = crypt($password, '$2a$08$' . Yii::app()->params['passwordSalt']);
		if($encrypted >= strlen(13)) {
			return $encrypted;
		} else {
			throw new Exception('Unable to encrypt user password.', 500);
		}	
	}
	
	/**
	 * Email user their login info.
	 * @access public
	 */
	public function mailUser() {
		$from = 'From: ' . Yii::app()->params['adminEmail'] . "\r\n" .
		$message = "Your CINCH Credentials:\r\n";
		$message .= "Username: " . $this->username . "\r\n";
		$message .= "Password: " . $this->password;
		
		mail($this->email, 'Your CINCH Credentials', $message, $from);
	}
}