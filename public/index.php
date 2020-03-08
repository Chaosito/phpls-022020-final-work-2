<?php

use core\exceptions\Error404;

require_once('../core/init.php');

try {
    $app = new \core\Application();
    $app->run();
} catch (Error404 $e) {
    header('HTTP/1.0 404 Not Found');
    header('Location: /404.html');
} catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    trigger_error($e->getMessage(), E_USER_ERROR);
}
