<?php

/*
 * Generic globals functions - G²F
 *
 */

//Jump to URL
function go($uri = '', $type = 'location', $cod = 302) {

    //se tiver 'http' na uri então será externo.
    if (strpos($uri, 'http://') === false
          || strpos($uri, 'https://') === false)
        $uri = URL . $uri;

    //send header
    if (strtolower($type) == 'refresh') header('Refresh:0;url=' . $uri);
    else header('Location: ' . $uri, TRUE, $cod);

    //... and stop
    exit;
}

//Download de arquivo em modo PHAR (interno)
function download($reqst = '') {
    //não faz download se a pasta for diferente de 'assets'
    if (strpos($reqst, 'assets/') === false) return false;

    //checando a existencia do arquivo solicitado
    $reqst = _file_exists($reqst);
    if($reqst == false) return false;

    //gerando header apropriado
    include __DIR__ . '/mimetypes.php';
    $ext = end((explode('.', $reqst)));
    if (!isset($_mimes[$ext])) $mime = 'text/plain';
    else $mime = (is_array($_mimes[$ext])) ? $_mimes[$ext][0] : $_mimes[$ext];

    //get file
    $dt = file_get_contents($reqst);

    //download
    ob_end_clean();
    ob_start('ob_gzhandler');

    header('Vary: Accept-Language, Accept-Encoding');
    header('Content-Type: ' . $mime);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($reqst)) . ' GMT');
    header('Cache-Control: must_revalidate, public, max-age=31536000');
    header('Content-Length: ' . strlen($dt));
    header('x-Server: nfw/RunPHP');
    header('ETAG: '.md5($reqst));
    exit($dt);
}

//Solve Paths [ROOT, RPHAR, SMODE & CACHE]
function solvePaths($base) {
    $base .= '/';
    $phar = (strpos($base, 'phar://') !== false);

    define('SMODE', php_sapi_name());
    define('ROOT', $phar ? str_replace('phar://', '', dirname($base)) . '/' : $base);
    define('RPHAR', $phar ? $base : false);
    define('CACHE', ROOT . 'cache/');

    defined('DEBUG') || define('DEBUG', false);
}

//Solve URL and define REQST & URL constants
function solveUrl() {
    //Detect SSL access
    if (!isset($_SERVER['SERVER_PORT'])) $_SERVER['SERVER_PORT'] = 80;
    $http = (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';

    //What's base??!
    $base = isset($_SERVER['PHAR_SCRIPT_NAME'])
                ? dirname($_SERVER['PHAR_SCRIPT_NAME'])
                : rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), ' /');
    if ($_SERVER['SERVER_PORT'] != 80) $base .= ':' . $_SERVER['SERVER_PORT'];

    //URL & REQST Constants:
    define('REQST', isset($_SERVER['REQUEST_URI']) ? urldecode(trim(str_replace($base, '', trim($_SERVER['REQUEST_URI'])), ' /')) : '');
    define('URL', isset($_SERVER['SERVER_NAME']) ? $http . $_SERVER['SERVER_NAME'] . $base . '/' : '');
}

//Check if file exists - return real path of file or false
function _file_exists($file){
    if(file_exists($file)) return $file;
    if(file_exists(ROOT.$file)) return ROOT.$file;
    if(file_exists(RPHAR.$file)) return RPHAR.$file;
    $xfile = str_replace(ROOT, RPHAR, $file);
    if(file_exists($xfile)) return $xfile;
    return false;
}

//Print mixed data and exit
function e($v) { exit(p($v)); }
function p($v, $echo = false) {
    $tmp = '<pre>' . print_r($v, true) . '</pre>';
    if ($echo) echo $tmp;
    else return $tmp;
}

function _autoloadPhar($class){

    echo '<h1>Carregar library em arquivos ".phar"</h1><h2>Supondo a seguinte classe:</h2><b>'.$class.'</b><br><br>';

    $class = 'php/'.str_replace('\\', '/', trim($class, ' \\'));
    $file = 'phar://'.RPHAR.$class.'.php';
    if(file_exists($file)) return $file;

    echo $file.'<span style="color:#999"> // caso esteja rodando o projeto ou "php" em PHAR</span>';

    $pct = explode('/', $class);
    $pct = array_reverse($pct, true);
    $tmp = '';
    $count = count($pct) - 1;

    foreach($pct as $k=>$v){
        for($i = 0; $i <= $count; $i++){
            if($k == $i) $tmp .= $pct[$i].'.phar/';
            else $tmp .= $pct[$i].'/';
        }
        $file = 'phar://'.ROOT.trim($tmp, ' /').($k < $count ? '.php' : '');
        echo '<br>'.$file;
        if(file_exists($file)) return $file;
        $tmp = '';
    }

    echo '<br><br><b>Finished!</b>';
    return false;
}

/**
 * Serialização e gravação dos dados em arquivo '.bkp'
 *
 *
 */
function shutdown(){
    echo 'shutdown';
        file_put_contents(ROOT.'php/serial.bkp', Dock::__serialize());
        //$a = new A;
        //$a::__unserialize($x);

        //e($a->view());
}

function error(){
    exit('Class: '.__FUNCTION__);
}


// =========================== HTML/DOC
function _loadStyle($file){

}

function _loadScript($file){

}

//Functions ----------------------------------------END :(