<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>

<!-- Datatable CDN -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="./assets/js/style.js"></script>

<?php if ($page != 'Will Download') { ?>
	<script>
		$(document).ready(function() {
			$('table').DataTable({
				dom: 'Bfrtip',
				buttons: ['excel', 'pdf', 'print']
			});
		});

		$(document).ready(function() {
			let url = window.location.href;
			if (url.includes('action=Ed')) {
				$('.modal').modal('show');
			}
		});
	</script>
<?php } ?>
</body>

</html>