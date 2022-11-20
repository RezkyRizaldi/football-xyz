<?php

namespace App\Enums;

enum PlayerPosition: string
{
    case STRIKER = 'striker';
    case MIDFIELDER = 'midfielder';
    case DEFENDER = 'defender';
    case GOAL_KEEPER = 'goalkeeper';
}
