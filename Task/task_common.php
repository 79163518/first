<?php
$enviroment = strtolower(getenv('RUNTIME_ENVIROMENT'));

require_once __DIR__.'/../Resource/bootstrap.php';

if (!$enviroment) {
    $enviroment = 'prod';
}

$container->setParameter('kernel.enviroment', $enviroment);

if ($container->get('kernel')->isEnviromentProd()) {
    set_error_handler('errorHandler');
    set_exception_handler('exceptionHandler');
}

function addLog($logArray, $entityManager)
{
    $blockMethods = array(
        'accountDetail',
        'couponAccountFlowList',
        'couponAccountInfo',
        'couponDetail',
        'couponExpireNotice',
        'couponFlowList',
        'couponForOrder',
        'couponList',
        'CouponTplDetail',
        'couponTplList',
        'logAlipayList',
        'moneyAccountInfo',
        'moneyFlowList',
        'receiptDetail',
        'userReceiptList',
        'userMoneyFlow',
    );

    foreach ($logArray as $log) {
        $log = json_decode($log, true);

        if (isset($log['method']) && in_array($log['method'], $blockMethods)) {
            continue;
        }

        $systemLog = new \Entity\SystemLog;

        $systemLog->setLevel($log['level']);
        $systemLog->setIp($log['ip']);
        $systemLog->setOp($log['op']);
            
        isset($log['body']) && $systemLog->setBody(json_encode($log['body']));
        isset($log['method']) && $systemLog->setMethod($log['method']);
        isset($log['params']) && $systemLog->setParams(json_encode($log['params']));
        isset($log['gmt_happen']) && $systemLog->setGmtHappen($log['gmt_happen']);
        isset($log['gmt_complete']) && $systemLog->setGmtComplete($log['gmt_complete']);
        
        $entityManager->persist($systemLog);
    }

    $entityManager->flush();
}
