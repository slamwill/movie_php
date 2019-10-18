
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="box box-primary">

            <div class="box-body ">
			
                <form class="form-inline" action="{{ URL::current() }}" method="get" pjax-container="" accept-charset="UTF-8">
                    {{--  {{ csrf_field() }}  --}}
                    <div class="form-group">
                            <label>報表區間：</label>
                    
                        <div class="input-group " style="width: 370px;">
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
                    
                    </div>
                    <button class="btn btn-default "  type="submit" style="margin-left:25px;"><i class="fa fa-search"></i>&nbsp;&nbsp;提交查詢</button>
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

});
</script>
{{--
<style>
    .range{
        display:inline-block;
        margin:1em;
    }
</style>
<form class="box-group" action="{{ $action }}" method="post" id="frmInfo" onSubmit="return checkSubmit(this);">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div style="width:80%; margin:1em;">
        <div class="range">
            <i class="fa fa-clock-o"></i>
        </div>
        <label class="range">報表區間：</label>
        <input type="text" class="range" style="width:50%;" id="activity_date" name="range" value="{{ $range }}" notnull="true" detail="報表區間" placeholder="YYYY/MM/DD - YYYY/MM/DD">
        <button class="range">提交搜尋</button>
    </div>
</form>

--}}