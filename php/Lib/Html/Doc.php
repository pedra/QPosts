<?php

/**
 * Layout HTML Container
 * @copyright   Bill Rocha - http://billrocha.tk
 * @license		http://billrocha/license
 * @author		Bill Rocha - prbr@ymail.com
 * @version		0.0.1
 * @package		Start\Html
 * @access 		public
 * @since		0.0.1
 */

namespace Lib\Html;

class Doc
    extends Render {

    protected $config = [];
    protected $pieces = [];
    protected $values = [];
    protected $path = '';
    protected $pharPath = '';
    protected $htmlCache = '';
    protected $htmlCacheTime = 100;
    protected $template = '';
    protected $ext = '.html';
    protected $content = '';
    protected $renderized = false;
    protected $tag = 'x:';

    //Inicializa e carrega o arquivo indicado
    function __construct($template = null) {
        //Load html.ini
        if(_file_exists(__DIR__.'/html.ini') === false) trigger_error("Config file 'html.ini' not found! || 1");
        $this->config = parse_ini_file(__DIR__.'/html.ini', true);

        $this->values['host'] = URL;
        $this->path = $this->config['htmlPath'];
        $this->pharPath = RPHAR.'html/';
        $this->htmlCache = $this->config['htmlCache'];
        $this->htmlCacheTime = $this->config['htmlCacheTime'];

        //criando os diretórios necessários
        if(!is_dir($this->path)) mkdir($this->path, 0777, true);
        if(!is_dir($this->htmlCache)) mkdir($this->htmlCache, 0777, true);

        $this->template = $template;
        $this->loadTemplate($template);
    }

    //Carrega uma partição HTML
    function insert($filename, $name = null) {
        if ($name === null)
            $name = $filename;
        $this->pieces[$name] = new Doc($filename);
        return $this->pieces[$name];
    }

    //Registra uma variável para o Layout
    function value($name, $value = null) { return $this->val($name, $value = null);}
    function val($name, $value = null) {
        if(is_string($name)) $this->values[$name] = $value;
        if(is_array($name)) $this->values = array_merge($this->values, $name);
        return $this;
    }

    //Processa todo o HTML
    function render($php = false, $blade = false, $sTag = true) {
        //Renderiza todas os fragmentos HTML injetados
        foreach ($this->pieces as $piece) {
            $piece->render($php, $blade, $sTag);
        }
        //Renderizando o Layout
        $this->content = $this->produce($php, $blade, $sTag);
        return $this;
    }

    /* SEND
     * Send headers & Output tris content
     *
     */
    function send() {
        ob_end_clean();
        ob_start('ob_gzhandler');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Cache-Control: must_revalidate, public, max-age=31536000');
        header('Server: START/1.3.0');//for safety ...
        header('X-Powered-By: START/1.3.0');//for safety ...
        exit($this->content . $this->statusBar());
    }


    //private/protected functions --------------------------------------------¬

    /* Status Bar
     * TODO : Criar o carregamento e compressão de arquivos CSS/JS para incluir os da barra de status.
     *
     * @return string Html status bar.
     */

    private function statusBar(){
        if(!DEBUG) return '';
        $tm = explode(' ', microtime());
        $tm = $tm[0] + $tm[1];
        $tm = intval(($tm - $_SERVER['REQUEST_TIME_FLOAT']) * 1000) . ' ms';
        $inphar = RPHAR ? ' ( '.RPHAR.' )' : '';
        return '<div style="position:fixed;bottom:0;right:0;background:#999;color:#000;font-family:\'Oxygen Mono\',monospace;padding:0 5px;font-size:11px;font-weight:normal;font-style:italic;text-shadow:1px 1px 1px #DDD">Running'.$inphar.' at '.$tm.'</div>';
    }

    //load template file contents (HTML)
    private function loadTemplate(){
        if(!is_string($this->template)) return false;
        if(file_exists($this->path.$this->template.$this->ext)) $file = $this->path.$this->template.$this->ext;
        elseif(file_exists($this->pharPath.$this->template.$this->ext)) $file = $this->pharPath.$this->template.$this->ext;
        else trigger_error ('Template "'.$this->template.'" not exists!!||2');

        $this->content = str_replace(["\r","\n","\t",'  '],'', file_get_contents($file));
        return $this;
    }

    //Pega uma variável ou todas
    protected function getVar($var = null) {
        return ($var == null) ? $this->values : (isset($this->values[$var]) ? $this->values[$var] : false);
    }

    //Insere o conteúdo processado Html
    protected function setContent($content) {
        $this->content = $content;
        return $this;
    }

    //Pega o conteúdo processado Html
    protected function getContent() {
        return $this->content;
    }

}
