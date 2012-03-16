<?php
/**
 * This is the model class for table "user_uploads".
 *
 * The followings are the available columns in table 'user_uploads':
 * @property integer $id
 * @property integer $user_id
 * @property string  $path
 * @property integer $processed
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Upload extends CActiveRecord {
	public $path;
	public $files_in_list;
	const MAX_URLS = 5000;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserUploads the static model class
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
		return 'upload';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('urls_in_list', 'maxUrls', CUploadedFile::getInstance(self::model(),'path')),
			array('path', 'file', 'types'=>'txt, csv'),
			array('user_id, processed', 'safe')
		);
	}
	
	/**
	* Counts number of urls in a file and compares them to max allowed
	*/
	public function maxUrls($attribute, $params) { 
		if(is_object($params[0]) && file_exists($params[0]->tempName)) {
			$this->files_in_list = count(file($params[0]->tempName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
			
			if($this->files_in_list > self::MAX_URLS) {
				$this->addError('urls_in_list', 'Your list appears to have more than 5000 urls listed. Please limit your list to 5000 urls.');
			} 
		}
	} 
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('processed',$this->processed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}