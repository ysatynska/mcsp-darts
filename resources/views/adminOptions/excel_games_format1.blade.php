<table>
    <thead>
        <tr>
            <th scope="col">created_at </th>
            <th scope="col">player1_id </th>
            <th scope="col">player2_id </th>
            <th scope="col">player1_name </th>
            <th scope="col">player2_name </th>
            <th scope="col">game_id </th>
            <th scope="col">round_number </th>
            <th scope="col">player1_score </th>
            <th scope="col">player2_score </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
            @foreach($game->rounds as $round)
                <tr>
                    <td> {{$game->updated_at}} </td>
                    <td> {{$game->player1->id}} </td>
                    <td> {{$game->player2->id}} </td>
                    <td> {{$game->player1->user->rc_full_name}} </td>
                    <td> {{$game->player2->user->rc_full_name}} </td>
                    <td> {{$game->id}} </td>
                    <td> {{$round->round_number}} </td>
                    <td> {{$round->score1}} </td>
                    <td> {{$round->score2}} </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
