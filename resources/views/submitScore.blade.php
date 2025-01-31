@extends('template')
@section('title')
    Submit Score
@endsection

@section('page_title')
    MCSP Darts
@endsection

@section('heading')
    Submit Score
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/weather.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/open-weather-icons.css')}}" rel="stylesheet" />
@endsection

@section('javascript')
    <script defer src="{{URL::asset('assets/js/submitScore.js')}}"> </script>
    <script src="{{URL::asset('assets/js/weather-toggle.js')}}" defer></script>
@endsection

@section('content')
    @include('weather')
    @if(Session::get('error') || $errors->any())
        <div class="row">
            <div class="col-12">
            <div class="alert alert-danger light">
                <p>{{ Session::get('error') }} {!! implode('', $errors->all('<div>:message</div>')) !!}</p>
            </div>
            </div>
        </div>
    @endif

    <div class="text-center">
        <h3 class="py-15"> Second Minton Invitational </h3>
    </div>

    <form method="POST" action="{{ action([App\Http\Controllers\GamesController::class, 'saveScore']) }}">
        @csrf
        <input type="hidden" name="term_id" value="{{$current_term->id}}">
        <div class="grid-2 py-lg-20 pb-sm-10">
            <div class="grid-item text-center">
                <label>Player 1
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
            <div class="player_scores_div grid-item text-center">
                <label>Total - <span id="total-score1">0 <span style="color: rgb(220, 58, 17);">(271 to go) </span></span>
                    <br>
                    <input type="number" name="player1_scores[0]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                    <input type="number" name="player1_scores[1]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                    <input type="number" name="player1_scores[2]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                </label>
            </div>
            <div class="grid-item text-center">
                <label>Player 2
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
            <div class="player_scores_div grid-item text-center">
                <label>Total - <span id="total-score2">0 <span style="color: rgb(220, 58, 17);">(271 to go) </span></span>
                    <br>
                    <input type="number" name="player2_scores[0]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                    <input type="number" name="player2_scores[1]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                    <input type="number" name="player2_scores[2]" style="width: 7rem; display: inline-block;" class="text-center form-control" min="0">
                </label>
            </div>
        </div>

        <div class="row text-center pt-0 pb-5" id="spot-for-error">
            @if ($current_term->tourn_term)
                <div class='mb-15'>
                    <label class='mb-10'> Is this game for a tournament? </label>
                    <br>
                    <label> <input type="radio" name="tourn_game" value="1" {{(old('tourn_game') == '1') ? 'checked' : ''}} required>
                    Yes </label>
                    <label> <input type="radio" name="tourn_game" value="0" {{(old('tourn_game') == '0') ? 'checked' : ''}} required>
                    No </label>
                    <br>
                </div>
            @endif
            <div>
                <button type="button" class="btn btn-primary mb-20" id="update_totals">Update Totals</button>
            </div>
            <a class = "btn btn-warning" name = "join_chat" value="Join Discord Server"
                href = "https://discord.gg/UCYV3AVYZr"
            >Join Discord Server</a>
        </div>
    </form>
    <hr>
    <br>
    <div class="grid-2 pt-lg-20 gap-8">
            <div class = "grid-item justify-self-center mb-25">
                <h3 class="text-center">Rules</h3>
                <ol>
                <li>Games to 271, win by 1.</li>
                <li>Players alternate each throw.  </li>
                <li>Each player gets 3 shots per round.</li>
                </ol>
            </div>
        <div class="grid-item text-center img-sm">
            <img src="{{URL::asset('MIDartsSm.png')}}" alt="Roland Minton" class="width-90">
        </div>
        <div class="grid-item text-center img-lg">
            <img src="{{URL::asset('MIDartsLg.png')}}" alt="Roland Minton" class="width-65">
        </div>
    </div>
@endsection






