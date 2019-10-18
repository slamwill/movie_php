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
		@if(@isset($sumByItems))
			@foreach($sumByItems as $sumByItem)
				<tr>
					<td>{{ $sumByItem['memo'] }} ( {{ $sumByItem['coins'] }} )</td>
					<td>{{ floor( $sumByItem['totalAccount'] ) }}</td>
					<td><a href='javascript:void(0)'  data-toggle="modal" data-target="#myModal" OnClick="myDetail('{{ $sumByItem['coins'] }}','{{ $sumByItem['memo'] }}')" title='詳細資料'>  {{ floor( $sumByItem['totalUsers'] ) }}</a></td>
				</tr>
			@endforeach
				<tr>
					<td>總和</td>
					<td>{{ $totalSumByAllAccounts }}</td>
					<td><a href='javascript:void(0)'  data-toggle="modal" data-target="#myModal" OnClick="myDetail('total')" title='詳細資料'>{{ $totalSumByAllUsers }}</a></td>
				</tr>

		@else
				<tr>
					<td></td>
					<td colspan="{{count($headers)}}"><div>查無數據</div></td>
				</tr>
		@endif

        </tbody>
    </table>

    <table id="downloadlist" class="table table-bordered table-hover">
        <thead>
			<tr>
				<th></th>
				@foreach($headers as $header)
					<th><small>{{ $header }}</small></th>
				@endforeach
			</tr>
        </thead>
        <tbody>
		@if(@isset($totalDownloadsByAllUsers))
			<tr>
				<td>下載</td>
				<td>{{ $totalDownloadsByAllUsers }}</td>
				<td>{{ $totalDownloadsByAllAccounts }}</td>
			</tr>
			<tr>
				<td>下載總點數</td>
				<td>{{ $totalDownloadsAmounts }}</td>
				<td>{{ $totalDownloadsAmounts }}</td>
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


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" value="">Modal Header</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-hover">
					<tbody>
						<tr>
							<td>使用者帳號</td>
							<td>下載次數</td>
						</tr>
					</tbody>
					<tbody id="detail">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>

function myDetail(coin,memo) {

	//alert(memo);
	
	if(coin == 'total')
	{
		$('.modal-title').text('使用者下載總和細節');

		arrayF = {!! json_encode( $roughItems['totalMemos'] ) !!};

		$("#detail tr").remove(); 

		for (var k in arrayF) {
			$('#detail').append('<tr><td>'+k+'</td><td>'+arrayF[k]+'</td></tr>');
		}
	}
	else
	{
		$('.modal-title').text(memo);
		array = {!! json_encode( $roughItems ) !!};
		//console.log(array);
		arrayF = array[coin][coin];
		//console.log(arrayF);
		//console.log(arrayF);
		//console.log(typeof(arrayF));
		$("#detail tr").remove();

		for (var k in arrayF) {
			$('#detail').append('<tr><td>'+k+'</td><td>'+arrayF[k]+'</td></tr>');
		}
	}
	



}






</script>