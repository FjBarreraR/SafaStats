<?php

namespace App\Entity;

enum Type: int
{
    case TERRESTRIAL = 0;
    case AQUATIC = 1;
    case AERIAL = 2;
}
