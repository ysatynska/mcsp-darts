@extends('template')

@if (isset($my_games))
    @section('title')
        My Games
    @endsection
    @section('heading')
        My Games
    @endsection
@elseif (isset($ranks))
    @section('title')
        @if ($students_only)
            Students' Ranks
        @else
            All Ranks
        @endif
    @endsection
    @section('heading')
        @if ($students_only)
            Students' Ranks
        @else
            All Players' Ranks
        @endif
    @endsection
@else
    @section('title')
        @if ($students_only)
            Student Games
        @else
            All Games
        @endif
    @endsection
    @section('heading')
        @if ($students_only)
            Student Games
        @else
            All Games
        @endif
    @endsection
@endif

@section('page_title')
    Ping Pong
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/allGames.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/tables.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/weather.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/open-weather-icons.css')}}" rel="stylesheet" />
@endsection

@section('javascript')
    <script>
        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        $(document).ready(function() {
            $('#header').append($('#weather-toggle'));
            $('#weather-toggle').show();
            var tempScale = getCookie("temp-scale");
            if (tempScale == 'C') {
                $('#c-toggle').addClass('active');
                $('#degree-c').show();
            }
            else {
                $('#f-toggle').addClass('active');
                $('#degree-f').show();
            }
        });
        $(document).on("click", "#f-toggle", function () {
            $(this).addClass('active');
            $('#c-toggle').removeClass('active');
            $('#degree-c').hide();
            $('#degree-f').show();
            document.cookie = "temp-scale=F";
        });
        $(document).on("click", "#c-toggle", function () {
            $(this).addClass('active');
            $('#f-toggle').removeClass('active');
            $('#degree-f').hide();
            $('#degree-c').show();
            document.cookie = "temp-scale=C";
        });
    </script>
@endsection
@section('content')
@include('weather')
<div id="tactical-nav" class="tactical-nav-menu">
    <ul>
        @foreach ($all_terms as $term)
            <li>
                @if (isset($my_games))
                    <a href="{{ action([App\Http\Controllers\GamesController::class, 'myGames'], ['term' => $term]) }}" style="background-color: {{$term == $current_term ? 'rgb(245, 245, 245)' : ''}}">
                        {{$term}}
                    </a>
                @elseif (isset($ranks))
                    <a href="{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => $students_only, 'term' => $term]) }}" style="background-color: {{$term == $current_term ? 'rgb(245, 245, 245)' : ''}}">
                        {{$term}}
                    </a>
                @else
                    <a href="{{ action([App\Http\Controllers\AdminController::class, 'allGames'], ['students_only' => $students_only, 'term' => $term]) }}" style="background-color: {{$term == $current_term ? 'rgb(245, 245, 245)' : ''}}">
                        {{$term}}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
