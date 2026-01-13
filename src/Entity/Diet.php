<?php

namespace App\Entity;

enum Diet: int
{
    case HERBIVORE = 0;
    case CARNIVORE = 1;
    case OMNIVORE = 2;
}
