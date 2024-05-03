<div id="weather-toggle" class="none">
    <span class="temp" id="f-toggle">F</span>
    <span class="temp" id="c-toggle">C</span>

    <div class="weather none" id="degree-f">
        <a href="https://openweathermap.org/?lat=37.2942&lon=-80.0549" class="weather-icon" style="color: white; font-family: pt-serif-pro,serif">
        <i class="owi owi-{{$weather->icon}}" style="font-size: 2em; color: var(--yellow)"></i>
        <span style="font-size: 26px">{{$weather->temperature}}&deg;</span>F<br>
        <span class="hidden-sm hidden-xs weather-details">
            <strong>{{$weather->summary}}</strong><br>
            <strong>H {{$weather->high}}&deg;F </strong>
            <strong>L {{$weather->low}}&deg;F</strong>
        </span>
        </a>
    </div>

    <div class="weather none" id="degree-c">
        <a href="https://openweathermap.org/?lat=37.2942&lon=-80.0549" style="color: white; font-family: pt-serif-pro,serif">
        <i class="owi owi-{{$weather->icon}}" style="font-size: 2em; color: var(--yellow)"></i>
        @php($degreec = ($weather->temperature - 32) / 1.8)
        <strong style="font-size: 26px">{{number_format($degreec)}}&deg;</strong>C<br>
        <span class="weather-details hidden-sm hidden-xs">
            <strong>{{$weather->summary}}</strong><br>
            @php($highc = ($weather->high - 32) / 1.8)
            @php($lowc = ($weather->low - 32) / 1.8)
            <strong>H {{number_format($highc)}}&deg;C </strong>
            <strong>L {{number_format($lowc)}}&deg;C</strong>
        </span>
        </a>
    </div>
</div>
