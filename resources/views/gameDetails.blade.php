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
    <link href="{{URL::asset('assets/css/tables.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/allGames.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/weather.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/open-weather-icons.css')}}" rel="stylesheet" />
    <style>
        td, th {
            text-align: center
        }
        .mb-30 {
            margin-bottom: 30px;
        }
        .main-container {
            padding-bottom: 25px;
        }
    </style>
@endsection

@section('javascript')
    <script src="{{URL::asset('assets/js/weather-toggle.js')}}"></script>
@endsection

@section('content')
    @include('weather')
    <div class="text-center mb-30">
        <h3> {{$game->player1->user->rc_full_name}} vs {{$game->player2->user->rc_full_name}} on {{$game->updated_at->format('M j, g:i a')}} </h3>
    </div>
    <table>
        <thead>
            <tr>
                <th scope="col">Round Number</th>
                <th scope="col">{{$game->player1->user->rc_full_name}}</th>
                <th scope="col">{{$game->player2->user->rc_full_name}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rounds as $round)
                <tr>
                    <td> {{$round->round_number}} </td>
                    <td> {{$round->score1}} </td>
                    <td> {{$round->score2}} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="grid-item justify-self-end align-self-center">
        {{$rounds->links()}}
    </div>
@endsection
