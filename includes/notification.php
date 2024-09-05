<?php
if(isset($notifications)){
    foreach ($notifications as $keyNotification => $tabNotification){
            foreach ($tabNotification as $notification){
                $checkNotif = false;
                if($keyNotification == 'success'){
                    $colorClass = 'notification-success-color';
                    $icon = '<i class="far fa-check-circle notification-icon '.$colorClass.'"></i>';
                    $title = 'Succès';
                    $checkNotif = true;
                    $class = 'container-notification-success';
                }
                elseif($keyNotification == 'error'){
                    $colorClass = 'notification-error-color';
                    $icon = '<i class="fas fa-info-circle notification-icon '.$colorClass.'"></i>';
                    $title = 'Erreur';
                    $checkNotif = true;
                    $class = 'container-notification-error';
                }
                elseif($keyNotification == 'warning'){
                    $colorClass = 'notification-warning-color';
                    $icon = '<i class="fas fa-exclamation-triangle notification-icon '.$colorClass.'"></i>';
                    $checkNotif = true;
                    $title = 'Attention !';
                    $class = 'container-notification-warning';
                }
                elseif($keyNotification == 'information'){
                    $colorClass = 'notification-information-color';
                    $icon = '<i class="fas fa-info-circle notification-icon '.$colorClass.'"></i>';
                    $checkNotif = true;
                    $title = 'Information';
                    $class = 'container-notification-information';
                }

                if($checkNotif){
                    ?>
                    <div class="container-notification pattern-shadow <?= $class ?>">
                        <?= $icon ?>
                        <div class="box-alert">
                            <b class="alert-title <?= $colorClass ?>"><?= $title ?></b>
                            <p><?= $notification ?></p>
                        </div>
                        <div class="box-exit-notification <?= $colorClass ?>">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    <?php
                }

            }
        ?>

        <?php
    }
}
