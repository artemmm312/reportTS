<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>

<?php

\Bitrix\Main\Loader::includeModule('tasks');
\Bitrix\Main\Loader::includeModule('crm');

$date = date("d.m.Y H:i:s");
$rangeWeek = [];
$timestamp = strtotime($date);
$week = strftime('%u', $timestamp);
$rangeWeek['startDate'] = date('d.m.Y 00:00:00', $timestamp - ($week - 1) * 86400);
$rangeWeek['endDate'] = date('d.m.Y 23:59:59', $timestamp + (7 - $week) * 86400);

var_dump($rangeWeek['startDate']);
var_dump($rangeWeek['endDate']);
$tasksData = [];
$tasks = CTasks::GetList(
	['CREATED_DATE' => 'desc'],
	['UF_CRM_TASK' => ['CO_299'], 'GROUP_ID' => 42, '>=CREATED_DATE' => $rangeWeek['startDate'], '<=CREATED_DATE' => $rangeWeek['endDate']],
	['*', 'UF_CRM_TASK'],
	false,
	[]
);
while ($record = $tasks->Fetch()) {
	//$tasksData[] = $record;
	$company = CCrmCompany::GetList([], ['ID' => substr($record['UF_CRM_TASK'][0], 3)], ['TITLE'], false)->Fetch();
	if ($company['TITLE'] !== null) {
        $record += ['Компания' => $company['TITLE']];
		$tasksData[] = $record;
	}
}
var_dump($tasksData);

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
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/sb-1.3.4/sp-2.0.2/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="styles/style.css">

    <script type=" text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/sb-1.3.4/sp-2.0.2/datatables.min.js"></script>
</head>
<body>
<div class="container">
    <table id="tasksTable" class="table-hover table-bordered border border-dark"></table>
</div>
<script type="text/javascript" src="js/script.js"></script>
</body>
</html>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>