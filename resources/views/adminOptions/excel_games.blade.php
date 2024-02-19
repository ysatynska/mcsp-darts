<table>
    <thead>
        <tr>
            <th scope="col">Date </th>
            <th scope="col">Player 1 </th>
            <th scope="col">Player 2 </th>
            <th scope="col">Score 1 </th>
            <th scope="col">Score 2 </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
        <tr>
            <td> {{$game->created_at}} </td>
            <td> {{$game->player1->name}} </td>
            <td> {{$game->player2->name}} </td>
            <td> {{$game->player1_score}} </td>
            <td> {{$game->player2_score}} </td>
        </tr>
        @endforeach
    </tbody>
</table>
