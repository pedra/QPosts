<?php


namespace Module;
use Lib\Dock;

abstract class AbstractModule
    extends Dock {


    function __construct($data){

        foreach($data as $key=>$val){

            $this->{trim($key, ' -')} = $val;
        }
    }


    abstract protected function render();
}