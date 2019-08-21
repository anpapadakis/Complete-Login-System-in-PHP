<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="polyfill/html5-simple-date-input-polyfill.min.js"></script>
<script>
$("#registerBtn").click(function(){
	if ($("#resetPassword").hasClass("show")) {
		$("#resetBtn").click();
	}
});

$("#resetBtn").click(function(){
	if ($("#register").hasClass("show")) {
		$("#registerBtn").click();
	}
});

// Show/Hide password
$('.show-pass').click(function() {
	var label = $(this);
	var pass_field = $(this).siblings('input');

	if ($(pass_field).attr('type') === "password") {
		$(pass_field).attr('type','text');
		$(label).text('Hide password');
	} else {
		$(pass_field).attr('type','password');
		$(label).text('Show password');
	}
});

$('#updatePhoto').change(function() {
	if ($(this).get(0).files.length > 0) {
		$('#photoUploaded').show();
	} else {
		$('#photoUploaded').hide();
	}
});


(function() {
	'use strict';
	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}
				form.classList.add('was-validated');
			}, false);
		});
	}, false);
})();
</script>
</body>
</html>
