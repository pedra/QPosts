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
 * @author http://plus.google.com/+BillRocha
 */

//define('DEBUG', true);

//Loading Starter ...
if (file_exists(__DIR__.'/php/.start')) include __DIR__.'/php/.start';
elseif(file_exists(__DIR__.'/start.phar')) include 'phar://'.__DIR__.'/start.phar/php/.start';
else exit('# ERROR : "Start" not found!!');

//Loading config
Lib\Data\Config::load(_file_exists('php/config.json'));

//Mount & Run application
Lib\Request::solve();
Lib\Request::run();