
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="box box-primary">

            <div class="box-body ">
			
                <form class="form-inline" action="{{ URL::current() }}" method="get" pjax-container="" accept-charset="UTF-8">
                    {{--  {{ csrf_field() }}  --}}
                    <div class="form-group">

                        <label>報表日期：</label>
                        <div class="input-group " style="width: 410px;">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="start_date" id="start_date" value="{{ $start_date}}" class="form-control date_start" style="width: 150px">
                                    </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="end_date" id="end_date" value="{{ $end_date }}" class="form-control date_end" style="width: 150px">
                                    </div>
                            </div>
                        </div>

                    </div>
                    <button class="btn btn-default "  type="submit" id="submit" style="margin-left:25px;"><i class="fa fa-search"></i>&nbsp;&nbsp;提交查詢</button>
                </form>

			</div>
		</div>
	</div>
</div>



<script>


$('#submit').on('click', function(){

	var start_date = new Date( $('#start_date').val() );
	var end_date = new Date( $('#end_date').val() );

	//start_date = new Date(start_date);
	//end_date = new Date(end_date);

	if( (end_date - start_date) > 2937600000 )
	{
		alert("日期範圍需小於35天");
		//$('#end_date').val( new Date($.now()) )
		return false;
	}

});



$(function () {
    $('.date_start').datetimepicker({"format":"YYYY/MM/DD","locale":"en"});

	//alert('555555');

	//alert( $('.date_start') );
	//var d = new Date();
	//alert(d);

    $('.date_end').datetimepicker({"format":"YYYY/MM/DD","locale":"en","useCurrent":false});
    $(".date_start").on("dp.change", function (e) {
        $('.date_end').data("DateTimePicker").minDate(e.date);
    });
    $(".date_end").on("dp.change", function (e) {
        $('.date_start').data("DateTimePicker").maxDate(e.date);
    });

    $("#btn-cancel").on("click", function (e) {
      $("#keyword").val("");
      $("#group").children().each(function(){
        if ($(this).text() == "全部"){
          $(this).attr("selected", true);
        }
      });
    })
});
</script>
