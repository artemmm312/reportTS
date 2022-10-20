<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>

<?php

\Bitrix\Main\Loader::includeModule('tasks');
\Bitrix\Main\Loader::includeModule('crm');

//$tasks = CTasks::GetList([], [], [], ['nPageTop' => 10], []);

$date = date("d.m.Y H:i:s");
$rangeWeek = [];
$timestamp = strtotime($date);
$week = strftime('%u', $timestamp);
$rangeWeek['startDate'] = date('d.m.Y 00:00:00', $timestamp - ($week - 1) * 86400);
$rangeWeek['endDate'] = date('d.m.Y 23:59:59', $timestamp + (7 - $week) * 86400);


$tasksData = [];
$tasks = CTasks::GetList(
	['CREATED_DATE' => 'desc'],
	['GROUP_ID' => 42, '>=CREATED_DATE' => $rangeWeek['startDate'], '<=CREATED_DATE' => $rangeWeek['endDate']],
	['ID', 'TITLE', 'CREATED_DATE', 'UF_CRM_TASK'],
	false,
	[]
);
while ($record = $tasks->Fetch()) {
	$company = CCrmCompany::GetList([], ['ID' => substr($record['UF_CRM_TASK'][0], 3)], ['TITLE'], false)->Fetch();
	if ($company['TITLE'] !== null) {
		$tasksData[] = [
			//'ID' => $record['ID'],
			'Компания' => $company['TITLE'],
			'Дата создания' => $record['CREATED_DATE'],
		];
	}
}


function multi_unique_and_count(array $array, $key) {
	$count_for_key = array_count_values(array_column($array, $key));
	$temp_array = array();
	$key_array = array();
	$i = 0;
	foreach($array as $arr) {
		if (!in_array($arr[$key], $key_array, true)) {
			$key_array[$i] = $arr[$key];
			$arr += ['count' => $count_for_key[$arr[$key]]];
			$temp_array[$i] = $arr;
		}
		$i++;
	}
	return array_values($temp_array);
}


$lastTasks = multi_unique_and_count($tasksData, 'Компания');
//var_dump($lastTasks);

$response = ["aaData" => $lastTasks];

echo json_encode($response);
