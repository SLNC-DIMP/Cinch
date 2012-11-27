<?php
Yii::import('application.lib.PasswordHash');
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
	* mail user their user info
	* @access public
	*/
	public function afterValidate() {
		parent::afterValidate();
		$this->mailUser();
	}
	
	/**
	* Encrypt user's password using Blowfish algorithm and bcrypt
	* @param $password
	* @param $db_pass
	* @access public
	* @return boolean
	*/
	public function validatePassword($password, $db_pass) {
        $check_pass = new PasswordHash(8, false);
        
		return $check_pass->checkPassword($password, $db_pass);
    }
 	
	 /**
	 * Replace the raw password with the hashed one
	 * @access public
	 * @return boolean
	 */
    public function beforeSave() {
        if (isset($this->password)) {
            $new_pass = new PasswordHash(8, false);
            $this->password = $new_pass->HashPassword($this->password);
        }
        return parent::beforeSave();
    }
	
	/**
	 * Email user their login info.
	 * @access public
	 */
	public function mailUser() {
		$from = 'From: ' . Yii::app()->params['adminEmail'] . "\r\n" .

		$message = "Username: " . $this->username . "\r\n";
		$message .= "Password: " . $this->password;
		
		mail($this->email, 'Your CINCH Credentials', $message, $from);
	}
}