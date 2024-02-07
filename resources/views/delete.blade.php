@extends('template')
@section('title')
    Submit Score
@endsection

@section('page_title')
    Ping Pong
@endsection

@section('heading')
  at <a href="https://www.roanoke.edu">Roanoke College</a>
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
@endsection
@section('content')
    <html>

    <div class="container">
        <div class="row text-center">
            <h2> Minton Invitational </h2>
        </div>

        <div class="row text-center">
            <div class="col-md-6 col-lg-6 col-sm-12 text-center">
                <label for="value1" >Player 1:</label>
                <input type="text" id="player1" placeholder="Joe Smith">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 text-center">
                <label for="value3" >Player 2: </label>
                <input type="text" id="player2" placeholder="Jane Smith">
            </div>
        </div>

        <div class="row text-center">
            <div class="col-md-6 col-lg-6 col-sm-12 text-center">
                <label for="value2">Score 1:</label>
                <input type="text" id="score1">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 text-center">
                <label for="value4">Score 2: </label>
                <input type="text" id="score2">
            </div>
        </div>

        <div class="row text-center">
            <button onclick="saveValues()" class="btn btn-primary">Record Score!</button>
        </div>
    </div>

    <hr>

    <div class="container">
        <div class="row">
            <div class = "col-md-5 col-lg-5 col-sm-12">
                <h2>Rules</h2>
                <ol type="1">
                <li>Game to 21, win by 2.</li>
                <li>Each player gets 5 serves.</li>
                <li>You can play whomever you want, whenever you want.  </li>
                </ol>
            </div>
        <div class="col-md-7 col-lg-7 col-sm-12 text-center">
            <img src="{{URL::asset('MI.png')}}" style="width: 50%"  alt="roland">
        </div>
    </div>
    </div>

    </html>
@endsection






