<?php

namespace ColeThorsen\USPS\Enums;

enum PaymentAccountType: string
{
    case EPS    = 'EPS'; // Enterprise Payment System
    case PERMIT = 'PERMIT';
    case METER  = 'METER';
    case TRUST  = 'TRUST';
}
