<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>

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
	<table id="tasksTable" class="table-hover table-bordered border border-dark">
		<thead>
		<tr>
			<th class="text-center">Название компании</th>
			<th class="this_week text-center">Текущая неделя</th>
			<th class="text-center">Неделю назад</th>
			<th class="text-center">Две недели назад</th>
			<th class="text-center">Три недели назад</th>
			<th class="text-center">Дата создания последней задачи</th>
		</tr>
		</thead>
	</table>
</div>
<div class="container">
	<div class="buttons">
		<button class="back_week btn btn-primary" type="button">
			Неделю назад
		</button>
		<button class="next_week btn btn-primary" type="button">
			Неделю вперед
		</button>
	</div>
</div>
<script type="text/javascript" src="js/script.js?v=0.0.5"></script>
</body>
</html>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
