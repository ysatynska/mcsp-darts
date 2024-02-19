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
                        All Games
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
                    @isset($ranks)
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
                        <td> {{$data_point->total_net}} </td>
                    @else
                        <td> {{$data_point->created_at->format('M j, g:ma')}} </td>
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
                        @elseif (isset($ranks))
                            {{-- do smth --}}
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
                {{-- export option --}}
                @if (isset($ranks))
                    <p style="color:gray; font-size:14px" class="mb-0">Last Updated: {{$last_updated}} </p>
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
        <p style="color:#9F3A38; font-size:16px" class="pb-20">Note that Total Net Points is one of many variables affecting a player's rank. If someone with rank 1 beat someone with rank 30 with scores 21:0 multiple times, their Total Net Points will be very high. However, if a new player beat the person with rank 1 with scores 21:10, their Total Net Points would end up being much lower but they will likely get rank 1. </p>

    </div>
</html>
@endsection
