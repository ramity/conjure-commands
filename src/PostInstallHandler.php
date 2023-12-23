<?php

namespace Ramity\Bundle\ConjureBundle;

class PostInstallHandler
{
    private $bundleClass = 'Ramity\Bundle\ConjureBundle\ConjureBundle';
    private $symfonyPath = __DIR__ . '/../../../../';
    private $bundlesPath = $symfonyPath . 'config/bundles.php';

    public static function addBundle()
    {
        $bundlesContent = file_get_contents($bundlesFile);

        // Check if the bundle is already added to avoid duplicates
        if (strpos($bundlesContent, $bundleClass) === false)
        {
            $pattern = '/return \[/';
            $replacement = "return [\n    $bundleClass::class => ['all' => true],"
            $newContent = preg_replace($pattern, $replacement, $bundlesContent);
            file_put_contents($bundlesFile, $newContent);
        }
    }
}
