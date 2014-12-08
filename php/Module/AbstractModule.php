<?php


namespace Module;

abstract class AbstractModule {


    function __construct($data){

        foreach($data as $key=>$val){

            $this->{trim($key, ' -')} = $val;
        }
    }


    abstract protected function render();
}