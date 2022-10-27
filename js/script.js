const week = {startDate: '', endDate: ''};
$(document).ready(function () {
	createTable();
	console.log(week.startDate);
	console.log(week.endDate);
	console.log(week);

	$('.back_week').on('click', function () {
		week.startDate = week.startDate.setDate(week.startDate.getDate() - 7);
		week.endDate = week.endDate.setDate(week.endDate.getDate() - 7);
		$('#tasksTable').DataTable().destroy();
		createTable();
	});
	$('.next_week').on('click', function () {
		week.startDate = week.startDate.setDate(week.startDate.getDate() + 7);
		week.endDate = week.endDate.setDate(week.endDate.getDate() + 7);
		$('#tasksTable').DataTable().destroy();
		createTable();
	});
});

function createTable() {
	//console.log(week);
	$('#tasksTable').DataTable({
		"language": {
			"url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json"
		}, //язык интерфейса самой таблицы
		'processing': true, //индикатор загрузки
		//'serverSide': true, //обработка на стороне сервера
		//'serverMethod': 'post',
		'ajax': {
			'url': 'handler.php', //источник данных ajax для таблицы
			'type': 'POST',
			'data': {'week': week},
			//'data': function(d) {d.week = week},
		},
		'columns': [
			{data: 'Компания', className: 'text-center'},
			{data: 'thisTotal', className: 'text-center'},
			{data: '-1weekTotal', className: 'text-center'},
			{data: '-2weekTotal', className: 'text-center'},
			{data: '-3weekTotal', className: 'text-center'},
			{data: 'Дата создания', className: 'text-center'},
		],
		"order": [[1, 'desc']],
		"drawCallback": function (settings) {
		},
		"initComplete": function (settings, json) {
			let date = json.date;
			let startDate = new Date(date.startDate);
			let endDate = new Date(date.endDate);
			week.startDate = startDate;
			week.endDate = endDate;
			console.log(week);
			$('.this_week').html("Текущая неделя " +
				"<p style='font-size: 10px; font-style: italic; margin: 0'>" +
				`(${startDate.toLocaleDateString()} - ${endDate.toLocaleDateString()})` +
				"</p>");
		},
		"footerCallback": function (tfoot, data, start, end, display) {
		},
	});
}