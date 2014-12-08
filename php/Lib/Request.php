<?php
/**
 * Description of Request
 *
 * @author http://plus.google.com/+BillRocha
 */

namespace Lib;
use Lib\Data\Config as CFG;

class Request {
    
    static $node = null;
    static $controller = 'Main';
    static $ctdef = true;
    static $method = 'Index';
    static $parms = [];
    static $reqst = [];
    
    static function solve($reqst = null){

        if(SMODE == 'cli') {
            global $argv;
            static::$reqst = $argv;
        } else {
            if($reqst == null && defined('REQST')) $reqst = REQST;

            $reqst = explode('/', trim($reqst, ' \\/'));
            foreach($reqst as $k=>$v){
                static::$reqst[$k] = trim($v);
            }
        }

        static::$parms = static::$reqst;
        //filtrando comandos (-z, -h, etc) -> vide: config.json
        if(SMODE == 'cli') static::cliCommands();
        
        static::solveController();        
        return static::getData();
    }
    
    //Start controller
    static function run(){
        //method?
        if(!static::$ctdef && isset(static::$reqst[1])){
            $action = strtolower(static::$reqst[1]);
            if(method_exists(static::$node, $action)){
                static::$method = $action;
                array_shift(static::$parms); 
            }
        }        
        static::$node->parms = static::$parms;
        static::$node->reqst = static::$reqst;
        //running
        return call_user_func_array(array(static::$node, static::$method), static::$parms);            
    }    
    
    static function getData(){
        return ['controller'=>static::$controller,
                'method'=>static::$method,
                'parms'=>static::$parms,
                'reqst'=>static::$reqst,
                'node'=>  static::$node];
    }
    
    static function getController(){
        if(is_object(static::$node)) return static::$node;
        return false;
    }
    
    // -------------------- Private Fucntions ----------------------------------


    static private function solveController(){

        $path = SMODE == 'cli' ? 'Controller/Cli/' : 'Controller/';

        if(isset(static::$reqst[0])){ 
            $ctrl = $path.ucfirst(strtolower(static::$reqst[0])); 

            if(static::$reqst[0] !== ''){
                if(_file_exists('php/'.$ctrl.'.php')){
                    static::$controller = str_replace('/', '\\', $ctrl);
                    array_shift(static::$parms);
                    static::$ctdef = false;
                    static::$node = new static::$controller();
                    return true;
                } else trigger_error('Page not found!', E_USER_ERROR);
            }
        }

        static::$controller = str_replace('/', '\\', $path).static::$controller;
        static::$node = new static::$controller();

        return false;
    }

    //varrendo os argumentos e procurando caracteres de configuração/comando
    static private function cliCommands(){

        if(!CFG::get('cli')) return array_shift(static::$parms);

        $commands = CFG::get('cli')->commands;

        foreach(static::$parms as $k=>$v){
            if(strpos($v, '-') !== 0) continue;
            
            $value = trim(substr($v, 3));
            $v = substr(trim(str_replace(['-','/'], '', $v)), 0, 1);

            //apagendo o parametro
            unset(static::$parms[$k]);

            if(isset($commands->$v)){ 
                //pegando um possível valor para o camando[ ex.: php mk.phar -l:nome_do_arquivo_de_log -z etc...]
                $commands->$v->value = $value != '' ? $value : true;
                static::$parms[$commands->$v->name] = $commands->$v->value; 
            }
        }

        array_shift(static::$parms);
        array_shift(static::$reqst);        
        return true;
    }
}
