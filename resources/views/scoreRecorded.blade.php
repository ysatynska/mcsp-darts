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
    <link href="{{URL::asset('assets/css/allGames.css')}}" rel="stylesheet" />
@endsection

@section('content')
<html>
    <body>
        <div class="text-center">
            <h3> Score Submitted! </h3>
        </div>

        <div class="grid-2 pb-20 pt-10 gap-0 mb-0">
              <div class="grid-item text-center align-self-center bold pt-serif-pro"> <h5>{{$game->player1->name}}</h5> </div>
              <div class="grid-item text-center align-self-center bold pt-serif-pro"> <h5>{{$game->player2->name}}</h5> </div>
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
        <div class="text-center pb-15">
            <a class="btn btn-primary"
                    onclick = "location.href =
                    '{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => 'yes']) }}'"
                    name="home" value="home"> View Ranks
            </a>
            <a class="btn btn-primary"
                    onclick = "location.href =
                    '{{ action([App\Http\Controllers\GamesController::class, 'myGames']) }}'"
                    name="home" value="home"> View My Games
            </a>
        </div>
    </body>
</html>
@endsection
