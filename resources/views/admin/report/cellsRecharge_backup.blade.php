<div style="width:100%; height:100%; overflow-x:scroll;">
    <table id="mainlist" class="table table-bordered table-hover">
        <thead>
			<tr>
				<th></th>
				@foreach($headers as $header)
					<th><small>{{ $header }}</small></th>
				@endforeach
			</tr>
        </thead>
        <tbody>

		@if($contentItems)
			@foreach($contentItems as $contentItem)
				<tr>
					<td>{{ floor( $contentItem['amount'] ) . $contentItem['currency'] }}</td>
					<td>{{ floor( $contentItem['groupAccount'] ) }}</td>
					<td>{{ floor( $contentItem['groupUsers'] ) }}</td>
					<td>{{ floor( $contentItem['groupAmount'] ) }}</td>
					<td>{{ floor( $contentItem['groupCoins'] ) }}</td>
				</tr>
			@endforeach
				<tr>
					<td>總和</td>
				@foreach($totalSumItems as $totalSumItem)
					<td>{{ $totalSumByAllUsers }}</td>
					<td>{{ $totalSumByAllAccounts }}</td>
					<td>{{ floor( $totalSumItem['totalAmount'] ) }}</td>
					<td>{{ floor( $totalSumItem['totalCoins'] ) }}</td>
				@endforeach
				</tr>
		@else
				<tr>
					<td></td>
					<td colspan="{{count($headers)}}"><div>查無數據</div></td>
				</tr>
		@endif

        </tbody>
    </table>
</div>

<script>
  $(function () {
    //Date range picker with time picker
    $('#activity_date').daterangepicker({
      timePicker: false,
      timePickerIncrement: 5,
      format: 'YYYY/MM/DD'
    });
  });
</script>