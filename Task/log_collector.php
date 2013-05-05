<?php
require_once __DIR__.'/../Task/task_common.php';
echo 'start '.date('Y-m-d H:i:s');

$logger = $container->get('logger');
$entityManager = $container->get('entity_manager')->getEntityManager();

$i = 0;
$logArray = array();

do {
	if ($i < 100) {
		$result = $logger->popLog();

		if (false == $result && !empty($logArray)) {
		    addLog($logArray, $entityManager);
            exit;
		}

        $logArray[] = $result;
        $i++;
	} else {
        $i = 0;
        addLog($logArray, $entityManager);
    }
} while ($result != false);

echo '. end '.date('Y-m-d H:i:s')."\n";
