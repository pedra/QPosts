<?php
/**
 * Description of Request
 *
 * @author http://plus.google.com/+BillRocha
 */

namespace Lib;
use Lib\Data\Config as CFG;

class Request
    extends Dock {

    private $node = null;
    private $controller = 'Main';
    private $ctdef = true;
    private $method = 'Index';
    private $parms = [];
    private $reqst = [];

    function solve($reqst = null){

        if(SMODE == 'cli') {
            global $argv;
            $this->reqst = $argv;
        } else {
            if($reqst == null && defined('REQST')) $reqst = REQST;

            $reqst = explode('/', trim($reqst, ' \\/'));
            foreach($reqst as $k=>$v){
                $this->reqst[$k] = trim($v);
            }
        }

        $this->parms = $this->reqst;
        //filtrando comandos (-z, -h, etc) -> vide: config.json
        if(SMODE == 'cli') static::cliCommands();

        static::solveController();
        return static::getData();
    }

    //Start controller
    function run(){
        //method?
        if(!$this->ctdef && isset($this->reqst[1])){
            $action = strtolower($this->reqst[1]);
            if(method_exists($this->node, $action)){
                $this->method = $action;
                array_shift($this->parms);
            }
        }
        $this->node->parms = $this->parms;
        $this->node->reqst = $this->reqst;
        //running
        return call_user_func_array(array($this->node, $this->method), $this->parms);
    }

    function getData(){
        return ['controller'=>$this->controller,
                'method'=>$this->method,
                'parms'=>$this->parms,
                'reqst'=>$this->reqst,
                'node'=>  $this->node];
    }

    function getController(){
        if(is_object($this->node)) return $this->node;
        return false;
    }

    // -------------------- Private Fucntions ----------------------------------


    private function solveController(){

        $path = SMODE == 'cli' ? 'Controller/Cli/' : 'Controller/';

        if(isset($this->reqst[0])){
            $ctrl = $path.ucfirst(strtolower($this->reqst[0]));

            if($this->reqst[0] !== ''){
                if(_file_exists('php/'.$ctrl.'.php')){
                    $this->controller = str_replace('/', '\\', $ctrl);
                    array_shift($this->parms);
                    $this->ctdef = false;
                    $this->node = new $this->controller();
                    return true;
                } else trigger_error('Page not found!', E_USER_ERROR);
            }
        }

        $this->controller = str_replace('/', '\\', $path).$this->controller;
        $this->node = new $this->controller();

        return false;
    }

    //varrendo os argumentos e procurando caracteres de configuração/comando
    private function cliCommands(){

        if(!CFG::get('cli')) return array_shift($this->parms);

        $commands = CFG::get('cli')->commands;

        foreach($this->parms as $k=>$v){
            if(strpos($v, '-') !== 0) continue;

            $value = trim(substr($v, 3));
            $v = substr(trim(str_replace(['-','/'], '', $v)), 0, 1);

            //apagendo o parametro
            unset($this->parms[$k]);

            if(isset($commands->$v)){
                //pegando um possível valor para o camando[ ex.: php mk.phar -l:nome_do_arquivo_de_log -z etc...]
                $commands->$v->value = $value != '' ? $value : true;
                $this->parms[$commands->$v->name] = $commands->$v->value;
            }
        }

        array_shift($this->parms);
        array_shift($this->reqst);
        return true;
    }
}
