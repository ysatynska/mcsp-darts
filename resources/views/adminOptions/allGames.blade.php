@extends('template')
@section('title')
    All Games Records
@endsection

@section('page_title')
    Ping Pong
@endsection

@section('heading')
  All Games Records
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/tables.css')}}" rel="stylesheet" />
    {{-- <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> --}}
@endsection
@section('content')
<html>
    <div id="allOrders">
        <h2> @if ($my_games !== true) All Games @else My Games @endif </h2>
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
        {{$all_games->links('pagination::tailwind')}}
    </div>
</html>
@endsection
