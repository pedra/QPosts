<?php

namespace Lib\Debug;
use Lib\Data\Persist;

class EException {

	static function Error($code, $msg, $file, $line, $obj){
        static::show($code, $msg, $file, $line);
    }        
        
    static function Exception(\Exception $e){
        static::show( $e->getCode(), 
                      $e->getMessage(), 
                      $e->getFile(), 
                      $e->getLine(), 
                      'Exception');
    }

    private static function show($code, $msg, $file, $line, $type = 'Error'){

        if($type == 'Error') header('HTTP/1.0 404 Not Found');
        else header('HTTP/1.1 500 Internal Server Error');

        //Request page not found
        if($code == E_USER_ERROR 
            && $msg == 'Page not found!') $img = self::infoImg();
        else {
            $tmp = self::errorType($code);
            $eType = $tmp['name'];
            $eText = $tmp['text'];
            $trace = self::formatTrace($file, $line);
            $debug = self::debug();
            $img   = self::errorImg();
        }

        //TODO : pegar uma página 404 do usuario em _file_exists('html/404.php');
        include __DIR__.'/page.php';
        exit();
    }


    static function debug(){
        if(!DEBUG) return '';

        $MEM = Persist::get('file_exists');
        
        $key = array_keys($MEM[0]);
        $dbg = '<h3>Debug:</h3><table><tr><th>'.$key[0].'</th><th>'.$key[1].'</th></tr>';

        foreach (Persist::get('file_exists') as $v) {
            $dbg .= '
            <tr>'.self::debugList($v).'</tr>';
        }
        return $dbg.'
        </table>';
    }

    static function debugList($arr){
        if(!is_array($arr)) return $arr;
        $dbg = '';
        foreach($arr as $k=>$v){
            $dbg .= '<td>'.self::debugList($v).'</td>';
        }
        return $dbg;
    }

    private static function formatTrace($file, $line){
        $trace = '<table>';
        $cont = 0;
        foreach (array_reverse(debug_backtrace(false)) as $row) {
            $cont ++;
            $trace .= '<tr><td><b>'.$cont.'.  </b></td><td>'.(isset($row['class']) ? $row['class'] : '&nbsp;&nbsp;').
                      (isset($row['type']) ? $row['type'] : '&nbsp;&nbsp;').
                      (isset($row['function']) ? $row['function'].'()' : '&nbsp;').
                      '</td><td>'.(isset($row['file']) ? str_replace(trim(ROOT, ' /'), '', $row['file']) : '').
                      (isset($row['line']) ? '['.$row['line'].']' : '').
                      '</td></tr>';
            if(isset($row['file']) && $row['file'] == $file && $row['line'] == $line) break;       
        }
        return $trace.'</table>';
    }


    private static function errorType($num){
        $manual = array(
            E_ERROR=>['name'=>'E_ERROR','text'=>'Erros fatais em tempo de execução.<br>Estes indicam erros que não podem ser recuperados, como problemas de alocação de memória. A execução do script é interrompida.'],
            E_WARNING => ['name'=>'E_WARNING', 'text'=>'Avisos em tempo de execução (erros não fatais).<br>A execução do script não é interrompida.'],
            E_PARSE=>['name'=>'E_PARSE','text'=>'Erro em tempo de compilação.<br>Erros gerados pelo interpretador.'],
            E_NOTICE=>['name'=>'E_NOTICE','text'=>'Notícia em tempo de execução.<br>Indica que o script encontrou alguma coisa que pode indicar um erro, mas que também possa acontecer durante a execução normal do script.'],
            E_CORE_ERROR=>['name'=>'E_CORE_ERROR','text'=>'Erro fatal que acontece durante a inicialização do PHP.<br>Este é parecido com E_ERROR, exceto que é gerado pelo núcleo do PHP.'],
            E_CORE_WARNING=>['name'=>'E_CORE_WARNING','text'=>'Avisos (erros não fatais) que aconteçam durante a inicialização do PHP.<br>Este é parecido com E_WARNING, exceto que é gerado pelo núcleo do PHP.'],
            E_COMPILE_ERROR=>['name'=>'E_COMPILE_ERROR','text'=>'Erro fatal em tempo de compilação.<br>Este é parecido com E_ERROR, exceto que é gerado pelo Zend Scripting Engine.'],
            E_COMPILE_WARNING=>['name'=>'E_COMPILE_WARNING','text'=>'Aviso em tempo de compilação.<br>Este é parecido com E_WARNING, exceto que é geredo pelo Zend Scripting Engine.'],
            E_USER_ERROR=>['name'=>'E_USER_ERROR','text'=>'Erro gerado pelo usuário.<br>Este é parecido com E_ERROR, exceto que é gerado pelo código PHP usando a função trigger_error().'], 
            E_USER_WARNING=>['name'=>'E_USER_WARNING','text'=>'Aviso gerado pelo usuário.<br>Este é parecido com E_WARNING, exceto que é gerado pelo código PHP usando a função trigger_error().'], 
            E_USER_NOTICE=>['name'=>'E_USER_NOTICE','text'=>'Notícia gerada pelo usuário.<br>Este é parecido com E_NOTICE, exceto que é gerado pelo código PHP usando a função trigger_error().'],
            E_STRICT=>['name'=>' E_STRICT','text'=>'Notícias em tempo de execução.<br>Permite ao PHP sugerir mudanças ao seu código as quais irão assegurar melhor interoperabilidade e compatibilidade futura do seu código.'],
            E_RECOVERABLE_ERROR=>['name'=>'E_RECOVERABLE_ERROR','text'=>'Erro fatal capturável.<br>Indica que um erro provavelmente perigoso aconteceu, mas não deixou o Engine em um estado instável. Se o erro não for pego por uma manipulador definido pelo usuário (veja também set_error_handler()), a aplicação é abortada como se fosse um E_ERROR.'],
            E_DEPRECATED=>['name'=>' E_DEPRECATED','text'=>'Avisos em tempo de execução.<br>Habilite-o para receber avisos sobre código que não funcionará em futuras versões.'],
            E_USER_DEPRECATED=>['name'=>'E_USER_DEPRECATED','text'=>'Mensagem de aviso gerado pelo usuário.<br>Este é como um E_DEPRECATED, exceto que é gerado em código PHP usando a função trigger_error().'],
            E_ALL=>['name'=>'E_ALL','text'=>'Todos erros e avisos, como suportado, exceto de nível E_STRICT no PHP < 6.']
            );
        return isset($manual[$num]) ? $manual[$num] : false;
    }

    private static function encodeImage ($filename=string,$filetype=string) {
        if ($filename) {
            $imgbinary = fread(fopen($filename, "r"), filesize($filename));
            return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
        }
    }

    private static function errorImg(){
        return 'data:image/png;base64,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';
    }

    private static function infoImg(){
        return 'data:image/png;base64,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';
    }

}

