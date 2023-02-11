<?php

require_once('../../../index.php');
$controller = new Controller($connection);
$controller->auth_user();