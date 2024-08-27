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
    <link href="{{URL::asset('assets/css/weather.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/open-weather-icons.css')}}" rel="stylesheet" />
@endsection

@section('javascript')
    <script defer src="{{URL::asset('assets/js/submitScore.js')}}"> </script>
    <script src="{{URL::asset('assets/js/weather-toggle.js')}}"></script>
    <script>
        $(document).on("click", "input[name='tourn_game'][value='1']", function () {
            $("input[name='term']").prop('disabled', true);
        });
        $(document).on("click", "input[name='tourn_game'][value='0']", function () {
            $("input[name='term']").prop('disabled', false);
        });
    </script>
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
    @php $tournMode = substr($current_term, -1) === 'T' @endphp

    <div class="text-center">
        <h3 class="py-15"> Minton Invitational </h3>
    </div>

    <form method="POST" action="{{ action([App\Http\Controllers\GamesController::class, 'saveScore']) }}">
        @csrf
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
            <div class="grid-item text-center">
                <label>Score 1 <br>
                    <input class="form-control text-center" type="number" name="score1" min="0" value="{{old('score1')}}" required>
                </label>
            </div>
            <div class="grid-item text-center">
                <label>Score 2 <br>
                    <input class="form-control text-center" type="number" name="score2" min="0" value="{{old('score2')}}" required>
                </label>
            </div>
        </div>

        <div class="row text-center pt-0 pb-5 submit-button">
            @if ($tournMode)
                <div class='mb-15'>
                    <label class='mb-10'> Is this a tournament game? </label>
                    <br>
                    <label> <input type="radio" name="tourn_game" value="1" {{(old('tourn_game') == '1') ? 'checked' : ''}} required>
                    Yes </label>
                    <label> <input type="radio" name="tourn_game" value="0" {{(old('tourn_game') == '0') ? 'checked' : ''}} required>
                    No </label>
                    <br>
                </div>
            @endif
            @if ($is_admin)
                <label for="term">Admin Options - Term</label>
                <br>
                <input list="existing_terms" id="term" name="term" placeholder="{{$current_term}}" class="text-center mb-15" autocomplete="off" value="{{old('term')}}">
                <datalist id="existing_terms">
                    @foreach ($all_terms as $term)
                        <option value="{{$term}}">
                    @endforeach
                </datalist>
                <br>
                <label style="color: rgb(161, 53, 53)">If you wish to {{$tournMode ? 'end the' : 'start a'}} tournament, please use '{{$tournMode ? substr($current_term, 0, -1) : $current_term.'T'}}' as the format for the term.</label>
                <br>
                <label style="color: rgb(161, 53, 53)" class='mb-15'>If you submit a game for any term other than '{{$current_term}}', all subsequent games will be recorded under that new term.</label>
                <br>
            @endif
            <input type='submit' class="btn btn-primary mb-15" value="Record Score">
            <br>
            <a class = "btn btn-warning" name = "join_chat" value="Join Discord Server"
                href = "https://discord.gg/UCYV3AVYZr"
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
@endsection






