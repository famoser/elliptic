<?php

function buildPath()
{
    return implode(DIRECTORY_SEPARATOR, func_get_args());
}

gc_disable();

define('TEST_DATA_DIR', buildPath(__DIR__, 'data'));

include buildPath(__DIR__, '..', 'vendor', 'autoload.php');

$requiredNestingLevel = 150;

if (extension_loaded('xdebug')) {
    $currentNestingLevel = intval(ini_get('xdebug.max_nesting_level'));

    if ($currentNestingLevel < $requiredNestingLevel) {
        fwrite(STDERR, <<<TEXT

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
IMPORTANT NOTICE:
It seems like you have the xdebug extension loaded.
To use phpecc you have to increase the maximum nesting level from $currentNestingLevel to at least $requiredNestingLevel
You can do this by adding the following line to your php.ini:

xdebug.max_nesting_level = $requiredNestingLevel

The tests will now run with this setting.
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


TEXT
        );
        ini_set('xdebug.max_nesting_level', $requiredNestingLevel);
    }
}
