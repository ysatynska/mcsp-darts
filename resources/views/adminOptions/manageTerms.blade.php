@extends('template')
@section('title')
    Manage Terms
@endsection

@section('page_title')
    Ping Pong
@endsection

@section('heading')
    Manage Terms
@endsection

@section('stylesheets')
    <link href="{{URL::asset('assets/css/submitScore.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/weather.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/css/open-weather-icons.css')}}" rel="stylesheet" />
@endsection

@section('javascript')
    <script>
        var current_term_id = @json($current_term->id);
    </script>
    <script src="{{URL::asset('assets/js/weather-toggle.js')}}"></script>
    <script src="{{URL::asset('assets/js/manageTerms.js')}}"></script>
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
        <h3 class="py-15"> Manage Terms </h3>
    </div>

    <form method="POST" action="{{ action([App\Http\Controllers\AdminController::class, 'changeCurrentTerm']) }}">
        @csrf
        <div class="row text-center pt-0 pb-5 submit-button">
            <label>Add a New Term</label>
            <br>
            <input type="text" name="new_term_name" class='mb-15' maxlength="255" required>
            <br>
            <div class='mb-10'>
                <label> Is this a tournament term? </label>
                <br>
                <label> <input type="radio" name="tourn_term" value="1" {{(old('tourn_term') == '1') ? 'checked' : ''}} required>
                Yes </label>
                <label> <input type="radio" name="tourn_term" value="0" {{(old('tourn_term') == '0') ? 'checked' : ''}} required>
                No </label>
            </div>
            <label for="old_terms">Or Switch to a Previous One</label>
            <br>
            <select name="old_term" id="old_terms" style="width: 190px;" class='mb-15'>
                <option value="{{$current_term->id}}">{{$current_term->term_name}}@if($current_term->tourn_term)T @endif</option>
                @foreach ($all_terms as $term)
                    @if($current_term->id != $term->id)
                        <option value="{{$term->id}}">{{$term->term_name}}@if($term->tourn_term)T @endif</option>
                    @endif
                @endforeach
            </select>
            <br>
            <input type='submit' class="btn btn-primary" value="Submit">
        </div>
    </form>
@endsection





