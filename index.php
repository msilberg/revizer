<?php
/**
 * Created by PhpStorm.
 * User: mike
 */

require_once 'core/data.php';

$_data = new PerformData($argv);
$_data->do_action();