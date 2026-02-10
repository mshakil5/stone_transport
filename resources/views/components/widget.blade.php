
<div class="box box-widget widget-user-2">
    <div class="widget-user-header">
        <h3 class="widget-user-username">{{$title}}</h3>
        @if(isset($description))
        <h5 class="widget-user-desc table-responsive">{{$description}}</h5>
        @endif
    </div>
    <div class="box-body table-responsive">
        {{$body}}
    </div>
</div>
