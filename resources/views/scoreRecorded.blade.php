@extends('template')
@section('title')
    Score Submitted
@endsection

@section('page_title')
    Ping Pong
@endsection

@section('heading')
  Score Submitted
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
@endsection
@section('content')
<html>
    <body>
        <div class="text-center">
            <h2> Thank You For Submitting Your Score! </h2>
        </div>

        <div class="grid-2 py-20 gap-0 mb-0">
              <div class="grid-item text-center bold pt-serif-pro"> <h5>{{$game->player1_name}}</h5> </div>
              <div class="grid-item text-center bold pt-serif-pro"> <h5>{{$game->player2_name}}</h5> </div>
              <div class="grid-item text-center text-red"> <h6>{{$game->player1_score}}</h6> </div>
              <div class="grid-item text-center text-red"> <h6>{{$game->player2_score}}</h6> </div>
        </div>


        <div class="text-center pb-15">
            <a class="btn btn-primary"
                    onclick = "location.href =
                    '{{ action([App\Http\Controllers\GamesController::class, 'submitScore']) }}'"
                    name="home" value="home"> Submit Another Score
            </a>
        </div>
    </body>
</html>
@endsection
