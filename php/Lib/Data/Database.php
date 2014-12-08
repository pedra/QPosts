<?php
/**
 * Description of Database
 *
 * @author http://plus.google.com/+BillRocha
 * 
 * Database Configuration
 * ex.:
 *
 *  Lib\Database::this(null, Lib\Config::get('database'));
 *  Lib\Database::this('books', Lib\Config::get('database'));
 *
 *  $users = Lib\Database::this('users', Lib\Config::get('database'));
 *
 *  Lib\Database::this()->sql('SELECT * FROM TABELA');
 *  Lib\Database::this('docs')->sql('SELECT * FROM documents WHERE livro = "Código de Etica"');
 *  $users->sql('SELECT * FROM USERS');
*/

namespace Lib\Data;
use PDO;

class Database {

    private $sql    = null;
    private $result = null;
    
    static $dsn = false;
    static $user = false;
    static $passw = false;
    static $conn = false;
    static $ME = ['default'=>null];
    
    
    //Static Factory method
    static function this($name = null, $cfg = null){
        if($name == null) $name = 'default';
        if($cfg == null && is_object(static::$ME[$name])) 
            return static::$ME[$name];
        return static::$ME[$name] = new Db($cfg);
    }
    
    function __construct($cfg = null){
        if(is_object($cfg) && isset($cfg->default)){
            $cfg = $cfg->{$cfg->default};            
            if(isset($cfg->dsn)) static::$dsn = $cfg->dsn;
            if(isset($cfg->user)) static::$user = $cfg->user;
            if(isset($cfg->passw)) static::$passw = $cfg->passw;
            static::$conn = false;
        }        
        if(!static::$dsn) trigger_error ('Configuração do banco de dados não encontrada.', E_USER_ERROR);
    }
    
    function connect(){
        if(static::$conn == false){
            static::$conn = new PDO(static::$dsn, static::$user, static::$passw);
        }
        if(!is_object(static::$conn))
                trigger_error('I can not connect to the database',E_USER_ERROR);
        return static::$conn;
    }
    
    function disConnect(){
        static::$conn = false;
    }
    
    function sql($sql = ''){
        return $this->sql = $sql;
    }
    
    function query($sql,$parms = array()){
        $this->sql = $sql;
        $sth = $this->connect()->prepare($sql);
        $sth->execute($parms);
        return $this->result = $sth->fetchAll(PDO::FETCH_CLASS,"Lib\Data\Row");
    }
    
    //Result Object
    function result(){
        return $this->result;
    }
    //Limpa os resultados
    function clear(){
        $this->result = new Result();
    }
    
    //TODO implementar ---------------------------------------------------------
    
    //Salva no banco de dados os dados do Result (alterados) - equivalente a "UPDATE" 
    function save(){}
    
    //Salva uma nova linha no BD - equivalente a "INSERT INTO"
    function insert(){}
}

/**
 * Description of Row
 *
 * @author http://plus.google.com/+BillRocha
 */
class Row {

    private $__table = null;
    private $__where = null;
    private $id = null;
    
    
    function __construct(){
        //TODO: null
    }

    
    //Salva os dados no banco de dados [insert/update] 
    function save(){
        //if($this->id == null) //INSERT INTO
        //else //UPDATE

        /* ex.: INSERT INTO ($this->__table) SET ($this->$key) = ($this->$value)
         *      UPDATE FROM ($this->__table) VALUES(($this->$key) = ($this->$value)) WHARE ($this->__where)
         *
         *      in foreach: bypass $__table and $__whare !!
         */
    }

    
    /** GET
     * Get parameter value
     * @param string $parm
     * @return boolean
     */
    function get($parm){
        if(isset($this->$parm)) return $this->$parm;
        return false;        
    }

    //Return $this as array
    function getArray(){
        foreach ($this as $k=>$v) {
            $a[$k] = $v;
        }
        return $a;
    }
    
    /** SET
     * Set parameter
     * @param string|array $parm Name of parameter or array of parameter name and value
     * @param mixed $value Value of parameter
     * @return boolean
     */
    function set($parm, $value = null){
        if(is_array($parm)){
            foreach($parm as $k=>$v){ $this->$k = $v; }
            return $this;
        }
        elseif(isset($this->$parm)) {
            $this->$parm = $value;
            return $this;
        }
        else return false;
    }

}

