<?php

namespace ColeThorsen\USPS\Enums;

enum PriceType: string
{
    case RETAIL     = 'RETAIL';
    case COMMERCIAL = 'COMMERCIAL';
    case CONTRACT   = 'CONTRACT';
}
