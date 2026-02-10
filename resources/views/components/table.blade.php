<table  id="{{$tableID}}" class="table table-hover" width="100%">
    <thead>
    @if(isset($head))
        <tr>
            {{$head}}
        </tr>
      @endif
    </thead>
    <tbody>
    @if(isset($body))
            {{$body}}
     @endif

    </tbody>
    <tfoot>
    @if(isset($footer))
        {{$footer}}
    @endif
    </tfoot>

</table>