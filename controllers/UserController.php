<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class UserController extends Controller
{
    const VALIDATOR_ACTIVATE=0;
    const VALIDATOR_RESTORE=1;  
            
    public function actionTest(){
        //echo "test helper from controller<br><br>";
        //tester\Help::test();
    }
    
    public function actionIndex(){
        $userId=Yii::app()->user->getId();
        $model=$this->loadModel($userId);                
        $this->render('index', array('model'=>$model));
    }     

    public function actionLogin(){
                
        $isAjax=Yii::app()->request->isAjaxRequest;
          
        $formLogin=new LoginForm;
        // collect user input data
        if(isset($_POST['LoginForm'])){
            
            $formLogin->attributes=$_POST['LoginForm'];
            if(empty($formLogin->username)){
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Login failed').'. '.Yii::t('AuthModule.main','Empty login'));
                $this->render('login', array('model'=>$formLogin));
                return;   
            }
            
            if(empty($formLogin->password)){
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Login failed').'. '.Yii::t('AuthModule.main','Empty password'));
                $this->render('login', array('model'=>$formLogin));
                return;   
            }            
            // validate user input and redirect to the previous page if valid

            $validated=$formLogin->validate();
            $loggedIn=($validated && $formLogin->login());
            
            if ($isAjax){
                if ($loggedIn){
                    $response=array('status'=>'success', 'message'=>'Successfully logged in');
                }
                else{
                    $response=array('status'=>'error', 'message'=>'Login error');
                }
                //create response and end
                echo CJSON::encode($response);
                Yii::app()->end();           
            }
            else{
                //not ajax
                if ($loggedIn){
                    $loggedUserPage=Yii::app()->user->getState('openPageAfterLogin');
                    if ($loggedUserPage==null){
                        $loggedUserPage=Common::getParam('profilePage');   
                    }
                    Yii::app()->user->setState('openPageAfterLogin', null);
                    $this->redirect($loggedUserPage);
                }
                else{
                    if (!Yii::app()->user->hasFlash('error')){
                        Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Login failed').'. '.Yii::t('AuthModule.main','Incorrect login or password'));
                    }
                    $this->render('login', array('model'=>$formLogin));
                    return; 
                }
            }
        }

        $username=Yii::app()->user->getState('username');
        $formLogin->username=$username;
        
        if ($isAjax){
            //$this->renderPartial('login',array('model'=>$formLogin));
            $this->renderPartial('login',array('model'=>$formLogin), false, true);
        }
        else{
            $this->render('login',array('model'=>$formLogin));
        }
        
    }

    public function actionLogout(){
        
        // Generate a login token and save it in the DB
        
        $userId=Yii::app()->user->getId();
        $modelUser=Users::model()->findByPk($userId);
        $modelUser->setScenario('lastlogin');
        $modelUser->logintoken = null;
        $modelUser->save();
        
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->user->returnUrl);   
    }      
    
    public function actionRegistration(){
            
        $model=new Users;
        
        if(isset($_POST['Users'])){

            //this is a second call this action but with form data,
            //so we need to update and save User model

            $model->attributes=$_POST['Users'];

                if(!$model->validate()){
                //haven't passed validators
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Incorrect form data'));
                $this->render('change', array('model'=>$model));
                return;
            }

            if (!$model->saveModel()){
                $this->render('change', array('model'=>$model));
                return;
            }

            $email=$model->email;
            $user_id=$model->id;

            Yii::app()->user->setState('username', $model->username);

            $guid=Common::getGUID();

            $validations=new Validations;
            $validations->guid=$guid;
            $validations->user_id=$user_id;
            $validations->email=$email;
            $validations->type=self::VALIDATOR_ACTIVATE;

            $date = new DateTime();
            $date->modify("+24 hours");
            $exp_time=$date->format(Common::getParam('dateFormat'));

            $validations->exp_datetime=$exp_time;
            $validations->comments='Activate new user';

            if(!$validations->validate() || !$validations->save()){
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Form validation error'));
                $this->redirect(array('user/registration'));
            }

            if (Common::sendActivationtEmail($model->email, $guid)){
                Yii::app()->user->setFlash('success', sprintf(Yii::t('AuthModule.main','Activation email has been sent to address'), $email));
                $this->redirect(array('user/activation'));
            }
            else{
                Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','Error sending email'), $email));
                $this->redirect(array('user/registration'));
            }
        }
        else{
            $this->render('change',array('model'=>$model));
        }
    }
    
    public function actionActivation($guid=''){
            
        $formActivation=new UserActivationForm;

        if(isset($_POST['UserActivationForm'])){

            $formActivation->attributes=$_POST['UserActivationForm'];

            if(!$formActivation->validate()){
                //haven't passed validators
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Incorrect data in activation form'));
                $this->redirect(array('user/activation'));
                return;
            }

            $guid=$formActivation->guid;
            
            $user_id=$this->getRestoreUserById($guid, self::VALIDATOR_ACTIVATE);
            if ($user_id==null){
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','ID not found'));
                $this->redirect(array('user/activation'));
                return;
            }
                    
            //update user password
            $modelUser=$this->LoadModel($user_id);
            if ($modelUser->activated){
                Yii::app()->user->setFlash('warning', Yii::t('AuthModule.main','User already activated'));
                $this->redirect(array('user/login'));
                return;
            }

            $modelUser->activated=true;
            $modelUser->setScenario('activation');

            if ($modelUser->saveModel()){
                Yii::app()->user->setFlash('success', Yii::t('AuthModule.main','User successfully activated'));
                $this->deleteRestoreGuid($guid, self::VALIDATOR_ACTIVATE);
                $loginForm=new LoginForm;
                $loginForm->username=$modelUser->username;
                $this->redirect(array('user/login'), array('model'=>$loginForm));
                return;
            }
            
        }
        else{
            $formActivation->guid=$guid;
            $this->render('activation',array('model'=>$formActivation));
        }
    }
    
    public function actionUpdate()
    {
        $userId=Yii::app()->user->getId();
        $model=Users::model()->findByPk($userId);

        if(isset($_POST['Users'])){
            //this is a second call with form data,
            //so we need to update and save User model

            $model->attributes=$_POST['Users'];

            if($model->validate())
            {
                $model->saveModel();
                yii::app()->user->setFlash('success', Yii::t('AuthModule.main','User data have been changed'));
                $this->render('index',array('model'=>$model));
                return;

            }
        }

        $this->render('change',array('model'=>$model));

    }        

    public function actionPassRequest(){

        if(!isset($_POST['PassRequestForm'])){
            //new request
            $requestForm=new PassRequestForm();
            $this->render('passrequest', array('model'=>$requestForm));
        }
        else{
            //requst form has been filled

            $requestForm=new PassRequestForm;
            $requestForm->attributes=$_POST['PassRequestForm'];
            
            if(!$requestForm->validate()){
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Restore form validation failed'));
                $this->render('passrequest', array('model'=>$requestForm));
                return;
            }

            //for is correct so, prepare and send request email
            
            $email=$_POST['PassRequestForm']['email'];

            //create new guid and sent it to user
            $guid=Common::getGUID();

            $userModel=new Users;
            $user=$userModel->getByEmail($email);
            if ($user==null){
                yii::app()->user->setFlash('warning', sprintf(Yii::t('AuthModule.main','Email address was not found'),$email));
                $this->redirect(array('user/passrequest'));
                return; 
            }
            $user_id=$user->id;

            $validations=new Validations;
            $validations->guid=$guid;
            $validations->user_id=$user_id;
            $validations->email=$email;
            $validations->type=self::VALIDATOR_RESTORE;

            $date = new DateTime();
            $date->modify("+24 hours");
            $exp_time=$date->format(Common::getParam('dateFormat'));

            $validations->exp_datetime=$exp_time;
            $validations->comments='Restore user password';
            
            if(!$validations->validate()){
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Password restore not complited'));
                $this->render('passrequest', array('model'=>$requestForm));
                return;
            }

            if (!$validations->save()){
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Password restore not complited'));
                $this->render('passrequest', array('model'=>$requestForm));
                return;
            }

            //send email with restore link
            if (!$requestForm->validate()){
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Restore form validation failed'));
                $this->render('passrequest', array('model'=>$requestForm));
                return;
            }

            $result=Common::sendPassRequestEmail($email, $guid);

            if ($result) {
                Yii::app()->user->setFlash('success', sprintf(Yii::t('AuthModule.main','Password restore link has been sent'), $email));
                $this->redirect(array('user/passchange'));
            }
            else{
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Error sending email message'));
                $this->redirect(array('user/passrequest'));
            }
        }    
    }

    public function actionPassChange($guid=''){

        if(isset($_POST['PassChangeForm'])){

            //new password entered in the form
            $model=new PassChangeForm();
            $model->attributes=$_POST['PassChangeForm'];
            if(!$model->validate()){
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Form validation error'));
                $this->render('passchange', array('model'=>$model));
                return;
            }

            $guid=$model->guid;

            $user_id=$this->getRestoreUserById($guid, self::VALIDATOR_RESTORE);
            if ($user_id==null){
                if (!Yii::app()->user->hasFlash('error')){
                    Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Incorrect data in the form'));
                }
                $this->render('passchange', array('model'=>$model));
                return;
            }

            //update user password
            $modelUser=$this->LoadModel($user_id);

            $modelUser->password_entered=$_POST['PassChangeForm']['password'];
            $modelUser->setScenario('passRestore');

            if ($modelUser->saveModel()){
                yii::app()->user->setFlash('success', Yii::t('AuthModule.main','Password successfully changed'));
                $this->deleteRestoreGuid($guid, self::VALIDATOR_RESTORE);
                $username=Yii::app()->user->setState('username', $modelUser->username);
                $this->redirect(array('user/login'));
            }
            else{
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Error changing password'));
                $this->redirect(array('user/passchange'),array('guid'=>$guid));
            }

        }
        else{
            //create new form

            if ($guid!=null && $this->getRestoreUserById($guid, self::VALIDATOR_RESTORE)==null){
                //wrong guid
                yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Incorrect ID'));
                $this->redirect(array('user/passrequest'));

            }

            $model=new PassChangeForm;
            $model->guid=$guid;
            $this->render('passchange',array('model'=>$model));
        }
    }       

    private function getRestoreUserById($guid, $validation_type){

        //find guid 

        $criteria=new CDbCriteria;
        $criteria->select='*';
        $criteria->addCondition('guid=:param1');
        $criteria->addCondition('type=:param2');
        $criteria->params=array(':param1'=>$guid, ':param2'=>$validation_type);
        $criteria->order = "exp_datetime DESC";
        $criteria->limit = 1;

        $validations=Validations::model()->find($criteria);
        if ($validations==null){
            //incorrect guid
            Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Restore failed. ID was not found'));
            return null;
        }

        if ((time()-$validations->exp_datetime)<0){
            Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Restore failed. ID is obsolet'));
            return null;
        }

        return $validations->user_id;

    }

    private function deleteRestoreGuid($guid, $validationType){

        $criteria=new CDbCriteria;
        $criteria->select='*';
        $criteria->addCondition('guid=:param1');
        $criteria->addCondition('type=:param2');
        $criteria->params=array(':param1'=>$guid, ':param2'=>$validationType);

        Validations::model()->deleteAll($criteria);

    }

    public function loadModel($id) {
        $model = Users::model()->findByPk($id);
        if ($model === null){
            throw new CHttpException(404, sprintf(Yii::t('AuthModule.main','Requested user does not exist'), $id));
        }
        return $model;
    }

    public function actions()
    {
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

    public function beforeAction(CAction $action){        

        $controller=$action->controller->id;
        $currentAction=$action->id;
      
        if(Yii::app()->user->isGuest){
            if ($controller=='user'){
               
                $pagesAllowedForGuest=array('login','registration','passrequest','passchange','captcha', 'activation', 'test');
                
                if (!in_array($currentAction, $pagesAllowedForGuest)){
                    Yii::app()->user->setFlash('info', Yii::t('AuthModule.main','Requested page require authorization'));
                    $returnUrl=Yii::app()->createUrl($controller."/".$currentAction);
                    Yii::app()->user->setState('openPageAfterLogin',$returnUrl);
                    Yii::app()->user->loginRequired();
               }
           }
        }
        elseif($controller=='user' && $currentAction=='login'){
            Yii::app()->user->setFlash('info', Yii::t('AuthModule.main','Requested page is not available in this mode')); 
            $this->redirect(Yii::app()->getHomeUrl());
        }
        
        return true;
    }

}
