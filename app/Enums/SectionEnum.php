<?php

namespace App\Enums;

enum SectionEnum: string
{
    const BG = 'bg_image';

    case EXAMPLE = 'example';
    case EXAMPLES = 'examples';

    case INTRO = 'intro';

    case ABOUT = 'about';

    //common sections
    case FOOTER = 'footer';
    case HEADER = 'header';

    case BANNER = 'banner';
    case STATISTICS = 'statistics';

    case PARTNERS = 'partners';
    case EXPERIENCE = 'experience';

    case MISSION = 'mission';
    case TEAM = 'team';
}

