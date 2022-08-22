<?php
$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Hello <?= $user->firstname ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>