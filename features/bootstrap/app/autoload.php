<?php
umask(0000);
$loader = require(__DIR__.'/../../../vendor/autoload.php');
$loader->addPsr4('Rezzza\\Bundle\\', __DIR__.'/../bundle/');
