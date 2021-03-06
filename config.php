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
        'attemptsBeforeCaptcha'=>10,
        'cookieBasedLoginDays'=>30,
        'userBlockMaxLoginAttempts'=>15,
        'userBlockTimeMinutes'=>30,
        'ipBlockMaxLoginAttempts'=>5,
        'ipBlockTimeMinutes'=>30,              
        'dateFormat'=>'Y-m-d H:i:s',              
        'timeZoneLabel'=>'UTC',     
        'profilePage'=>array('/usernews'),
        'defaultController'=>'user/login',
        'useInvitations'=>true,
    );
     