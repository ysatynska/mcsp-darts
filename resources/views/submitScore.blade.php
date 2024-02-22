@extends('template')
@section('title')
    Submit Score
@endsection

@section('page_title')
    Ping Pong
@endsection

@section('heading')
  Submit Score
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
@endsection

@section('javascript')
    <script defer src="{{URL::asset('assets/js/submitScore.js')}}"> </script>
@endsection

@section('javascript')
    <script defer src="{{URL::asset('assets/js/submitScore.js')}}"> </script>
@endsection

@section('content')
    <html>
        @if(Session::get('error'))
            <div class="row">
                <div class="col-12">
                <div class="alert alert-danger light">
                    <p>{{ Session::get('error') }}</p>
                </div>
                </div>
            </div>
        @endif

        <div class="text-center">
            <h3 class="py-15"> Minton Invitational </h3>
        </div>

        <form method="POST" action="{{ action([App\Http\Controllers\GamesController::class, 'saveScore']) }}">
            @csrf
            <div class="grid-2 py-lg-20 pb-sm-10">
                <div class="grid-item text-center">
                    <label>Player 1:
                        {!! MustangBuilder::typeaheadAjax("player1_name",
                            action([App\Http\Controllers\TypeaheadController::class, 'user_search']), $user->display_name,
                            array("input_data_name" => "input_data", "display_data_name"=>"display_data"),
                            array("class"=>"typehead text-center", "required" => true),
                            "new_person",
                            true)
                        !!}
                        <input type="hidden" name="player1_id" id="new_person1" value="{{$user->RCID}}">
                    </label>
                </div>
                <div class="grid-item text-center">
                    <label>Player 2:
                        {!! MustangBuilder::typeaheadAjax("player2_name",
                            action([App\Http\Controllers\TypeaheadController::class, 'user_search']), old('player2_name'),
                            array("input_data_name" => "input_data", "display_data_name"=>"display_data"),
                            array("class"=>"typehead text-center", "required" => true),
                            "new_person",
                            true)
                        !!}
                        <input type="hidden" name="player2_id" id="new_person2" value="{{old('player2_id')}}">
                    </label>
                </div>
                <div class="grid-item text-center">
                    <label>Score 1: <br>
                        <input class="form-control text-center" type="number" name="score1" min="0" value="{{old('score1')}}" required>
                    </label>
                </div>
                <div class="grid-item text-center">
                    <label>Score 2: <br>
                        <input class="form-control text-center" type="number" name="score2" min="0" value="{{old('score2')}}" required>
                    </label>
                </div>
            </div>

            <div class="row text-center pt-0 pb-5 submit-button">
                <input type='submit' class="btn btn-primary mb-15" value="Record Score">
                <br>
                <a class = "btn btn-warning" name = "join_chat" value="Join Discord Server"
                    onclick="location.href = 'https://discord.gg/SzbJs9aS'"
                >Join Discord Server</a>
            </div>
        </form>
        <hr>
        <div class="grid-2 pt-lg-20 gap-8">
                <div class = "grid-item justify-self-center">
                    <h3 class="text-center">Rules</h3>
                    <ol>
                    <li>Game to 21, win by 2.</li>
                    <li>Each player gets 5 serves.</li>
                    <li>You can play whomever you want, whenever you want.  </li>
                    </ol>
                </div>
            <div class="grid-item text-center">
                <img src="{{URL::asset('MI.png')}}" alt="Roland Minton" class="width-lg-65 width-sm-90">
            </div>
        </div>
    </html>
@endsection






