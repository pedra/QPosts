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
 * Configuração geral
 * 
 * usage: Lib\Config::load(configFile.json);
 * 
 * get: $x = (new Lib\Config())->database->mysql->dsn;
 *      $x = Lib\Config::get('database')->mysql->dsn;
 * 
 * set: Lib\Config::set('database', 'new value');
 *
 * @author Paulo
 */

namespace Lib\Data;

class Config {
    
    static $file = null;
    static $data = null;
    
    /* Load data from json file
     * 
     * ex.: Lib\Config::load(ROOT.'config.json');
     * 
     */
    static function load($file = null){        
        if($file != null && file_exists($file)){
            static::$file = $file;
            return static::$data = json_decode(file_get_contents($file));
        }
    }

    //get file
    static function getFile(){
        return static::$file;
    } 
    
    /* Save data to json file type
     * 
     * ex.: Lib\Config::save(ROOT.'testeJsonFile.json');
     * 
     */
    static function save($file = null){
        if($file == null) $file = static::$file;
        if(is_writable(dirname($file))){
            file_put_contents($file, json_encode(static::$data));
        }else trigger_error ('Não é possivel salvar o arquivo "'.$file.'".');
    }

    /* Static get node
     * ex.: $x = Lib\Config::get('database')->mysql->dsn;
     */
    static function get($node = null){
        if($node == null) return static::$data;
        if(!isset(static::$data->$node)) return false;
        return static::$data->$node;
    }
    
    /* Static set new value
     * ex.: Lib\Config::set('database', 'new value');
     */
    static function set($node, $value){
        if(isset(static::$data->$node)){
            static::$data->$node = $value;
        }
    }
}
