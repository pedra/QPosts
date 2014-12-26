<?php

/* Environment Constants
 * TODO: translation to the English
 *
 * ROOT:    diretório base do site/app;
 * RPHAR:   diretório base do arquivo PHAR;
 * SMODE:   modo de acesso [cli, cliserver]
 * CACHE:   diretório de cache (deve ter permissão 0777);
 * URL:     endereço base de acesso http(s) do site;
 * REQST:   tudo o que é digitado depois da URL;
 *
 * @author http://google.com/+BillRocha
 */

define('DEBUG', true); //uncomment this for show debug bar in HTML view

//Loading Starter ...
$root = __DIR__.'/php/.start';
$phar = 'phar://'.__DIR__.'/start.phar/php/.start';

if(file_exists($root)) include $root;
elseif(file_exists($phar)) include $phar;
else exit('ERROR: "start" not found!');

//Mount & Run application
Dock::App()->solve();
Dock::App()->run();