var week;
$(document).ready(function () {
	createTable();
	$('.next_week').on('click', function() {
		$('#tasksTable').DataTable().destroy();
		week = {'startDate': startDate.setDate(startDate.getDate() - 7),
			'endDate': endDate.setDate(endDate.getDate() - 7)};
		createTable(week);

	})
	$('.back_week').on('click', function() {
		$('#tasksTable').DataTable().destroy();
		week = {'startDate': startDate.setDate(startDate.getDate() + 7).toString(),
			'endDate': endDate.setDate(endDate.getDate() + 7).toString()};
		//console.log(week);
		createTable(week);
		//$('#tasksTable').DataTable().ajax.reload();
	})
});

function createTable(week ='') {
	$('#tasksTable').DataTable({
		"language": {
			"url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json"
		}, //язык интерфейса самой таблицы
		'processing': true, //индикатор загрузки
		//'serverSide': true, //обработка на стороне сервера
		'serverMethod': 'post',
		'ajax': {
			'url': 'handler.php', //источник данных ajax для таблицы
			'data': { 'week': week },
			//'data': function ( d ) {week},
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
			if (settings.json !== undefined) {
				var date = settings.json.date;
				startDate = new Date(date.startDate);
				endDate = new Date(date.endDate);
			}
			$('.next_week').on('click', function() {
				week = {'startDate': startDate.setDate(startDate.getDate() - 7),
					'endDate': endDate.setDate(endDate.getDate() - 7)};
			})
			$('.back_week').on('click', function() {
				$('#tasksTable').DataTable().destroy();
				week = {'startDate': startDate.setDate(startDate.getDate() + 7).toString(),
					'endDate': endDate.setDate(endDate.getDate() + 7).toString()};
				//console.log(week);
				createTable(week);
				//$('#tasksTable').DataTable().ajax.reload();
			})
		},
		"initComplete": function (settings, json) {
		},
		"footerCallback": function (tfoot, data, start, end, display) {
		},
	});
}