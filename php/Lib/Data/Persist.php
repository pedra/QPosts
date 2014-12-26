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
use Lib\Dock;

class Persist
    extends Dock {

    private $filename = null;
    private $data = null;
    private $temp = [];
    private $session = [];

    /* Load data from json file
     *
     * ex.: Lib\Config::load(ROOT.'config.json');
     *
     */
    function load($filename = null){
        if($filename != null && file_exists($filename)){
            $this->filename = $filename;
            return $this->data = json_decode(file_get_contents($filename));
        }
    }

    //get file
    function getFileName(){
        return $this->filename;
    }

    /* Save data to json file type
     *
     * ex.: Lib\Config::save(ROOT.'testeJsonFile.json');
     *
     */
    function save($filename= null){
        if($filename== null) $filename= $this->filename;
        if(is_writable(dirname($filename))){
            file_put_contents($filename, json_encode($this->data));
        }else trigger_error ('Não é possivel salvar o arquivo "'.$filename.'".');
    }

    /* Static get node
     * ex.: $x = Lib\Config::get('database')->mysql->dsn;
     */
    function get($node = null){
        if($node == null) return $this->temp;
        if(!isset($this->temp[$node])) return false;
        return $this->temp[$node];
    }

    /* Static set new value
     * ex.: Lib\Config::set('database', 'new value');
     */
    function set($node, $value){
        $this->temp[$node] = $value;
    }


    /* Insert
     * Insert into array "name", index "index" & mixed value "value"
     */
    function insert($name, $index = null, $value){
        if($index == null) return $this->temp[$name][] = $value;
        return $this->temp[$name][$index] = $value;
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
        if(!isset($this->temp[$name])) $this->temp[$name] = null;

        //argument set
        $c = count($arguments) - 1;
        $t = [];
        if($c > 0) {
            $tmp = '$t';
            for($i = 0; $i < $c; $i++){
                $tmp .= '[\''.$arguments[$i].'\']';
            }
            eval($tmp." = '".$arguments[$c]."';");
            $this->temp[$name] = $t;
        }

        //return NODE
        return $this->temp[$name];
    }
}
