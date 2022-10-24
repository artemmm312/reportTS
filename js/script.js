$(document).ready(function () {
	$('#tasksTable').DataTable({
		"language": {
			"url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json"
		}, //язык интерфейса самой таблицы
		'processing': true, //индикатор загрузки
		//'serverSide': true, //обработка на стороне сервера
		'serverMethod': 'post',
		'ajax': {
			'url': 'handler.php', //источник данных ajax для таблицы
		},
		'columns': [
			{data: 'Компания', title: 'Название компании'},
			{data: 'thisTotal', title: 'Текущая неделя'},
			{data: '-1weekTotal', title: 'Неделю назад'},
			{data: '-2weekTotal', title: 'Две недели назад'},
			{data: '-3weekTotal', title: 'Три недели назад'},
			{data: 'Дата создания', title: 'Дата создания последней задачи'},
		],
		"order": [[1, 'desc']],
		"drawCallback": function (settings) {
		},
		"initComplete": function (settings, json) {
		},
		"footerCallback": function (tfoot, data, start, end, display) {
		},
	});
});