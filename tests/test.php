<?php
require_once __DIR__.'/../vendor/autoload.php'; // Autoload files using Composer
use Central\CentralGeneral;
echo CentralGeneral::inputField('hidden', 'test', 'test', 'test');
?>
