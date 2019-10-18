<footer class="footer">
		<div class="container">
	
			<div class="row">
				<div class="col-md-6">
					<h4>警告</h4>
					<p class="footer-text">
						本網站只這合十八歲或以上人士觀看。內容可能令人反感；不可將本網站的內容派發、傳閱、出售、出租、交給或借予年齡未滿18歲的人士或將本網站內容向該人士出示、播放或放映。
					</p>
					<div class="foot-line"></div>
				</div>
				<div class="col-md-6">
					<h4>LEGAL DISCLAIMER WARNING: </h4>
					<p class="footer-text">
						THIS FORUM CONTAINS MATERIAL WHICH MAY OFFEND AND MAY NOT BE DISTRIBUTED, CIRCULATED, SOLD, HIRED, GIVEN, LENT,SHOWN, PLAYED OR PROJECTED TO A PERSON UNDER THE AGE OF 18 YEARS.
					</p>

				</div>
			
			
			</div>

			<p class="footer-text attention">
				站点申明：我们立足于美利坚合众国，受北美法律保护,未满18岁或被误导来到这里，请立即离开！
			</p>

			<p class="copywrite">Copyright © {{ env('APP_NAME') }} all rights reserve</p>
			<center class="hide">執行時間： {{ getMicroTime() }}</center>
			<p>&nbsp;</p>

		</div>
</footer>

<script type="text/javascript">
@if(session()->has('message'))
	app.success('{{ session()->get('message') }}');
@endif

@if ($errors->any())
	app.error('{{ $errors->first() }}');
@endif
</script>
