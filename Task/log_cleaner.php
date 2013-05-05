<?php
require_once __DIR__.'/../Task/task_common.php';
echo 'start '.date('Y-m-d H:i:s');

$limit = 100;

$lastMonthLogId = $container->get('model_fi_system_log')->getLastMonthLogId();

if ($lastMonthLogId) {
    $lastMonthLogAmount = $container->get('model_fi_system_log')->getLastMonthLogAmount();
    if ($lastMonthLogAmount > 10000) {
        $deleteAmount = 10000;
    } else {
        $deleteAmount = $lastMonthLogAmount;
    }

    for ($i=0; $i<($deleteAmount/$limit); $i++) {
        $container->get('model_fi_system_log')->cleanLog($lastMonthLogId, $limit);
    }
}
