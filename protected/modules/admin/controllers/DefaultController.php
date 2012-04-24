<?php

class DefaultController extends Controller
{	
	/**
	* @return array action filters
	*/
	public function filters()
	{
		return array(
			'rights',
		);
	}
	
	/**
	* Renders admin homepage
	*/
	public function actionIndex()
	{
		$this->render('index');
	}
}