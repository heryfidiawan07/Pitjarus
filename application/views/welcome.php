<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="<?= base_url('assets/bootstrap-5.2.0/css/bootstrap.min.css') ?>">
		<link rel="stylesheet" href="<?= base_url('node_modules/select2/dist/css/select2.min.css') ?>">

		<title>Pitjarus Test</title>
	</head>
	<body>
		<div class="container py-5">
			<form action="" id="form" class="mb-5 d-flex justify-content-center">
				<div class="row">
					<div class="col-md-3">
						<label for="area" class="form-label">Area</label>
						<select name="area[]" id="area" class="form-control js-example-basic-multiple" multiple="multiple">
							<option>Select Area</option>
							<?php foreach($area as $row): ?>
								<option value="<?= $row->area_id ?>"><?= $row->area_name ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-3">
						<label for="from" class="form-label">Date From</label>
						<input type="date" id="from" class="form-control">
					</div>
					<div class="col-md-3">
						<label for="to" class="form-label">Date To</label>
						<input type="date" id="to" class="form-control">
					</div>
					<div class="col-md-3 pt-2">
						<button type="submit" class="btn btn-primary mt-4">View</button>
					</div>
				</div>
			</form>
			<div class="row d-flex justify-content-center">
				<div class="card p-2">
					<canvas id="chart-canvas"></canvas>
				</div>
			</div>
			<div class="row mt-5 d-flex justify-content-center">
				<div class="card p-2" id="table-card">
					<div class="table-responsive scrollbar-custom">
						<table id="datatable" class="table table-hover small">
						</table>
					</div>
				</div>
			</div>
		</div>

	<script src="<?= base_url('assets/js/jquery-3.6.1.min.js') ?>"></script>
	<script src="<?= base_url('assets/bootstrap-5.2.0/js/bootstrap.min.js') ?>"></script>
	<script src="<?= base_url('node_modules/select2/dist/js/select2.min.js') ?>"></script>

	<!-- Chart JS -->
	<script src="<?= base_url('node_modules/chart.js/dist/chart.min.js') ?>"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

	<script>
		const base_url = "<?= base_url() ?>"

		$(document).ready(function() {
			$('.js-example-basic-multiple').select2()
		})

		// Event
		$(document).on('change', '#from', function(e) {
			// <input type="date" onkeydown="return false" />
			$('#to').attr({min: $('#from').val()})
		})

		$(document).on('submit', '#form', function(e) {
			e.preventDefault()
			getChartData()
			getTableData()
		})

		function getChartData() {
			$.ajax({
				url: base_url+'welcome/chart',
				type: 'POST',
				data: {
					area: $('#area').val(),
					from: $('#from').val(),
					to: $('#to').val(),
				},
				dataType: 'json',
				success: async function(res) {
					pitjarusChart.data = res
					pitjarusChart.update()
				}
			})
		}

		function getTableData() {
			$.ajax({
				url: base_url+'welcome/datatables',
				type: 'POST',
				data: {
					area: $('#area').val(),
					from: $('#from').val(),
					to: $('#to').val(),
				},
				dataType: 'json',
				success: async function(res) {
					$('#datatable').html('')
					
					let thead = '<thead><tr><th>Brand</th>'
					res.header.map((val) => {
						thead += '<th>'+val+'</th>'
					})
					thead += '</tr></thead>'
						
					let tbody = '<tbody>'
					$.each(res.data, async (index, value) => {
						tbody += '<tr>'
						tbody += '<td>'+index+'</td>'
							res.header.map((head) => {
								tbody += '<td>'+value[head]+' %</td>'
							})
						tbody += '</tr>'
					})
					tbody += '</tbody>'

					$('#datatable').html(thead+tbody)
				}
			})
		}

		// Chart JS
		Chart.register(ChartDataLabels)
		const pitjarusChart = new Chart(document.getElementById('chart-canvas'), {
			type: 'bar',
			data: {},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				animation: {
					duration: 3000
				},
				layout: {
					padding: {
						top: 25
					}
				},
				plugins: {
					legend: {
						labels: {
							usePointStyle: true,
						},
						position: 'bottom'
					},
					datalabels: {
						color: 'black',
						anchor: 'end',
						align: 'top'
					}
				},
				scales: {
					y: {
						display: true,
						title: {
							display: true,
							text: 'Percentage (%)',
						}
					}
				},
			}
		})
	</script>
	</body>
</html>
