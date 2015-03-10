Yii 1.1 user auth module
=============
This module was created to authorize users in Yii 1.1 applications. 

Module supports basic authentification, login and password restore procedures.

Module also contains user and ip blocking mechanism to prevent bruteforce attacs.

You are welcome!
=============
If you want to add or modify some module code please feel free and just do it! 

Any help will be appreciated.

Installation
=============

To install Auth module please follow instruction below.

1. Copy folder "auth" and it's content to your folder /protected/modules/

2. Add to configuration file (/protected/config/main.php) these lines:

a.
	'import'=>array(
                //...
                //you old lines remains here
                //...

                'application.modules.auth.models.Users',

   	),

b.  	'modules'=>array(
                //...
                //you old lines remains here
                //...

                'auth'=>require(dirname(__FILE__).'/../modules/auth/config.php'),
        ),

c.  	'components'=>array(

		'user'=>array(
                    //...
                    //you old lines remains here
                    //...

                    'loginUrl'=>array('auth/user/login'),
                    'class'=>'application.modules.auth.components.authWebUser',
		),

d.      'homeUrl'=>array('auth/user/index'),

3.  Change parameters in /modules/auth/config.php file. Required paramter is 'adminEmail' used to send emails
    from module, all other parameters is optional and have default values.

4.  Create tables on your database (path to current database see in general settings of your application) according to file in /modules/auth/etc/database.sql

5.  You can open pages from your application according to these examples:
        echo CHtml::link("Login",array('/auth/user/login'));
        echo CHtml::link("Profile",array('/auth/user/index'));
        echo CHtml::link("Logout",array('/auth/user/logout'));
        echo CHtml::link("Register",array('/auth/user/registration'));
        echo CHtml::link("Restore password",array('/auth/user/passrequest'));
        echo CHtml::link("Change password",array('/auth/user/passchange'));

6.  You can change default layout for module pages in auth/components/Controller.php

Information
=============
Author: Gregory Rusakov
e-mail: greg@ontime.email

Current version.: 150307 1731

<img src="https://ga-beacon.appspot.com/UA-60499668-1/GregoryRusakov/yii1.1-user-auth-module" />
