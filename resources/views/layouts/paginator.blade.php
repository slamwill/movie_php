@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="disabled"><span>&raquo;</span></li>
        @endif
    </ul>





	<div class="pagination mobile">
		<div class="row">
			{{-- Previous Page Link --}}
			<div class="col-xs-4">
			@if ($paginator->onFirstPage())
				<button class="btn btn-block-mobile-only col-xs-12 disabled"><span>上一页</span></button>
			@else
				<a href="{{ $paginator->previousPageUrl() }}" rel="prev"><button class="btn btn-block-mobile-only col-xs-12">上一页</button></a>
			@endif
			</div>

			{{-- Pagination Elements --}}
			<div class="col-xs-4">
				<select id="page_index" class="form-control"" onchange="window.location.href = this.value">		
					<option selected>{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</option>
					@foreach (range(1,$paginator->lastPage()) as $page)
						<option value="{{ url()->current() }}?page={{$page}}">{{ $page }}</option>
					@endforeach
				</select>
			</div>

			{{-- Next Page Link --}}
			<div class="col-xs-4">
				@if ($paginator->hasMorePages())
					<a href="{{ $paginator->nextPageUrl() }}" rel="next"><button class="btn btn-block-mobile-only col-xs-12">下一页</button></a>
				@else
					<button class="btn btn-block-mobile-only col-xs-12 disabled"><span>下一页</span></button>
				@endif
			</div>

		</div>
	</div>
@endif