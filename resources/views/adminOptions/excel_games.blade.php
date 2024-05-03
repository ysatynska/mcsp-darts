<table>
    <thead>
        <tr>
            <th scope="col">Player 1 </th>
            <th scope="col">Player 2 </th>
            <th scope="col">Score 1 </th>
            <th scope="col">Score 2 </th>
            <th scope="col">Date </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
        <tr>
            <td> {{$game->player1->user->rc_full_name}} </td>
            <td> {{$game->player2->user->rc_full_name}} </td>
            <td> {{$game->player1_score}} </td>
            <td> {{$game->player2_score}} </td>
            <td> {{$game->updated_at}} </td>
        </tr>
        @endforeach
    </tbody>
</table>
