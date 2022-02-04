<?php
use memberpress\courses as base;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

$quiz_id = get_the_ID();
$lesson = new models\Quiz($quiz_id);
$course = $lesson->course();
?>

<div id="mpcs-admin-header-wrapper"></div>
