<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>

<?php

\Bitrix\Main\Loader::includeModule('tasks');
\Bitrix\Main\Loader::includeModule('crm');

$date = date("d.m.Y H:i:s");
$timestamp = strtotime($date);
$number_day = strftime('%u', $timestamp);
$thisWeekRange = [];
$thisWeekRange['startDate'] = date('d.m.Y 00:00:00', $timestamp - ($number_day - 1) * 86400);
$thisWeekRange['endDate'] = date('d.m.Y 23:59:59', $timestamp + (7 - $number_day) * 86400);

$week = '';
if (!empty($_POST['week'])) {
	$week = $_POST['week'];
	if ($week !== '') {
		$thisWeekRange['startDate'] = date('d.m.Y 00:00:00', $week['startDate'] / 1000);
	}
	if ($week !== '') {
		$thisWeekRange['endDate'] = date('d.m.Y 23:59:59', $week['endDate'] / 1000);
	}
}

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

function getTaskForWeek(array $tasksData, array $dateRange)
{
	$result = [];
	foreach ($tasksData as $task => $data) {
		if (date("Y.m.d H:i:s", strtotime($data['Дата создания'])) >= date("Y.m.d H:i:s", strtotime($dateRange['startDate'])) &&
			date("Y.m.d H:i:s", strtotime($data['Дата создания'])) <= date("Y.m.d H:i:s", strtotime($dateRange['endDate']))) {
			$result[] = $data;
		}
	}
	return $result;
}

$tasks_thisWeek = getTaskForWeek($tasksData, $thisWeekRange);
$tasks_minus1Week = getTaskForWeek($tasksData, $minus1WeekRange);
$tasks_minus2Week = getTaskForWeek($tasksData, $minus2WeekRange);
$tasks_minus3Week = getTaskForWeek($tasksData, $minus3WeekRange);


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

/*function multi_unique(array $array, $key)
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
}*/

$tasks_thisWeek = multi_unique_and_count($tasks_thisWeek, 'Компания');

$result = $tasks_thisWeek;

$totalCompanyMinus1Week = array_count_values(array_column($tasks_minus1Week, 'Компания'));
foreach ($totalCompanyMinus1Week as $comp => $value) {
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

$totalCompanyMinus2Week = array_count_values(array_column($tasks_minus2Week, 'Компания'));
foreach ($totalCompanyMinus2Week as $comp => $value) {
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

$totalCompanyMinus3Week = array_count_values(array_column($tasks_minus3Week, 'Компания'));
foreach ($totalCompanyMinus3Week as $comp => $value) {
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

$date = ['startDate' => date('Y-m-d H:i:s', strtotime($thisWeekRange['startDate'])),
	'endDate' => date('Y-m-d H:i:s',strtotime($thisWeekRange['endDate']))];

$response = ["aaData" => $result, "date" => $date];

echo json_encode($response);
