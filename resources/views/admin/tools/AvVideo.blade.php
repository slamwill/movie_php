<div class="btn-group" data-toggle="buttons">
    @foreach($video_source as $option => $label)
    <label class="btn btn-info btn-sm {{ \Request::get('video_source', 'all') == $option ? 'active' : '' }}">
        <input type="radio" class="tools-video-source" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>

<div class="btn-group" data-toggle="buttons">
    @foreach($video_type as $option => $label)
    <label class="btn btn-default btn-sm {{ \Request::get('video_type', 'all') == $option ? 'active' : '' }}">
        <input type="radio" class="tools-video-type" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>
<div class="btn-group" data-toggle="buttons">
    @foreach($has_video as $option => $label)
    <label class="btn btn-twitter btn-sm {{ \Request::get('has_video', 'all') == $option ? 'active' : '' }}">
        <input type="radio" class="tools-has-video" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>

<div class="btn-group" data-toggle="buttons">
    @foreach($enable as $option => $label)
    <label class="btn btn-success btn-sm {{ \Request::get('has_video', 'all') == $option ? 'active' : '' }}">
        <input type="radio" class="tools-enable" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>


<div class="btn-group" data-toggle="buttons">
    @foreach($is_free as $option => $label)
    <label class="btn btn-danger btn-sm {{ \Request::get('is_free', 'all') == $option ? 'active' : '' }}">
        <input type="radio" class="tools-is-free" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>
