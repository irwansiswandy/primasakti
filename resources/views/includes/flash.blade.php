@if (Session::has('flash_message'))
	<script>
	    swal({
	      	type: "{{ Session::get('flash_message.type') }}",
	      	title: "{{ Session::get('flash_message.title') }}",
	      	text: "{{ Session::get('flash_message.message') }}",
	      	timer: 1800,
	      	showConfirmButton: false
	    });
	</script>
@elseif (Session::has('flash_message_with_confirm_button'))
	<script>
		swal({
			type: "{{ Session::get('flash_message_with_confirm_button.type') }}",
	      	title: "{{ Session::get('flash_message_with_confirm_button.title') }}",
	      	text: "{{ Session::get('flash_message_with_confirm_button.message') }}"
		});
	</script>
@endif