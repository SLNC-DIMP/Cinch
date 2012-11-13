<?php

/**
 * This is the model class for table "download_list_settings".
 *
 * The followings are the available columns in table 'download_list_settings':
 * @property integer $id
 * @property string $url
 * @property integer $user_uploads_id
 * @property integer $user_id
 * @property integer $processed
 *
 * The followings are the available model relations:
 * @property UserUploads $userUploads
 * @property User $user
 */
class DownloadListSettings extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DownloadListSettings the static model class
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
		return 'download_list_settings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url, user_uploads_id, user_id', 'required'),
			array('user_uploads_id, user_id, processed', 'numerical', 'integerOnly'=>true),
			array('url', 'length', 'max'=>1024),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, url, user_uploads_id, user_id, processed', 'safe', 'on'=>'search'),
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
			'userUploads' => array(self::BELONGS_TO, 'UserUploads', 'user_uploads_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'url' => 'Url',
			'user_uploads_id' => 'User Uploads',
			'user_id' => 'User',
			'processed' => 'Processed',
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
		$criteria->compare('url',$this->url,true);
		$criteria->compare('user_uploads_id',$this->user_uploads_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('processed',$this->processed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}