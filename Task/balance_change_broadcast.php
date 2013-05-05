<?php
require_once __DIR__.'/../Task/task_common.php';
echo 'start '.date('Y-m-d H:i:s');

$now = strtotime(date('Y-m-d H:i:00'));

$moneyFlows = $container->get('model_fi_money_flow')->getOneMinuteFlow($now);
$accountModel = $container->get('model_fi_money_account');
$topic = $container->get('kernel')->getConfig('account_balance_changes_topic');

foreach ($moneyFlows as $flow) {
    $account = $accountModel->getMoneyAccount($flow['ali_uid']);

    is_null($account['balance_minimum']) && $account['balance_minimum'] = 0;

    $content = array(
        'ali_uid'         => $flow['ali_uid'],
        'gmt_happen'      => $flow['gmt_trade'],
        'event_type'      => \Entity\Finance\MoneyFlow::typeEnglish($flow['type']),
        'account_type'    => 'money_account',
        'balance_change'  => round($flow['amount'], 2),
        'balance'         => round($flow['balance'], 2),
        'reminder_switch' => \Entity\Finance\MoneyAccount::remindeRswitchEnglish($account['balance_reminder_switch']),
        'balance_minimum' => round($account['balance_minimum'], 2),
    );

    $container->get('metaq')->sendAccountBalanceChangesNotice($content);
}

$couponAccountFlows = $container->get('model_fi_coupon_account_flow')->getOneMinuteFlow($now);

foreach ($couponAccountFlows as $flow) {
    $content = array(
        'ali_uid'         => $flow['ali_uid'],
        'gmt_happen'      => $flow['gmt_trade'],
        'event_type'      => \Entity\Finance\CouponAccountFlow::typeEnglish($flow['type']),
        'account_type'    => 'coupon_account',
        'balance_change'  => round($flow['amount'], 2),
        'balance'         => round($flow['balance'], 2),
        'reminder_switch' => 'off', 
        'balance_minimum' => 0,
    );

    $container->get('metaq')->sendAccountBalanceChangesNotice($content);
}

$num = count($moneyFlows) + count($couponAccountFlows);

echo ' end '.date('Y-m-d H:i:s').' num: '.$num."\n";

