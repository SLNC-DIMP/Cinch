<?php
/**
 * This is the model class for table "user_uploads".
 *
 * The followings are the available columns in table 'user_uploads':
 * @property integer $id
 * @property integer $user_id
 * @property string $upload_path
 * @property integer $processed
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Upload extends CActiveRecord {
	public $upload_path;
	
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
			array('upload_path', 'file', 'types'=>'txt, csv'),
			array('user_id, processed', 'safe')
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
		$criteria->compare('upload_path',$this->upload_path,true);
		$criteria->compare('processed',$this->processed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}