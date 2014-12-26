<?php

namespace Module;

class Banner 
    extends AbstractModule
    implements InterfaceModule {



    function render(){
        _loadStyle('banner');
        _loadScript('banner');

        return '<div'.(isset($this->class) ? ' class="banner"' : '').'>Módulo <b>BANNER</b> Carregado.</div>';
    }

    function getTitle(){
        return 'Variáveis: <pre>'.print_r($this, true).'</pre>';
    }

    function getContent(){
        return 'Conteúdo da matéria/artigo!';
    }
}