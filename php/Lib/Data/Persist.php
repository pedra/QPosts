<?php
/* 
 * Copyright (C) 2014 http://plus.google.com/+BillRocha
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Persistent Manager 
 * 
 * set: Lib\Persist::set('name', 'content', [flag: session, all, none]); 
 * get: $value = Lib\Persist::get('name');
 *
 * @author http://google.com/+BillRocha
 */

namespace Lib\Data;

class Persist {
    
    static $filename = null;
    static $data = null;
    static $temp = ['HTML/school/host'=>URL];
    static $session = [];
    
    /* Load data from json file
     * 
     * ex.: Lib\Config::load(ROOT.'config.json');
     * 
     */
    static function load($filename = null){        
        if($filename != null && file_exists($filename)){
            static::$filename = $filename;
            return static::$data = json_decode(file_get_contents($filename));
        }
    }

    //get file
    static function getFileName(){
        return static::$filename;
    } 
    
    /* Save data to json file type
     * 
     * ex.: Lib\Config::save(ROOT.'testeJsonFile.json');
     * 
     */
    static function save($filename= null){
        if($filename== null) $filename= static::$filename;
        if(is_writable(dirname($filename))){
            file_put_contents($filename, json_encode(static::$data));
        }else trigger_error ('Não é possivel salvar o arquivo "'.$filename.'".');
    }

    /* Static get node
     * ex.: $x = Lib\Config::get('database')->mysql->dsn;
     */
    static function get($node = null){
        if($node == null) return static::$temp;
        if(!isset(static::$temp[$node])) return false;
        return static::$temp[$node];
    }
    
    /* Static set new value
     * ex.: Lib\Config::set('database', 'new value');
     */
    static function set($node, $value){
        static::$temp[$node] = $value;
    }


    /* Insert
     * Insert into array "name", index "index" & mixed value "value"
     */
    static function insert($name, $index = null, $value){
        if($index == null) return static::$temp[$name][] = $value;
        return static::$temp[$name][$index] = $value;
    }

    /* Static call for a existent node
     *
     * ex.: $temp->xnode = value;
     *      $var = CONS::xnode();
     *
     * 2 -  $temp->xnode->ynode->znode = value;
     *      $var = CONS::xnode()->ynode->znode;
     *
     * 3 -  $temp;
     *      CONS::xnode('value');           <<resulta->> $temp->xnode = value;
     *
     * 4 -  $temp->xnode = value;
     *      CONS::xnode()->ynode = value;   <<resulta->> $temp->xnode->ynode = value;
     *
     *
     */

    static function __callStatic($name, $arguments){
        //node $name not exists
        if(!isset(static::$temp[$name])) static::$temp[$name] = null;

        //argument set
        $c = count($arguments) - 1;
        $t = [];
        if($c > 0) {
            $tmp = '$t';
            for($i = 0; $i < $c; $i++){
                $tmp .= '[\''.$arguments[$i].'\']';
            }
            eval($tmp." = '".$arguments[$c]."';");
            static::$temp[$name] = $t;
        }

        //return NODE
        return static::$temp[$name];
    }
}
