<?php

namespace Ramity\Bundle\ConjureBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ConjureBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        // Register your command(s) here
        $application->add(new Command\ChatGPTCommand());
        $application->add(new Command\WrapperCommand());
    }
}
