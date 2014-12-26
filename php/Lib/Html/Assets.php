<?php
/**
 * Template Assets
 * @copyright   Bill Rocha - http://billrocha.tk
 * @license		http://billrocha.tk/license
 * @author		Bill Rocha - prbr@ymail.com
 * @version		0.0.1
 * @package		Start\Html
 * @access 		public
 * @since		0.0.1
 */

namespace Lib\Html;
use Lib\Dock;

class Assets
    extends Dock {

    //config file data [html.ini]
    private $config = [];

    //Max cache time
    private $lifeTime = 100000;

    //URL access for Styles
    private $urlStyle = null;

    //ROOT path for Styles
    private $rootStyle = null;

    //URL access for Scripts
    private $urlScript = null;

    //ROOT path for Scripts
    private $rootScript = null;


    function __construct($config){
        $this->config = $config;
        $this->urlStyle = $this->config['urlStyle'];
        $this->rootStyle = $this->config['rootStyle'];
        $this->urlScript = $this->config['urlScript'];
        $this->rootScript = $this->config['rootScript'];
    }


    /* setStyle
     * Modeling the assets output
     * xTag format: <x::style file="main.css, lib/reset.css" cached="true" media="All" />
     *
     */
    function setStyle($sTag){
        //Se não for indicado 'file' ...
        if(!isset($sTag['file'])) return false;
        $cached = (isset($sTag['cached']) && trim($sTag['cached']) == 'true') ? true : false;

        $tmp = explode(',', trim($sTag['file'], ' /'));
        foreach($tmp as $i){
            $tmp = trim($i, ' /');
            if($tmp != '') $files[] = $tmp;
        }

        //add others ...
        /*
        $config = o::get('style');
        if($config){
           $files = array_merge($files, $config);
        }*/

        $cache = md5(implode('_', $files)).'.cache.css';
        $o = '';
        $attr = isset($sTag['media']) ? ' media="'.$sTag['media'].'"':'';

        //create cache file (if not exists)
        if($cached){
            if(!file_exists($this->rootStyle.$cache) || (time() - filemtime($this->rootStyle.$cache)) > $this->lifeTime) {
                $dt = '';
                foreach ($files as $file) {
                    $pF = _file_exists($this->rootStyle.$file);
                    if($pF === false) continue;
                    $dt .= preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                           preg_replace('<\s*([@{}:;,]|\)\s|\s\()\s*>S','\1',
                           str_replace(array("\n","\r","\t"),'',
                           file_get_contents($pF))))."\n";
                }
                file_put_contents($this->rootStyle.$cache, $dt);
            }
            $o = '<link href="'.$this->urlStyle.$cache.'"  rel="stylesheet" type="text/css"'.$attr.'/>'."\n";
        } else {
           foreach ($files as $file) {
                $o .= '<link href="'.$this->urlStyle.$file.'"  rel="stylesheet" type="text/css"'.$attr.'/>'."\n";
            }
        }
        return $o;
    }

    /* setScript
     * Modeling the assets output
     * xTag format: <x::script file="main.js, lib/jquery/jquery-ui.js" cached="true" />
     *
     */
    function setScript($sTag){
        //Se não for indicado 'file' ...
        if(!isset($sTag['file'])) return false;
        $cached = (isset($sTag['cached']) && trim($sTag['cached']) == 'true') ? true : false;

        $tmp = explode(',', ','.trim($sTag['file'], ' /'));
        foreach($tmp as $i){
            $tmp = trim($i, ' /');
            if($tmp != '') $files[] = $tmp;
        }

        //add others ...
        /*
        $config = o::get('script');
        if($config){
           $files = array_merge($files, $config);
        }*/

        //$url = URL;
        //$environment = '/*environment*/ var URL="'.URL.'"; var URL_IMG="'.$url['image'].'";var URL_AJAX="'.$url['ajax'].'";var URL_SCRIPT="'.$url['script'].'";var URL_STYLE="'.$url['style'].'";'."\n\n";
        $environment = '/*environment*/ var URL="'.URL.'";'."\n\n";

        $cache = md5(implode('_', $files)).'.cache.js';
        $o = '';

        //create cache file (if not exists)
        if($cached){
            if(!file_exists($this->rootScript.$cache) || (time() - filemtime($this->rootScript.$cache)) > $this->lifeTime) {
                $dt = $environment;
                foreach ($files as $file) {
                    $pF = _file_exists($this->rootScript.$file);
                    if($pF === false) continue;
                    $dt .=  '/* '.$file.'.js */ '.
                            preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                            preg_replace("/^\s/m",'',
                            str_replace("\t",'',
                            file_get_contents($pF))))."\n";
                }
                file_put_contents($this->rootScript.$cache, $dt);
            }
            $o = '<script type="text/javascript" src="'.$this->urlScript.$cache.'"></script>'."\n";
        } else {
           foreach ($files as $file) {
                $o .= '<script type="text/javascript" src="'.$this->urlScript.$file.'"></script>'."\n";
            }
        }
        return $o;
    }


    /* setLifeTime
     * Max cache time
     */
    function setLifeTime(int $time){
        return $this->lifeTime = $time;
    }

    /* getLifeTime
     * Max cache time
     */
    function getLifeTime(){
        return $this->lifeTime;
    }

}
