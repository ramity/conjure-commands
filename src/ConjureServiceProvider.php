<?php

namespace OOS\Conjure;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ConjureServiceProvider extends Bundle
{
    public function registerCommands(Application $application)
    {
        // Register your command(s) here
        $application->add(new Command\ChatGPTCommand());
    }
}
