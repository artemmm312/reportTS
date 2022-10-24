<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>

<?php

\Bitrix\Main\Loader::includeModule('tasks');
\Bitrix\Main\Loader::includeModule('crm');

$date = date("d.m.Y H:i:s");
$timestamp = strtotime($date);
$number_day = strftime('%u', $timestamp);
$thisWeekRange = [];
$thisWeekRange['startDate'] = date('d.m.Y 00:00:00', $timestamp - ($number_day - 1) * 86400);
$thisWeekRange['endDate'] = date('d.m.Y 23:59:59', $timestamp + (7 - $number_day) * 86400);

$minus1WeekRange = [];
$minus1WeekRange['startDate'] = date('d.m.Y 00:00:00', strtotime($thisWeekRange['startDate'] . '-1 week'));
$minus1WeekRange['endDate'] = date('d.m.Y 23:59:59', strtotime($thisWeekRange['endDate'] . '-1 week'));

$minus2WeekRange = [];
$minus2WeekRange['startDate'] = date('d.m.Y 00:00:00', strtotime($thisWeekRange['startDate'] . '-2 week'));
$minus2WeekRange['endDate'] = date('d.m.Y 23:59:59', strtotime($thisWeekRange['endDate'] . '-2 week'));

$minus3WeekRange = [];
$minus3WeekRange['startDate'] = date('d.m.Y 00:00:00', strtotime($thisWeekRange['startDate'] . '-3 week'));
$minus3WeekRange['endDate'] = date('d.m.Y 23:59:59', strtotime($thisWeekRange['endDate'] . '-3 week'));


$tasks = CTasks::GetList(
	['CREATED_DATE' => 'desc'],
	['GROUP_ID' => 42, '>=CREATED_DATE' => $minus3WeekRange['startDate'], '<=CREATED_DATE' => $thisWeekRange['endDate']],
	['*', 'UF_CRM_TASK'],
	false,
	[]
);
$tasksData = [];
while ($record = $tasks->Fetch()) {
	if (strpos($record['UF_CRM_TASK'][0], 'CO_') !== false) {
		$company = CCrmCompany::GetList([], ['ID' => substr($record['UF_CRM_TASK'][0], 3)], ['TITLE'], false)->Fetch();
		$record += ['Компания' => $company['TITLE']];
		$tasksData[] = [
			'ID' => $record['ID'],
			'Компания' => $record['Компания'],
			'Дата создания' => $record['CREATED_DATE']];
	}
}

