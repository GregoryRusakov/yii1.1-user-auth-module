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
                    $this->renderIndex($model);
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
                $this->renderIndex($model);
                return;
            }
	}

        public function renderIndex($model){
            
            $maxAttemptsBeforeCaptha=(int)Common::getParam('attemptsBeforeCaptcha');
            
            if ($maxAttemptsBeforeCaptha!=0){
                $loginAtteptsInSession=(int)Yii::app()->session['loginAtteptsInSession'];

                if ($loginAtteptsInSession>$maxAttemptsBeforeCaptha) { 
                    $model->scenario = 'withCaptcha';
                }   

                Yii::app()->session['loginAtteptsInSession']=++$loginAtteptsInSession;

            }                           
            $this->render('index', array('model'=>$model));    
        }

        public function actions()
	{
		// return external action classes, e.g.:
            return array(
                // captcha action renders the CAPTCHA image displayed on the contact page
                'captcha'=>array(
                    'class'=>'CCaptchaAction',
                    'maxLength'=> 4,
                    'minLength'=> 4,
                    'testLimit'=>3,
                    'backColor'=>0xFFFFFF,
                ),        
            );
	}

}