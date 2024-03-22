@php
//Assert: At this point, they are either Faculty or Staff
$type = $person->Faculty ? "faculty" : "staff";
@endphp
<strong class='{{ $type }}'>{{$person->display_name}}</strong>
