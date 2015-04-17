<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class HybridController extends Controller
{
    
    public function actionIndex(){
        echo "Hybrid auth";
    }     

    public function actionTest(){
        echo "Hybrid test";
    }     

    public function actionLogin($service){
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }
        
        require_once( "/../../../../myphp/hybridauth/Hybrid/Auth.php" );
        $config=require( "/../../../../myphp/hybridauth/config.php" );
     
        $config['base_url']=Yii::app()->getBaseUrl(true) . $config['base_url'];
        
        try{
            $hybridauth = new Hybrid_Auth($config);
            $adapter = $hybridauth->authenticate($service);
            $user_profile = $adapter->getUserProfile();
        }catch(Exception $ex){
            //var_dump($ex);
            
            $errorMessage=$ex->getMessage();
            Yii::log($errorMessage, CLogger::LEVEL_WARNING, 'hybridAuth');
            Yii::app()->user->setFlash('warning', 'Вход через сервис ' . $service . ' не был выполнен.');
            $errorLoginUrl=Yii::app()->createUrl('auth');
            echo '<script>
                if (window.opener){
                    window.opener.location.href="' . $errorLoginUrl . '"
                    window.close();
                }else {
                }
                </script>';
            exit();
            
        }

        $successLoginUrl=Yii::app()->createUrl('userprofiles');
        echo '<script>
            if (window.opener){
                window.opener.location.href="' . $successLoginUrl . '"
                window.close();
            }else {
            }
            </script>';
        
        //$this->redirect('admin');
        exit();
        
        var_dump($user_profile);
        
        //check if user exist in database
        //if not then create user
        
        
        //login user
        
    }
 
}
