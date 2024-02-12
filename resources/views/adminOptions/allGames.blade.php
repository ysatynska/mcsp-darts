@extends('template')
@if ($my_games === true)
    @section('title')
        My Games Records
    @endsection
    @section('heading')
        My Games Records
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
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/tables.css')}}" rel="stylesheet" />
    {{-- <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> --}}
@endsection
@section('content')
<html>
    <div>
        <div class="grid-2 mb-10">
            <div class="grid-item align-self-center">
                <h2 class="inline"> @if ($my_games !== true) All Games @else My Games @endif </h2>
            </div>
            <div class="grid-item justify-self-end align-self-center">
                <form id="search-form" method="GET" action="
                @if ($my_games === true)
                    {{ action([App\Http\Controllers\GamesController::class, 'myGames']) }}
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
                </form>
            </div>
          </div>
        <table>
            <thead>
                <tr>
                  <th scope="col" class="bold">Date </th>
                  @if ($my_games !== true)
                    <th scope="col" class="bold">Player 1 </th>
                    <th scope="col" class="bold">Player 2 </th>
                    <th scope="col" class="bold">Score 1 </th>
                    <th scope="col" class="bold">Score 2 </th>
                  @else
                    <th scope="col" class="bold">Opponent </th>
                    <th scope="col" class="bold">Their Score </th>
                    <th scope="col" class="bold">My Score</th>
                  @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($all_games as $game)
                <tr>
                    <td> {{$game->created_at->format('M j, g:ma')}} </td>
                    @if ($my_games !== true)
                        <td> {{$game->player1_name}} </td>
                        <td> {{$game->player2_name}} </td>
                        <td> {{$game->player1_score}} </td>
                        <td> {{$game->player2_score}} </td>
                    @else
                        @if ($game->player1_rcid === $my_rcid)
                            <td> {{$game->player2_name}} </td>
                            <td> {{$game->player2_score}} </td>
                            <td> {{$game->player1_score}} </td>
                        @else
                            <td> {{$game->player1_name}} </td>
                            <td> {{$game->player1_score}} </td>
                            <td> {{$game->player2_score}} </td>
                        @endif
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid-2 mb-0">
            <div class="grid-item align-self-center py-15">
                {{-- add export option --}}
                @if (!$my_games)
                <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                    onclick="location.href = '{{action([App\Http\Controllers\AdminController::class, 'exportStudentOnly'])}}'"
                >Export Students Only</a>

                 <a class = "btn btn-primary mb-2" name = "export" value="Export Excel"
                    onclick="location.href = '{{action([App\Http\Controllers\AdminController::class, 'exportAll'])}}'"
                >Export All</a>
                @endif
            </div>
            <div class="grid-item justify-self-end align-self-center">
                {{$all_games->links()}}
            </div>
          </div>
    </div>
</html>
@endsection
