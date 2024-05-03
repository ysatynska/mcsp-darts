<div class='row ajax-hover' style='font-size:.8em; margin: 7px;'>
    @if($person->Faculty == "Yes" || $person->Staff == "Yes")
      @include('typeahead.FacStaff')
    @elseif($person->Student == "Yes")
      @include('typeahead.Student')
    @endif
</div>
