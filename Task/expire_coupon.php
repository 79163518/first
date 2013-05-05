<?php
require_once __DIR__.'/../Task/task_common.php';
echo 'start '.date('Y-m-d H:i:s');

$backend = new \Service\Finance\Backend;
$backend->setContainer($container)
    ->setEntityManager();

$now = time();

$result = $backend->expireCoupon($now);
echo ' num:'.json_encode($result);
echo ' end '.date('Y-m-d H:i:s')."\n";

