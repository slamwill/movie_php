<script type="text/javascript">
@if(session()->has('message'))
	app.success('{{ session()->get('message') }}');
@endif

@if ($errors->any())
	app.error('{{ $errors->first() }}');
@endif
</script>
