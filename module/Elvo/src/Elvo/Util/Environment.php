<?php

namespace Elvo\Util;


class Environment extends Options
{

    const MODE_DEVEL = 'devel';

    const MODE_PROD = 'prod';

    const OPT_MODE = 'mode';


    public function isModeDevel()
    {
        return (self::MODE_DEVEL == $this->get(self::OPT_MODE));
    }
}