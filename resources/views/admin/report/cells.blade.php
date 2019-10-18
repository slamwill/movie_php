<div style="width:100%; height:100%; overflow-x:scroll;">
    <table id="mainlist" class="table table-bordered table-hover">
        <thead>
        <tr>
            @foreach($headers as $header)
            <th><small>{{$header}}</small></th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                @foreach($row as $key => $cell)
                    <td>
                        @if(is_array($cell))
                            <div><a href="{{$cell[2]}}" target="_blank">{{$cell[0]}}</a></div>
                            <div>Views：{{$cell[1]}}</div>
                        @else
                            {{$cell}}
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td></td>
                <td colspan="{{count($headers)}}"><div>查無數據</div></td>
            </tr>
        @endforelse
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