<div>
    <div class="grid-2 mb-10">
        <div class="grid-item align-self-center">
            <h3 class="inline">
                @if (isset($my_games))
                    My Games
                @elseif (isset($ranks))
                    <a href='{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => 'yes', 'search' => $search, 'term' => $current_term]) }}'
                        @if (!$students_only)
                            style="color:gray; font-size:16px"
                        @endif>
                        Students' Ranks
                    </a>
                    <br>
                    <a href='{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => 'no', 'search' => $search, 'term' => $current_term]) }}'
                        @if ($students_only)
                            style="color:gray; font-size:16px"
                        @endif>
                        All Players' Ranks
                    </a>
                @else
                    <a href='{{ action([App\Http\Controllers\AdminController::class, 'allGames'], ['students_only' => 'yes', 'search' => $search, 'term' => $current_term]) }}'
                        @if (!$students_only)
                            style="color:gray; font-size:16px"
                        @endif>
                        Student Games
                    </a>
                    <br>
                    <a href='{{ action([App\Http\Controllers\AdminController::class, 'allGames'], ['students_only' => 'no', 'search' => $search, 'term' => $current_term]) }}'
                        @if ($students_only)
                            style="color:gray; font-size:16px"
                        @endif>
                        All Games
                    </a>
                @endif
            </h3>
        </div>
        <div class="grid-item justify-self-end align-self-center">
            <form id="search-form" method="GET" action={!!$search_action!!}>
                <div class="input-group">
                    <input type="text" placeholder="Name or Score" class="form-control width100" name="search" id="search" value="{{ $search ?? "" }}" />
                    <span class="input-group-btn">
                        <button id="search_btn" type="submit" class="btn btn-info">&nbsp<span class="far fa-search"></span>&nbsp</button>
                    </span>
                </div>
                <input type="hidden" name="term" value="{{$current_term}}" />
                @isset($students_only)
                    <input type="hidden" name="students_only" value="{{$students_only ? "yes" : "no"}}" />
                @endisset
            </form>
        </div>
        </div>
    <table>
        <thead>
            <tr>
                @if (isset($ranks))
                    <th scope="col" class="bold">Rank</th>
                    <th scope="col" class="bold">Name</th>
                    <th scope="col" class="bold numericTd">Rating</th>
                    <th scope="col" class="bold numericTd">Total Net</th>
                    <th scope="col" class="bold numericTd">Games Played</th>
                @else
                    <th scope="col" class="bold">Date </th>
                    @if (isset($my_games))
                        <th scope="col" class="bold">Opponent </th>
                        <th scope="col" class="bold numericTd">Their Score </th>
                        <th scope="col" class="bold numericTd">My Score</th>
                    @else
                        <th scope="col" class="bold">Player 1 </th>
                        <th scope="col" class="bold">Player 2 </th>
                        <th scope="col" class="bold numericTd">Score 1 </th>
                        <th scope="col" class="bold numericTd">Score 2 </th>
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $data_point)
                <tr>
                    @if (isset($ranks))
                        @if ($students_only)
                            <td> {{$data_point->rank_students}} </td>
                        @else
                            <td> {{$data_point->rank_all}} </td>
                        @endif
                        <td>
                            {{$data_point->user->rc_full_name}}
                            @php $gameStreak = $data_point->numGamesStreak($students_only) @endphp
                            @if ($gameStreak >= 3)
                                <span style="color:#a13535; font-weight: 700; font-size: 14px">
                                    {{$gameStreak}}-game streak
                                    <i class="fa-sharp fa-solid fa-fire fa-lg" style="color: #a13535;"></i>
                                </span>
                            @endif
                        </td>
                        @if ($students_only)
                            <td class="numericTd"> {{$data_point->rating_students}} </td>
                            <td class="numericTd"> {{$data_point->total_net_students}} </td>
                        @else
                            <td class="numericTd"> {{$data_point->rating_all}} </td>
                            <td class="numericTd"> {{$data_point->total_net_all}} </td>
                        @endif
                        <td class="numericTd"> {{$data_point->numGamesPlayed($students_only)}} </td>
                    @else
                        <td> {{$data_point->updated_at->format('M j, g:ia')}} </td>

                        @if (isset($my_games))
                            @if ($data_point->player1->rcid === $my_rcid)
                                <td> {{$data_point->player2->user->rc_full_name}} </td>
                                <td class="numericTd"> {{$data_point->player2_score}} </td>
                                <td class="numericTd"> {{$data_point->player1_score}} </td>
                            @else
                                <td> {{$data_point->player1->user->rc_full_name}} </td>
                                <td class="numericTd"> {{$data_point->player1_score}} </td>
                                <td class="numericTd"> {{$data_point->player2_score}} </td>
                            @endif
                        @else
                            <td> {{$data_point->player1->user->rc_full_name}} </td>
                            <td> {{$data_point->player2->user->rc_full_name}} </td>
                            <td class="numericTd"> {{$data_point->player1_score}} </td>
                            <td class="numericTd"> {{$data_point->player2_score}} </td>
                        @endif
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grid-2 mb-0">
        <div class="grid-item align-self-center py-15">
            @if (isset($ranks))
                @if ($last_updated)
                    <p style="color:gray; font-size:14px" class="mb-0">Updated: {{$last_updated}} </p>
                @endif
            @elseif (isset($is_admin))
                <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                    href = '{{action([App\Http\Controllers\AdminController::class, 'exportStudentOnly'], ['term' => $current_term])}}'
                >Export Students Only</a>

                <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                    href = '{{action([App\Http\Controllers\AdminController::class, 'exportAll'], ['term' => $current_term])}}'
                >Export All</a>
            @endif
        </div>
        <div class="grid-item justify-self-end align-self-center">
            {{$data->links()}}
        </div>
    </div>
    @if (isset($ranks))
        <p style="color:rgb(161, 53, 53)" class="font-sm-15 font-17"><strong>Note:</strong> you need to have played at least 4 unique players to have a rank. </p>
        <p style="color:#197c8e" class="font-sm-12 font-17"><strong>Rating:</strong> predicts the outcome of future games betwen the players. If player A has rating 15 and player B has rating 23, player B is predicted to score 8 points more than player A. </p>
        <p style="color:#197c8e" class="pb-20 font-sm-12 font-17"><strong>Total Net Points:</strong> shows the difference between the number of points scored and the number of points lost by the player across all games. </p>
    @endif
</div>
@endsection
