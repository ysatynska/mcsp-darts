@extends('template')

@if (isset($my_games))
    @section('title')
        My Games Records
    @endsection
    @section('heading')
        My Games Records
    @endsection
@elseif (isset($ranks))
    @section('title')
        @if ($students_only)
            Students' Ranks
        @else
            All Players' Ranks
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
        All Games Records
    @endsection
    @section('heading')
        All Games Records
    @endsection
@endif

@section('page_title')
    Ping Pong
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/allGames.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/tables.css')}}" rel="stylesheet" />
@endsection
@section('content')
<html>
    <div>
        <div class="grid-2 mb-10">
            <div class="grid-item align-self-center">
                <h3 class="inline">
                    @if (isset($my_games))
                        My Games
                    @elseif (isset($ranks))
                        <a onclick = "location.href =
                            '{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => 'yes']) }}'"
                            @if (!$students_only)
                                style="color:gray; font-size:16px"
                            @endif>
                            Students' Ranks
                        </a>
                        <br>
                        <a onclick = "location.href =
                            '{{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => 'no']) }}'"
                            @if ($students_only)
                                style="color:gray; font-size:16px"
                            @endif>
                            All Players' Ranks
                        </a>
                    @else
                        <a onclick = "location.href =
                            '{{ action([App\Http\Controllers\AdminController::class, 'allGames'], ['students_only' => 'yes']) }}'"
                            @if (!$students_only)
                                style="color:gray; font-size:16px"
                            @endif>
                            Student Games
                        </a>
                        <br>
                        <a onclick = "location.href =
                            '{{ action([App\Http\Controllers\AdminController::class, 'allGames'], ['students_only' => 'no']) }}'"
                            @if ($students_only)
                                style="color:gray; font-size:16px"
                            @endif>
                            All Games
                        </a>
                    @endif
                </h3>
            </div>
            <div class="grid-item justify-self-end align-self-center">
                <form id="search-form" method="GET" action="
                    @if (isset($my_games))
                        {{ action([App\Http\Controllers\GamesController::class, 'myGames']) }}
                    @elseif (isset($ranks))
                        {{ action([App\Http\Controllers\RanksController::class, 'showRanks'], ['students_only' => ($students_only === true) ? 'yes' : 'no']) }}
                    @else
                        {{ action([App\Http\Controllers\AdminController::class, 'allGames']) }}
                    @endif
                ">
                    <div class="input-group">
                    <input type="text" placeholder="Name or Score" class="form-control width100" name="search" id="search" value="{{ $search ?? "" }}" />
                    <span class="input-group-btn">
                        <button id="search_btn" type="submit" class="btn btn-info">&nbsp<span class="far fa-search"></span>&nbsp</button>
                    </span>
                    </div>
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
                        <th scope="col" class="bold">Rank </th>
                        <th scope="col" class="bold">Name </th>
                        <th scope="col" class="bold">Rating </th>
                        <th scope="col" class="bold">Total Net Points</th>
                    @else
                        <th scope="col" class="bold">Date </th>
                        @if (isset($my_games))
                            <th scope="col" class="bold">Opponent </th>
                            <th scope="col" class="bold">Their Score </th>
                            <th scope="col" class="bold">My Score</th>
                        @elseif (isset($ranks))
                            {{-- do smth --}}
                        @else
                            <th scope="col" class="bold">Player 1 </th>
                            <th scope="col" class="bold">Player 2 </th>
                            <th scope="col" class="bold">Score 1 </th>
                            <th scope="col" class="bold">Score 2 </th>
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
                        <td> {{$data_point->name}} </td>
                        @if ($students_only)
                            <td> {{$data_point->rating_students}} </td>
                            <td> {{$data_point->total_net_students}} </td>
                        @else
                            <td> {{$data_point->rating_all}} </td>
                            <td> {{$data_point->total_net_all}} </td>
                        @endif
                    @else
                        <td> {{$data_point->created_at->format('M j, g:ia')}} </td>

                        @if (isset($my_games))
                            @if ($data_point->player1->rcid === $my_rcid)
                                <td> {{$data_point->player2->name}} </td>
                                <td> {{$data_point->player2_score}} </td>
                                <td> {{$data_point->player1_score}} </td>
                            @else
                                <td> {{$data_point->player1->name}} </td>
                                <td> {{$data_point->player1_score}} </td>
                                <td> {{$data_point->player2_score}} </td>
                            @endif
                        @else
                            <td> {{$data_point->player1->name}} </td>
                            <td> {{$data_point->player2->name}} </td>
                            <td> {{$data_point->player1_score}} </td>
                            <td> {{$data_point->player2_score}} </td>
                        @endif
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid-2 mb-0">
            <div class="grid-item align-self-center py-15">
                @if (isset($ranks))
                    <p style="color:gray; font-size:14px" class="mb-0">Updated: {{$last_updated}} </p>
                @elseif (isset($is_admin))
                    <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                        onclick="location.href = '{{action([App\Http\Controllers\AdminController::class, 'exportStudentOnly'])}}'"
                    >Export Students Only</a>

                    <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                        onclick="location.href = '{{action([App\Http\Controllers\AdminController::class, 'exportAll'])}}'"
                    >Export All</a>
                @endif
            </div>
            <div class="grid-item justify-self-end align-self-center">
                {{$data->links()}}
            </div>
        </div>
        @if (isset($ranks))
            <p style="color:#197c8e" class="font-sm-12 font-17"><strong>Rating:</strong> predicts the outcome of future games betwen the players. If player A has rating 15 and player B has rating 23, player B is predicted to score 8 points more than player A. </p>
            <p style="color:#197c8e" class="pb-20 font-sm-12 font-17"><strong>Total Net Points:</strong> shows the difference between the number of points scored and the number of points lost by the player across all games. </p>
        @endif
    </div>
</html>
@endsection