function getTaskForWeek (array $tasksData, array $dateRange) {
    $result = [];
	foreach ($tasksData as $task => $data) {
		if (date("Y.m.d H:i:s",strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s",strtotime($dateRange['startDate'])) &&
			date("Y.m.d H:i:s",strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s",strtotime($dateRange['endDate']))) {
            $result[] = $data;
		}
	}
    return $result;
}


$tasks_thisWeek = [];
foreach ($tasksData as $task => $data) {
	if (date("Y.m.d H:i:s",strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s",strtotime($thisWeekRange['startDate'])) &&
      date("Y.m.d H:i:s",strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s",strtotime($thisWeekRange['endDate']))) {
		$tasks_thisWeek[] = $data;
	}
}

$tasks_minus1Week = [];
foreach ($tasksData as $task => $data) {
	if (date("Y.m.d H:i:s",strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s",strtotime($minus1WeekRange['startDate'])) &&
      date("Y.m.d H:i:s",strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s",strtotime($minus1WeekRange['endDate']))) {
		$tasks_minus1Week[] = $data;
	}
}

$tasks_minus2Week = [];
foreach ($tasksData as $task => $data) {
	if (date("Y.m.d H:i:s",strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s",strtotime($minus2WeekRange['startDate'])) &&
      date("Y.m.d H:i:s",strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s",strtotime($minus2WeekRange['endDate']))) {
		$tasks_minus2Week[] = $data;
	}
}

$tasks_minus3Week = [];
foreach ($tasksData as $task => $data) {
	if (date("Y.m.d H:i:s",strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s",strtotime($minus3WeekRange['startDate'])) &&
			date("Y.m.d H:i:s",strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s",strtotime($minus3WeekRange['endDate']))) {
		$tasks_minus3Week[] = $data;
	}
}
//var_dump($tasks_minus3Week);

function multi_unique_and_count(array $array, $key)
{
	$count_for_key = array_count_values(array_column($array, $key));
	$temp_array = array();
	$key_array = array();
	$i = 0;
	foreach ($array as $arr) {
		if (!in_array($arr[$key], $key_array, true)) {
			$key_array[$i] = $arr[$key];
			$arr += ['thisTotal' => $count_for_key[$arr[$key]], '-1weekTotal' => 0, '-2weekTotal' => 0, '-3weekTotal' => 0];
			$temp_array[$i] = $arr;
		}
		$i++;
	}
	return array_values($temp_array);
}

function multi_unique(array $array, $key)
{
	$temp_array = array();
	$key_array = array();
	$i = 0;
	foreach ($array as $arr) {
		if (!in_array($arr[$key], $key_array, true)) {
			$key_array[$i] = $arr[$key];
			$temp_array[$i] = $arr;
		}
		$i++;
	}
	return array_values($temp_array);
}

$tasks_thisWeek = multi_unique_and_count($tasks_thisWeek, 'Компания');
//var_dump($tasks_thisWeek);
/*$tasks_minus1Week = multi_unique($tasks_minus1Week, 'Компания');
$tasks_minus2Week = multi_unique($tasks_minus2Week, 'Компания');
$tasks_minus3Week = multi_unique($tasks_minus3Week, 'Компания');*/

$result = $tasks_thisWeek;
//var_dump($result);


$count_for_key1 = array_count_values(array_column($tasks_minus1Week, 'Компания'));
foreach ($count_for_key1 as $comp => $value) {
	if (in_array($comp, array_column($result, 'Компания'), true)) {
		$index = array_search($comp, array_column($result, 'Компания'), true);
		$result[$index]['-1weekTotal'] = $value;
	} else {
		$j = array_search($comp, array_column($tasks_minus1Week, 'Компания'), true);
		$result[] = ['ID' => $tasks_minus1Week[$j]['ID'],
			'Компания' => $tasks_minus1Week[$j]['Компания'],
			'Дата создания' => $tasks_minus1Week[$j]['Дата создания'],
			'thisTotal' => 0,
			'-1weekTotal' => $value,
			'-2weekTotal' => 0,
			'-3weekTotal' => 0];
	}
}
$count_for_key2 = array_count_values(array_column($tasks_minus2Week, 'Компания'));
foreach ($count_for_key2 as $comp => $value) {
	if (in_array($comp, array_column($result, 'Компания'), true)) {
		$index = array_search($comp, array_column($result, 'Компания'), true);
		$result[$index]['-2weekTotal'] = $value;
	} else {
		$j = array_search($comp, array_column($tasks_minus2Week, 'Компания'), true);
		$result[] = ['ID' => $tasks_minus2Week[$j]['ID'],
			'Компания' => $tasks_minus2Week[$j]['Компания'],
			'Дата создания' => $tasks_minus2Week[$j]['Дата создания'],
			'thisTotal' => 0,
			'-1weekTotal' => 0,
			'-2weekTotal' => $value,
			'-3weekTotal' => 0];
	}
}
$count_for_key3 = array_count_values(array_column($tasks_minus3Week, 'Компания'));
foreach ($count_for_key3 as $comp => $value) {
	if (in_array($comp, array_column($result, 'Компания'), true)) {
		$index = array_search($comp, array_column($result, 'Компания'), true);
		$result[$index]['-3weekTotal'] = $value;
	} else {
		$j = array_search($comp, array_column($tasks_minus3Week, 'Компания'), true);
		$result[] = ['ID' => $tasks_minus3Week[$j]['ID'],
			'Компания' => $tasks_minus3Week[$j]['Компания'],
			'Дата создания' => $tasks_minus3Week[$j]['Дата создания'],
			'thisTotal' => 0,
			'-1weekTotal' => 0,
			'-2weekTotal' => 0,
			'-3weekTotal' => $value];
	}
}

//var_dump($result);

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