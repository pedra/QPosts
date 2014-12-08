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
use o;

class Assets {    
    
    //Max cache time
    private $lifeTime = 100000;

    
    /* setStyle
     * Modeling the assets output
     * ZoomTag format: <z::style file="main, lib/reset" cached="true" />
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

        //create cache file (if not exists)
        if($cached){
            if(!file_exists(ROOT.'assets/style/'.$cache) || (time() - filemtime(ROOT.'assets/style/'.$cache)) > $this->lifeTime) {            
                $dt = '';
                foreach ($files as $file) {
                    if(file_exists(ROOT.'assets/style/'.$file)) $pF = ROOT.'assets/style/'.$file;
                    elseif(file_exists(RPHAR.'assets/style/'.$file)) $pF = RPHAR.'assets/style/'.$file;
                    else continue;
                        $dt .= preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                               preg_replace('<\s*([@{}:;,]|\)\s|\s\()\s*>S','\1',
                               str_replace(array("\n","\r","\t"),'',
                               file_get_contents($pF))))."\n";
                }    
                file_put_contents(ROOT.'assets/style/'.$cache, $dt);                
            }
            $out = array(URL.'assets/style/'.$cache);
        } else {            
           foreach ($files as $file) { 
                $out[] = URL.'assets/style/'.$file;
            }
        }        
        return $out;
    }
    
    /* setScript
     * Modeling the assets output
     * ZoomTag format: <z::script file="main, lib/jquery/jquery-ui" cached="true" />
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

        //create cache file (if not exists)
        if($cached){
            if(!file_exists(ROOT.'assets/js/'.$cache) || (time() - filemtime(ROOT.'assets/js/'.$cache)) > $this->lifeTime) {          
                $dt = $environment;
                foreach ($files as $file) {
                    //if(file_exists(o::html('script').$file.'.js'))
                    if(file_exists(ROOT.'assets/js/'.$file)) $pF = ROOT.'assets/js/'.$file;
                    elseif(file_exists(RPHAR.'assets/js/'.$file)) $pF = RPHAR.'assets/js/'.$file;
                    else continue;
                        $dt .=  '/* '.$file.'.js */ '.
                                preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                                preg_replace("/^\s/m",'',
                                str_replace("\t",'',
                                file_get_contents($pF))))."\n";
                }    
                file_put_contents(ROOT.'assets/js/'.$cache, $dt);                
            }
            $out = array(URL.'assets/js/'.$cache);
        } else {            
           foreach ($files as $file) {
                $out[] = URL.'assets/js/'.$file;
            }
        }        
        return $out;
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
