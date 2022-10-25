$(document).ready(function () {
	createTable();

	/*let table = $('#tasksTable').DataTable();
	let date;
	table.on('init', function () {
		date = table.ajax.json().date;
		console.log(date);
	})
	let week;


	$('.next_week').on('click', function () {
		let startDate = new Date(date.startDate);
		let endDate = new Date(date.endDate);
		week = {
			'startDate': startDate.setDate(startDate.getDate() - 7),
			'endDate': endDate.setDate(endDate.getDate() - 7)
		};
		console.log(week);
		//table.ajax.reload();
		table.destroy();
		createTable(week);
	});

	$('.back_week').on('click', function () {
		let startDate = new Date(date.startDate);
		let endDate = new Date(date.endDate);
		week = {
			'startDate': startDate.setDate(startDate.getDate() + 7),
			'endDate': endDate.setDate(endDate.getDate() + 7)
		};
		console.log(week);
		table.destroy();
		createTable(week);
	});*/
	$('.next_week').on('click', function () {
		$.ajax({
			type: 'POST',
			url: 'handler.php',
			success: function (result) {
				let date = JSON.parse(result).date;
				console.log(date);
				let startDate = new Date(date.startDate);
				let endDate = new Date(date.endDate);
				let week = {
					'startDate': startDate.setDate(startDate.getDate() - 7),
					'endDate': endDate.setDate(endDate.getDate() - 7)
				};
				$('#tasksTable').DataTable().destroy();
				createTable(week);
			},
		});
	});
});

function createTable(week) {
	console.log(week);
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
			{data: 'Компания', title: 'Название компании'},
			{data: 'thisTotal', title: 'Текущая неделя'},
			{data: '-1weekTotal', title: 'Неделю назад'},
			{data: '-2weekTotal', title: 'Две недели назад'},
			{data: '-3weekTotal', title: 'Три недели назад'},
			{data: 'Дата создания', title: 'Дата создания последней задачи'},
		],
		"order": [[1, 'desc']],
		"drawCallback": function (settings) {
			/*if (settings.json !== undefined) {
				let date = settings.json.date;
				let startDate = new Date(date.startDate);
				let endDate = new Date(date.endDate);

				$('.next_week').one('click', function () {
					let week = {
						'startDate': startDate.setDate(startDate.getDate() - 7),
						'endDate': endDate.setDate(endDate.getDate() - 7)
					};
					$('#tasksTable').DataTable().destroy();
					createTable(week);
				});
				$('.back_week').one('click', function () {
					let week = {
						'startDate': startDate.setDate(startDate.getDate() + 7),
						'endDate': endDate.setDate(endDate.getDate() + 7)
					};
					console.log(week);
					$('#tasksTable').DataTable().destroy();
					createTable(week);
				});
			}*/
		},
		"initComplete": function (settings, json) {
			/*				let date = json.date;
							let startDate = new Date(date.startDate);
							let endDate = new Date(date.endDate);
						$('.next_week').one('click', function () {
							let week = {
								'startDate': startDate.setDate(startDate.getDate() - 7),
								'endDate': endDate.setDate(endDate.getDate() - 7)
							};
							$('#tasksTable').ajax.reload();
							//$('#tasksTable').DataTable().destroy();
							createTable(week);
							//$('#tasksTable').DataTable().clear().draw();
						});
						$('.back_week').one('click', function () {
							let week = {
								'startDate': startDate.setDate(startDate.getDate() + 7),
								'endDate': endDate.setDate(endDate.getDate() + 7)
							};
							console.log(week);
							//$('#tasksTable').DataTable().destroy();
							createTable(week);
							//$('#tasksTable').DataTable().clear().draw();
						});*/
		},
		"footerCallback": function (tfoot, data, start, end, display) {
		},
	});
}