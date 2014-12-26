<?php

namespace Controller\Cli;

class Run {

    function index(){
        echo "\n\tPHP built-in web server\n";
        if(strpos(strtoupper(PHP_OS), 'WIN') !== false) {
            echo "\n\tRunning in Windows ...\n\n";
            exec('explorer "http://localhost:8080" & php -S localhost:8080 index.php');
        }elseif(strpos(strtoupper(PHP_OS), 'LINUX') !== false) {
            echo "\n\tRunning in Linux ...\n\n";
            exec('php -S localhost:8080 index.php');
        }else echo "\n\tStopped!! (O.S. error)\n\n...";
    }
}