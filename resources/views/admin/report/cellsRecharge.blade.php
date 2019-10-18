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
			@foreach($roughItems as $key => $roughItemCurrencies)
				@foreach($roughItemCurrencies as $key1 => $roughItem)
					<tr>
						<td>{{ $key }} ( {{ $key1 }} )</td>
						<td>{{ $roughItem['sumAmount'] }}  {{ $roughItem['currency']['currency'] }}</td>
						<td>{{ $roughItem['sumAccounts'] }}</td>
						<td>{{ $roughItem['sumUsers'] }}</td>
						<td>{{ $roughItem['maxAmount'] }}  {{ $roughItem['currency']['currency'] }}</td>
						<td>{{ $roughItem['maxAmountUserName']['name'] }}</td>
					</tr>
				@endforeach
			@endforeach
		@else
				<tr>
					<td></td>
					<td colspan="{{count($headers)}}"><div>查無數據</div></td>
				</tr>
		@endif
        </tbody>
    </table>

    <table id="sumlist" class="table table-bordered table-hover">
        <thead>
			<tr>
				@foreach($sumHeaders as $sumHeader)
					<th><small>{{ $sumHeader }}</small></th>
				@endforeach
			</tr>
        </thead>
        <tbody>
		@if(@isset($sumItems))
			@foreach($sumItems as $key => $sumItemCurrencies)
				@foreach($sumItemCurrencies as $key1 => $sumItem)
					<tr>
						<td>{{ $key }}  {{ $sumItem['currency'] }}</td>
						<td>{{ $sumItem['sumAmount'] }}  {{ $sumItem['currency'] }}</td>
						<td>{{ $sumItem['sumAccounts'] }}</td>
						<td>{{ $sumItem['sumUsers'] }}</td>
						<td>{{ $sumItem['sumCoins'] }}</td>
					</tr>
				@endforeach
			@endforeach
				<tr>
					<td>總和</td>
					<td>{{ $totalItems['totalAmounts'] }}  RMB</td>
					<td>{{ $totalItems['totalAccounts'] }}</td>
					<td>{{ $totalItems['totalUsers'] }}</td>
					<td>{{ $totalItems['totalCoins'] }}</td>
				</tr>
				<tr>
					<td>總和</td>
					<td>{{ $totalItems['totalAmountsNT'] }}  nt</td>

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

