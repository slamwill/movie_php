<div style="width:100%; height:100%; overflow-x:scroll;">
    <table id="mainlist" class="table table-bordered table-hover">
        <thead>
			<tr>
				@foreach($headers as $header)
					<th><small>{{ $header }}</small></th>
				@endforeach
			</tr>
        </thead>
        <tbody>
		@if(@isset($roughItems))
			@foreach($roughItems as $key => $roughItem)
				<tr>
					<td>{{ $key }}</td>
					<td>{{ $roughItem['sumUsers'] }}</td>

					<td>
						<button type="button" class="btn btn-secondary btn-sm" onClick="myFunction('{{ $key }}')" value="{{ $key }}" data-toggle="modal" data-target="#myModal">詳細資料</button>
					</td>
				</tr>
			@endforeach
				<tr>
					<td>當日下載次數總和</td>
					<td>{{ $downloadTotal }}</td>
					<td>
						<button type="button" class="btn btn-secondary btn-sm" onClick="myFunction('total')" data-toggle="modal" data-target="#myModal">詳細資料</button>
					</td>
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
							<td>影片avkey</td>
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

function myFunction(date) {

	if(date == 'total')
	{
		$('.modal-title').text('下載總和細節');

		arrayF = {!! json_encode( $totalMemo ) !!};

		$("#detail tr").remove(); 

		for (var k in arrayF) {
			$('#detail').append('<tr><td><a target="_blank" rel="noopener noreferrer" href="https://www.avddav.com//watch/'+k+'">'+k+'<a></td><td>'+arrayF[k]+'</td></tr>');
		}
	}
	else
	{
		$('.modal-title').text(date);

		array = {!! json_encode( $detailItems ) !!};
		arrayF = array[date][date];

		$("#detail tr").remove(); 

		for (var k in arrayF) {
			$('#detail').append('<tr><td><a target="_blank" rel="noopener noreferrer" href="https://www.avddav.com//watch/'+k+'">'+k+'<a></td><td>'+arrayF[k]+'</td></tr>');
		}
	}

	/*
	var table = document.getElementById("detail");
	$("#detail tr").remove(); 

    for (var k in arrayF) {
		//console.log(arrayF[k]);
		//console.log(k);
		var row = table.insertRow(0);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		cell1.innerHTML = k;
		cell2.innerHTML = arrayF[k];
    }
	*/


	/*
	$.ajax({
		url: 'api/reportService',
		type: 'GET',
		data: { 'key' : key},
		success: function (response) {
			if (response.status == 0) {
				console.log(22222222);
			}
			else {
				console.log(555555);
				console.log(response);
			}
		},
		error: function(response){
			console.log(7777777);
		}
	});
	*/

}

</script>
