<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-body ">
                @if(session('status'))
                    <div class="alert alert-warning">
                        {{session('status')}}
                    </div>
                @endif
                <form action="{{ URL::current() }}" method="get" pjax-container="" accept-charset="UTF-8">
                    {{--  {{ csrf_field() }}  --}}
                    <div class="form-group">
                        <label>重算報表：</label>
                        <select name="report_type" style="width:200px">
                            <option value="">請選擇</option>
                            <option value="ReportCategoryMonth" @if( !empty($report_type) && $report_type == 'ReportCategoryMonth') selected @endif>每日點擊數最高分類</option>
                            <option value="ReportWatchMonth" @if( !empty($report_type) && $report_type == 'ReportWatchMonth') selected @endif>每日點擊數最高影片</option>
                            <option value="ReportTagMonth" @if( !empty($report_type) && $report_type == 'ReportTagMonth') selected @endif>每日點擊數最高標籤</option>
                            <option value="ReportMonth" @if( !empty($report_type) && $report_type == 'ReportMonth') selected @endif>月報表</option>
                        </select>
                    </div>
                    <div class="form-inline">
                        <label>重算區間：</label>
                        <div class="input-group row" style="width: 370px;" >
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="start_date" value="{{ $start_date}}" class="form-control date_start" style="width: 150px">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="end_date" value="{{ $end_date }}" class="form-control date_end" style="width: 150px">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-default "  type="submit" style="margin-left:25px;">執行</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    $('.date_start').datetimepicker({"format":"YYYY/MM/DD","locale":"en"});
    $('.date_end').datetimepicker({"format":"YYYY/MM/DD","locale":"en","useCurrent":false});
    $(".date_start").on("dp.change", function (e) {
        $('.date_end').data("DateTimePicker").minDate(e.date);
    });
    $(".date_end").on("dp.change", function (e) {
        $('.date_start').data("DateTimePicker").maxDate(e.date);
    });

	$('select').select2();

	@if( !empty($message) )        
		toastr.success('{{ $message }}');
	@endif

});
</script>