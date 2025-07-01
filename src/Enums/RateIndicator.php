<?php

namespace ColeThorsen\USPS\Enums;

enum RateIndicator: string
{
    // Digit-based indicators
    case THREE_DIGIT                     = '3D';
    case THREE_DIGIT_DIMENSIONAL_RECT    = '3N';
    case THREE_DIGIT_DIMENSIONAL_NONRECT = '3R';
    case FIVE_DIGIT                      = '5D';

    // Basic indicators
    case BASIC     = 'BA';
    case MIXED_NDC = 'BB';
    case NDC       = 'BM';

    // Cubic Pricing Tiers
    case CUBIC_TIER_1 = 'C1';
    case CUBIC_TIER_2 = 'C2';
    case CUBIC_TIER_3 = 'C3';
    case CUBIC_TIER_4 = 'C4';
    case CUBIC_TIER_5 = 'C5';
    case CUBIC_PARCEL = 'CP';

    // USPS Connect
    case CONNECT_LOCAL_MAIL                = 'CM';
    case CONNECT_LOCAL_SINGLE_PIECE        = 'LC';
    case CONNECT_LOCAL_FLAT_RATE_BOX       = 'LF';
    case CONNECT_LOCAL_LARGE_FLAT_RATE_BAG = 'LL';
    case CONNECT_LOCAL_OVERSIZED           = 'LO';
    case CONNECT_LOCAL_SMALL_FLAT_RATE_BAG = 'LS';

    // Distribution Centers
    case NDC_DC        = 'DC';
    case SCF           = 'DE';
    case FIVE_DIGIT_DF = 'DF';

    // Dimensional
    case DIMENSIONAL_NONRECTANGULAR     = 'DN';
    case DIMENSIONAL_RECTANGULAR        = 'DR';
    case SCF_DIMENSIONAL_NONRECTANGULAR = 'SN';
    case SCF_DIMENSIONAL_RECTANGULAR    = 'SR';

    // Priority Mail Express
    case EXPRESS_FLAT_RATE_ENVELOPE         = 'E4';
    case EXPRESS_LEGAL_FLAT_RATE_ENVELOPE   = 'E6';
    case EXPRESS_LEGAL_FLAT_RATE_ENV_SUNDAY = 'E7';
    case EXPRESS_SINGLE_PIECE               = 'PA';

    // Flat Rate
    case LEGAL_FLAT_RATE_ENVELOPE  = 'FA';
    case MEDIUM_FLAT_RATE_BOX      = 'FB';
    case FLAT_RATE_ENVELOPE        = 'FE';
    case PADDED_FLAT_RATE_ENVELOPE = 'FP';
    case SMALL_FLAT_RATE_BOX       = 'FS';
    case LARGE_FLAT_RATE_BOX       = 'PL';
    case LARGE_FLAT_RATE_BOX_APO   = 'PM';
    case SMALL_FLAT_RATE_BAG       = 'SB';

    // Tray/Pallet Boxes
    case FULL_TRAY_BOX              = 'O1';
    case HALF_TRAY_BOX              = 'O2';
    case EMM_TRAY_BOX               = 'O3';
    case FLAT_TUB_TRAY_BOX          = 'O4';
    case SURFACE_TRANSPORTED_PALLET = 'O5';
    case FULL_PALLET_BOX            = 'O6';
    case HALF_PALLET_BOX            = 'O7';

    // Cubic Soft Pack Tiers
    case CUBIC_SOFT_PACK_TIER_1  = 'P5';
    case CUBIC_SOFT_PACK_TIER_2  = 'P6';
    case CUBIC_SOFT_PACK_TIER_3  = 'P7';
    case CUBIC_SOFT_PACK_TIER_4  = 'P8';
    case CUBIC_SOFT_PACK_TIER_5  = 'P9';
    case CUBIC_SOFT_PACK_TIER_6  = 'Q6';
    case CUBIC_SOFT_PACK_TIER_7  = 'Q7';
    case CUBIC_SOFT_PACK_TIER_8  = 'Q8';
    case CUBIC_SOFT_PACK_TIER_9  = 'Q9';
    case CUBIC_SOFT_PACK_TIER_10 = 'Q0';

    // Other
    case NON_PRESORTED = 'NP';
    case OVERSIZED     = 'OS';
    case PRESORTED     = 'PR';
    case SINGLE_PIECE  = 'SP';
}
