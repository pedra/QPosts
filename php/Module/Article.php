<?php

namespace Module;

class Article 
    extends AbstractModule
    implements InterfaceModule {



    function render(){
        return 'Módulo Carregado.';
    }

    function getTitle(){
        return 'Variáveis: <pre>'.print_r($this, true).'</pre>';
    }

    function getContent(){
        return 'Conteúdo da matéria/artigo!';
    }
}