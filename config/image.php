<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MyImage Driver
    |--------------------------------------------------------------------------
    |
    | Intervention MyImage supports “GD Library” and “Imagick” to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included options:
    |   - \Intervention\MyImage\Drivers\Gd\Driver::class
    |   - \Intervention\MyImage\Drivers\Imagick\Driver::class
    |
    */

    'driver' => \Intervention\Image\Drivers\Gd\Driver::class

];
