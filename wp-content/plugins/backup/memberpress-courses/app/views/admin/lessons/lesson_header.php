<?php
use memberpress\courses as base;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

$lesson_id = get_the_ID();
$lesson = new models\Lesson($lesson_id);
$course = $lesson->course();
?>

<div id="mpcs-admin-header-wrapper"></div>
