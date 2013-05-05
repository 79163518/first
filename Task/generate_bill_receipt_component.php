<?php
require_once __DIR__.'/../Task/task_common.php';
echo 'start '.date('Y-m-d H:i:s');

$db_manager = $container->get('db_manager');
$db = $db_manager->getDb();


$m = date('Y-m-d');
$first_m = date("Y-m-01", mktime(0,0,0,date("m",strtotime($m))-1,date("d",strtotime($m)),date("Y",strtotime($m))) ); 
$last_m = date("Y-m-t", strtotime($first_m));
  								
$quantity = $db->fetchColumn("SELECT count(*) as count FROM (SELECT ali_uid, SUM(amount) FROM fi_money_flow WHERE type =:type AND source_type =:source_type 
						 AND gmt_trade>=:starttime AND gmt_trade<=:endtime GROUP BY ali_uid HAVING SUM(amount) <0 ) t ", array(
    	'type' => \Entity\Finance\MoneyFlow::TYPE_CONSUME,
		'source_type' => \Entity\Finance\MoneyFlow::SOURCE_TYPE_BILL,
		'starttime' => strtotime($first_m),
		'endtime' => strtotime($last_m.' 23:59:59') 
		));

echo ' total:'.$quantity;

for ($i = 0; $i < ($quantity/1000); $i++) {
    $flows = $db->fetchAll("SELECT ali_uid,sum(amount)as amount FROM fi_money_flow WHERE type =:type AND source_type =:source_type 
						 AND gmt_trade>=:starttime AND gmt_trade<=:endtime GROUP BY ali_uid HAVING SUM(amount) <0 ORDER BY ali_uid ASC LIMIT 1000 OFFSET ".$i*1000, array(
        'type' => \Entity\Finance\MoneyFlow::TYPE_CONSUME,
    	'source_type' => \Entity\Finance\MoneyFlow::SOURCE_TYPE_BILL,
		'starttime' => strtotime($first_m),
		'endtime' => strtotime($last_m.' 23:59:59') 
    ));
		
	foreach ($flows as $flow) {
		    
		    $db->insert('fi_receipt_component', array(
		            'ali_uid' => $flow['ali_uid'],
		            'amount' => -1*$flow['amount'],
		     		'gmt_trade' =>  time(),
		     		'type' => \Entity\Finance\ReceiptComponent::TYPE_BILL,
		    		'status' => \Entity\Finance\ReceiptComponent::STATUS_AVAILABLE,
		    		'source_identify' => date('YmdHis'),
		    		'gmt_create' => date('Y-m-d H:i:s'),
		    		'gmt_update' => date('Y-m-d H:i:s')
		   	 ));
	   }
}

echo ' end '.date('Y-m-d H:i:s')."\n";
