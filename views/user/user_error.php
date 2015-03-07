<?php
/* @var $this UserController */

?>
<?php
    if (!isset($title)){
        $title=Yii::t('AuthModule.forms', 'Error. Title');
    }

    if (!isset($message)){
        $message=Yii::t('AuthModule.forms', 'Error. Message');
    }

    $this->pageTitle=$title;
    
?>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <?php echo '<h3 class="panel-title">'.$title.'</h3>'; ?>
            </div>
            <div class="panel-body">
                <?php
                    echo $message;
                ?>
            </div>
        </div>
    </div>
</div>