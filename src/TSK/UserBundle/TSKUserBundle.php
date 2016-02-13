<?php

namespace TSK\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TSKUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
