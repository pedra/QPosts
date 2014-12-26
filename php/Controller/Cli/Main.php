<?php
/**
 * Description of Cli\Main
 *
 * @author http://plus.google.com/+BillRocha
 */

namespace Controller\Cli;
use Lib\PharManager as PM;

class Main {

    private $phar = null;

    function index(){

        //Object PharManager
        $this->phar = new PM;

        //pegando os parãmetros
        if(isset($this->parms['quiet'])) $this->phar->quiet = true;

        //Selecionando a action
        if(isset($this->parms['show'])) $this->show();         //SHOW
        elseif(isset($this->parms['make'])) $this->compile();  //COMPILE
        else $this->help();                                    //HELP

        //Output Time
        echo "\n\n".$this->totalTime(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']);
    }

    /* SHOW
     * Show data from [name].phar
     *
     */
    function show(){
        if(!isset($this->parms[0])) return $this->help('type the "path/to/[project name].phar".');
        echo $this->phar->formatLog($this->phar->show($this->parms[0]));
    }

    /* COMPILE
     * Compile this projet
     *
     */
    function compile(){

        if(!isset($this->parms[0])) return $this->help('type the "path/to/[project name].phar".');
        if(end((explode('.', $this->parms[0]))) != 'phar') return $this->help('file extension ".phar" - it\'s important!');

        $this->phar->compress = true;
        $this->phar->create($this->parms[0], (isset($this->parms[1]) ? $this->parms[1] : ROOT));
        if($e = $this->phar->getError()) $this->help($e);
    }

    /* HELP
     * show help message
     *
     * @parm msg string     insert a extra title in help [optional]
     *
     */
    function help($msg = ''){
        $msg = $msg != '' ? "\n  # WARNING: $msg\n" : '';

        echo $msg.'
  PHAR COMPILER

  Usage: php .start -m -q path/to/[project name].phar

  options:
  -h            this help
  -m            compile entire project path
  -d            show data from path/to/[name].phar
  -q            quiet mode
  ';
    }


    /* Calcula e gera uma saida em tempo HUMANO
     * entrada: $tm é indicado em milissegundos float [ex.: 2.980765 = 2 segundos e 980 milissegundos]
     */
    private function totalTime($tm){
         $mn = $sg = $ms = 0;
         $tm = 0 + $tm;

         //minutos
         if(intval($tm/60) > 0){
             $mn = intval($tm/60);
             $tm = $tm - ($mn * 60);
         }
         //segundos
         if(intval($tm) > 0){
             $sg = intval($tm);
             $tm = $tm - $sg;
         }
         //milissegundos
         $ms = intval($tm*1000);

         return "\n - Tempo total: "
              .($mn > 0 ? $mn.' minutos e ' : '')
              .($sg > 0 ? $sg.' segundos e ' : '')
              .($ms+1).' milissegundos.' ;
    }

}
