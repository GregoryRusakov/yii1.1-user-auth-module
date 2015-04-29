<?php

class InvitationsController extends Controller
{
	public function actionIndex()
	{
            
            $model=new Invitations;
            
            if(isset($_POST['Invitations'])){
                $model->attributes=$_POST['Invitations'];
                $isAvailable=Invitations::model()->checkAvaliable($model->guid);
                if (!$isAvailable){
                    Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Invitation is not available'));
                    $this->render('index', array('model'=>$model));
                    return;
                }
                //invitation ok, redirect to registration
                Yii::app()->user->setFlash('info', Yii::t('AuthModule.main','Invitation is available'));
                Yii::app()->user->setState('invitationGuid', $model->guid);
                $this->redirect(array('user/registration'));
            }
            else{
                //new form
                $model=new Invitations;
		$this->render('index', array('model'=>$model));
            }
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}