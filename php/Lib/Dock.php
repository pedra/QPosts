<?php

/* Doca de "ancoragem" e criação de objetos do sistema
 * TODO: translation to the english
 *
 *
 * @author http://google.com/+BillRocha
 */


namespace Lib;

abstract class Dock {


    /**
     * referencia estática a própria classe!
     * Todas as classes que "extends" essa BASE armazenam sua instância singleton neste array.
     */
    protected static $__doca = [];
    protected static $__serialize = [];


    /**
     * Construtor singleton da própria classe.
     * Acessa o método estático para criar uma instância da classe automáticamente.
     *
     * @param string $class Classe invocada.
     * @return object this instance
    */
    final public static function this($arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null){
        $class = get_called_class();
        if (!isset(static::$__doca[$class])) static::$__doca[$class] = new static($arg1, $arg2, $arg3, $arg4, $arg5, $arg6);
        return static::$__doca[$class];
    }

    /**
     * Simples setter!.
     * Acessa e modifica um atributo privado ou público da classe.
     *
     * @param string $var nome do atributo.
     * @param mixed $val novo valor do atributo.
     * @return mixed|null retorna o valor modificado ou null se o atributo não for acessível (não existir).
    */
    static function _set($var, $val){
        return self::this()->$var = $val;
    }

    /**
     * Simples getter!.
     * Retorna o valor de um atributo privado ou público da classe.
     *
     * @param string $var nome do atributo.
     * @return mixed|null retorna o valor ou null se o atributo não for acessível (não existir).
    */
    static function _get($var = null){
        if($var == null) return self::this();//retorna TODOS os argumentos da classe
        if(isset(self::this()->$var)) return self::this()->$var;
        return null;
    }

    /**
     * Usado para capturar a requisição de um objeto estacionado
     * Uso: $config &= Dock::Config(); // retirna a instãncia do objeto Lib\Data\Config.
     *
     */
    static function __callStatic($name, $arguments){
        if(isset(static::$__doca[$name])) return static::$__doca[$name];
        return false;
    }

    /**
     * Estacionamento de Objeto
     * 'Parkeia' (estaciona) um objeto na 'doca'.
     * Um erro serpa gerado pelo sistema se a classe não for encontrada pelo autoload do sistema.
     *
     * Ex.: Dock::park('Lib\Html\Doc', 'HTML', arg1, arg2, ...);
     * -- Equ: $html = new Lib\Html\Doc(arg1, arg2, ...);
     * -- Uso: Dock::HTML()->someMethod();
     *
     *
     * @param string $class nome da classe reconhecido pelo autoload do sistema;
     * @param string $key   nome de chamada da classe (alias);
     * @param mixed $args   qualquer número de argumentos a serem repassados para o '__construct' do objeto instanciado;
     * @return object retorna o objeto instanciado.
    */
    final static function park($class, $key = null){
        if($key != null && isset(static::$__doca[$key])) return static::$__doca[$key];
        if($key == null) $key = $class;
        static::$__doca[$key] = new $class();

        if(method_exists(static::$__doca[$key], '__construct')) {
            $args = func_get_args();
            array_shift($args); //remove '$class' argument
            array_shift($args); //remove '$key' argument
            call_user_func_array(array(static::$__doca[$key], '__construct'), $args);
        }
        return static::$__doca[$key];
    }


    final static function __serialize(){
        foreach (static::$__doca as $k=>$o) {
            $ser[$k] = gzcompress(serialize($o), 9);
        }
        return gzcompress(serialize($ser));
    }

    final static function __unserialize($ser){
        $ser = unserialize(gzuncompress($ser));
        if(!is_array($ser)) return false;

        foreach($ser as $k=>$o){
            static::$__doca[$k] = unserialize(gzuncompress($o));
        }
    }

    final static function __dockDebug(){
        return static::$__doca;
    }
}