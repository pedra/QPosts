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
 * usage: Lib\Config::this()->load(configFile.json);
 *
 * get: $x = (new Lib\Config())->database->mysql->dsn;
 *      $x = Lib\Config::this()->get('database')->mysql->dsn;
 *
 * set: Lib\Config::this()->set('database', 'new value');
 *
 * Usando a "Doca":
 *      Dock::Config()->load('config.json');
 *
 * @author Paulo
 */

namespace Lib\Data;
use Lib\Dock;

class Config
    extends Dock {

    private $file = null;
    private $data = null;


    function __construct($file = null){
        $this->load($file);
    }

    /* Load data from json file
     *
     * ex.: Lib\Config::this()->load(ROOT.'config.json');
     *
     */
    function load($file = null){
        if($file != null && file_exists($file)){
            $this->file = $file;
            return $this->data = json_decode(file_get_contents($file));
        }
    }

    //get file
    function getFile(){
        return $this->file;
    }

    /* Save data to json file type
     *
     * ex.: Lib\Config::this()->save(ROOT.'testeJsonFile.json');
     *
     */
    function save($file = null){
        if($file == null) $file = $this->file;
        if(is_writable(dirname($file))){
            file_put_contents($file, json_encode($this->data));
        }else trigger_error ('Não é possivel salvar o arquivo "'.$file.'".');
    }

    /* Get node
     * ex.: $x = Lib\Config::this()->get('database')->mysql->dsn;
     */
    function get($node = null){
        if($node == null) return $this->data;
        if(!isset($this->data->$node)) return false;
        return $this->data->$node;
    }

    /* Set new value
     * ex.: Lib\Config::this()->set('database', 'new value');
     */
    function set($node, $value){
        if(isset($this->data->$node)){
            $this->data->$node = $value;
        }
    }
}
