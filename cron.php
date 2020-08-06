<?php
exec('php artisan schedule:run', $output);
print_r($output);