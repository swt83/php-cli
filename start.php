<?php

#################################################
# Legacy autoloader for Laravel 3.
#################################################
Autoloader::map(array(
    'Travis\\CLI' => __DIR__.'/src/cli.php',
));