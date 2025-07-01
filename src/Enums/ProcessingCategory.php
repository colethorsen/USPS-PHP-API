<?php

namespace ColeThorsen\USPS\Enums;

enum ProcessingCategory: string
{
    case LETTERS     = 'LETTERS';
    case FLATS       = 'FLATS';
    case PARCELS     = 'PARCELS';
    case MACHINABLE  = 'MACHINABLE';
    case NONSTANDARD = 'NONSTANDARD';
}
