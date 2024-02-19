<table>
    <thead>
        <tr>
            <th scope="col">Player1 </th>
            <th scope="col">Player2 </th>
            <th scope="col">Score1 </th>
            <th scope="col">Score2 </th>
            <th scope="col">Date </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
        <tr>
            <td> {{$game->player1->name}} </td>
            <td> {{$game->player2->name}} </td>
            <td> {{$game->player1_score}} </td>
            <td> {{$game->player2_score}} </td>
            <td> {{$game->created_at}} </td>
        </tr>
        @endforeach
    </tbody>
</table>
