
    <?php
        $this->pageTitle=Yii::t('AuthModule.forms', 'User profile. Title');
    ?>

    <?php
        echo '<p>'.Yii::t('AuthModule.forms', 'User profile. Header').' '.$model->username.'</p>';
    ?>

    <?php 
        if (Yii::app()->user->isGuest){
            throw new CHttpException(404, Yii::t('AuthModule.forms', 'User profile. User not exist'));
        }
    ?>

    <p> 
        <?php echo CHtml::link(Yii::t('AuthModule.forms', 'User profile. Edit profile'),array('user/update'));?>
    </p>

    <p> 
        <?php echo CHtml::link("Управление подписками",array('subscribes'));?>
    </p>
    
