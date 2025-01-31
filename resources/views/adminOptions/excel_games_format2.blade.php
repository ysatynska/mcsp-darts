<table>
    <thead>
        <tr>
            <th scope="col">created_at </th>
            <th scope="col">player_id </th>
            <th scope="col">oponnent_id </th>
            <th scope="col">player_name </th>
            <th scope="col">oponnent_name </th>
            <th scope="col">game_id </th>
            <th scope="col">round_number </th>
            <th scope="col">player_score </th>
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
                </tr>
            @endforeach
            @foreach($game->rounds as $round)
                <tr>
                    <td> {{$game->updated_at}} </td>
                    <td> {{$game->player2->id}} </td>
                    <td> {{$game->player1->id}} </td>
                    <td> {{$game->player2->user->rc_full_name}} </td>
                    <td> {{$game->player1->user->rc_full_name}} </td>
                    <td> {{$game->id}} </td>
                    <td> {{$round->round_number}} </td>
                    <td> {{$round->score2}} </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
