<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

    return array(
        
        //'websiteHost'=>'http://localhost/trader-news.ru',
        'attemptsBeforeCaptcha'=>2,
        'cookieBasedLoginDays'=>5,
        'userBlockMaxLoginAttempts'=>50,
        'userBlockTimeMinutes'=>30,
        'ipBlockMaxLoginAttempts'=>70,
        'ipBlockTimeMinutes'=>30,              
        'dateFormat'=>'Y-m-d H:i:s',              
        'timeZoneLabel'=>'МСК',     
        'adminEmail'=>'root@localhost',
        'profilePage'=>array('user/index'),
        'defaultController'=>'user/login',
        'useInvitations'=>true,

    );
     