<?php
/**
 * Template Render
 * @copyright Bill Rocha - http://billrocha.tk
 * @license		http://billrocha/license
 * @author		Bill Rocha - prbr@ymail.com
 * @version		0.0.1
 * @package		Start\Html
 * @access 		public
 * @since     0.0.1
 */

namespace Lib\Html;
use Lib\Dock;

class Render
	extends Dock {

	private $assets = null;
	private $modules = [];
	private $plugins = [];


	/**
	 * Renderiza o arquivo html.
	 * Retorna um array com o produto da renderização ou 'ecoa' o resultado.
	 *
	 * @param bool $get	Retorna o produto da renderização para um pós-tratamento
	 * @return array|void
	*/
	function produce($php = false, $brade = false, $sTag = true){
		//With blade ???
		if($brade) $this->setContent($this->blade($this->getContent()));

		//With ©sTag ???
		if($sTag) {
			$ponteiro = -1;
			$content = $this->getContent();

			//Loop de varredura para o arquivo HTML
			while($ret = $this->sTag($content, $ponteiro)){
				$ponteiro = 0 + $ret['-final-'];
				$vartemp = '';

				//constant URL
				if($ret['-tipo-'] == 'var' && $ret['var'] == 'url') $vartemp = URL;
				elseif (method_exists($this, '_' . $ret['-tipo-'])) $vartemp = $this->{'_' . $ret['-tipo-']}($ret);

				//Incluindo o bloco gerado pelas ©sTags
				$content = substr_replace($this->getContent(), $vartemp, $ret['-inicio-'], $ret['-tamanho-']);
				$this->setContent($content);

				//RE-setando o ponteiro depois de adicionar os dados acima
				$ponteiro = strlen($vartemp) + $ret['-inicio-'] -1;
			}//end while
		}//end ©sTag

		//Eval PHP in HTML
		if($php) $this->evalPHP();

		//returns the processed contents
		return $this->getContent();
	}


	/**
	 * Scaner for ©sTag
	 * Scans the file to find a ©STAG - returns an array with the data found ©sTag
	 *
	 * @param string $arquivo	file content
	 * @param string $ponteiro	file pointer
	 * @param string $tag	©sTag to scan
	 * @return array|false     array with the data found ©sTag or false (not ©sTag)
	*/

	function sTag(&$arquivo, $ponteiro = -1, $tag = null){
		if($tag == null) $tag = $this->tag;
		$inicio = strpos($arquivo, '<'.$tag, $ponteiro + 1);
		if($inicio !== false){
			//get the type (<s:tipo ... )
			$x = substr($arquivo, $inicio, 25);
			preg_match('/(?<tag>\w+):(?<type>\w+|[\:]\w+)/', $x, $m);
			if(!isset($m[0])) return false;

			$ntag = $m[0];
			//the final ...
			$ftag = strpos($arquivo, '</' . $ntag . '>', $inicio);
			$fnTag   = strpos($arquivo, '/>', $inicio);
			$fn   = strpos($arquivo, '>', $inicio);

			//not  /> or </s:xxx>  = error
			if($fnTag === false && $ftag === false) return false;

			if($ftag !== false ) {
				if($fn !== false && $fn < $ftag){
					$a['-content-'] = substr($arquivo, $fn+1, ($ftag - $fn)-1);
					$finTag = $fn;
					$a['-final-'] = $ftag + strlen('</'.$ntag.'>');
				} else return false;
			} elseif($fnTag !== false) {
				$a['-content-'] = '';
				$finTag = $fnTag;
				$a['-final-'] = $fnTag + 2;
			} else return false;

			//catching attributes
			preg_match_all('/(?<att>\w+)="(?<val>.*?)"/', substr($arquivo, $inicio, $finTag - $inicio), $atb);

			if(isset($atb['att'])){
				foreach ($atb['att'] as $k=>$v){
					$a[$v] = $atb['val'][$k];
				}
			}

			//block data
			$a['-inicio-'] = $inicio;
			$a['-tamanho-'] = ($a['-final-'] - $inicio);
			$a['-tipo-'] = 'var';

			if(strpos($m['type'], ':') !== false) $a['-tipo-'] = str_replace (':', '', $m['type']);
			else $a['var'] = $m['type'];

			return $a;
		}
		return false;
	}

	/**
	 * Scaner para Blade.
	 * Retorna o conteúdo substituindo variáveis BLADE (@var_name).
	 *
	 * @param string $arquivo Conteúdo do arquivo a ser 'scaneado'
	 * @return string         O mesmo conteudo com variáveis BLADE substituídas
	*/
	function blade($arquivo){
		$t = strlen($arquivo) - 1;
		$ini = '';
		$o = '';

		for($i =0; $i <= $t; $i++){

			if($ini != '' && $ini < $i){
				if($arquivo[$i] == '@' && ($i - $ini) < 2) {
					$o .= '@';
					$ini = '';
					continue;
				}
				if(!preg_match("/[a-zA-Z0-9\.:\[\]\-_()\/'$+,\\\]/",$arquivo[$i])){
					$out1 = substr($arquivo, $ini+1, $i-$ini-1);
					$out = rtrim($out1, ',.:');
					$i += (strlen($out) - strlen($out1));

					if($this->getVar($out)) $out = $this->getVar($out);
					else {
						restore_error_handler();
						ob_start();
						$ret = eval('return '.$out.';');
						if(ob_get_clean() === '') $out = $ret;
						else $out = '';
					}
					$o .= $out; //exit($o);
					$ini = '';
					if($arquivo[$i] != ' ') $i --;//retirando espaço em branco...
				}
			} elseif($ini == '' && $arquivo[$i] == '@') $ini = $i;
			else $o .= $arquivo[$i];
		}//end FOR
		return $o;
	}


	//################################ ©sTagS #######################################

	/**
	 * evalPHP :: Rum PHP tag for contents.
	 *
	 * @param none
	 * @return string
	*/
	function evalPHP() {
		extract($this->getVar());
		ob_start();
		eval('?>' . $this->getContent());

		//pegando o conteúdo processado
		$this->setContent(ob_get_contents());
		ob_end_clean();
	}

	/**
	 * ClearData :: Clear all extra data.
	 *
	 * @param array $ret Starttag data array.
	 * @return array Data array cleared.
	*/
	function clearData($ret){
		unset($ret['var'], $ret['-inicio-'], $ret['-tamanho-'], $ret['-final-'], $ret['-tipo-'], $ret['-content-'], $ret['tag']);
		return $ret;
	}


	/**
	 * _list :: Create ul html tag
	 * Parameter "tag" is the list type indicator (ex.: <s:_list  . . . tag="li" />)
	 *
	 * @param array $ret ©sTag data array
	 * @return string|html
	*/
	function _list($ret){
		if(!isset($ret['var'])) return '';
		$v = $this->getVar(trim($ret['var']));
		if(!$v || !is_array($v)) return '';

		$tag = isset($ret['tag']) ? $ret['tag'] : 'li';
		$ret = $this->clearData($ret);

		//Tag UL and params. (class, id, etc)
		$o = '<ul';
		foreach($ret as $k=>$val){
			$o .= ' '.trim($k).'="'.trim($val).'"';
		}
		$o .= '>';
		//create list
		foreach ($v as $k=>$val){
			$o .= '<'.$tag.'>'.$val.'</'.$tag.'>';
		}
		return $o . '</ul>';
	}

	/**
	* ©sTag :: Insere um elemento "select"
	*
	* @param array $ret dados da ©sTag
	* @return string|html
	*/
	function _select($ret){
		if(!isset($ret['var'])) return '';
		$v = $this->getVar(trim($ret['var']));
		if(!$v || !is_array($v)) return '';

		$ret = $this->clearData($ret);
		$temp = '<select';

		if($v != ''){
			foreach($ret as $key => $value){
				if(trim($key) == 'multiple') $temp .= ' '.trim($key);
				else $temp .= ' '.trim($key).'="'.trim($value).'"';
				unset($ret[$key]);
			}
			$temp .= '>';

			foreach($v as $k => $vl){
				$temp .= '<option value="'.$k.'"';
				if(is_array($vl)) $temp .= ' selected="selected">'.$vl[0].'</option>';
				else $temp .= '>'.$vl.'</option>';
			}
			$temp .= '</select>';
		}
		return $temp;
	}

	/**
	 * ©sTag :: Carregando um arquivo JavaScript
	 *
	 * @param array $ret dados do arquivo JS vindos da ©sTag
	 * @return boll
	*/
	function _script($ret){
		$as = (is_object($this->assets) ? $this->assets : $this->assets = new Assets($this->config));
		return $as->setScript($this->clearData($ret));
	}

	/**
	 * ©sTag :: Carregando um arquivo CSS (na tag head)
	 *
	 * @param array $ret dados do arquivo CSS vindos da ©sTag
	 * @return boll
	*/
	function _style($ret){
		$as = (is_object($this->assets) ? $this->assets : $this->assets = new Assets($this->config));
		return $as->setStyle($this->clearData($ret));
	}

	/**
	 * ©sTag :: Carrega uma view pre-renderizada (subview)
	 *
	 * @param array $ret dados da ©sTag.
	 * @return string   Content html or empty string.
	*/
	function _html($ret){
		if(!isset($ret['file'])) return '';

		$cached = isset($ret['cached']) ? trim($ret['cached']) : false;
		$file = trim($ret['file'], ' \\/');
		$sfile = $this->htmlCache.str_replace(['\\','/'], '_', $file).'.html';

		if($cached == 'true') {
			$sf = _file_exists($sfile);

			if($sf !== false) {
				$tm = stat($sf);
				$tm = $tm['mtime'];

				//Se o tempo de cache ainda não foi atingido:
				if(($tm + $this->htmlCacheTime) > time()) return '<!-- cached: '.date('Y.m.d H:i:s',$tm).' -->'.file_get_contents($sf);
			}
		}

		//Se for configurado como Static:
		if($cached == 'static'){
			$sf = _file_exists($sfile);
			if($sf !== false) return '<!-- static page -->'.file_get_contents($sf);
		}

		//Renderizando o HTML
		$f = _file_exists('html/'.$file.'.html');
		if($f !== false){
			$doc = new Doc($file);
			$doc->value($this->values);
			$content = $doc->render()->getcontent();

			//save cache html content
			if($cached != false) file_put_contents($sfile, $content);

			return $content;
		}
		return '';
	}

	/**
	 * _var
	 * Insert variable data assigned in view
	 * Parameter "tag" is the tag type indicator (ex.: <s:variable  . . . tag="span" />)
	 *
	 * @param array $ret ©sTag data array
	 * @return string   Renderized Html
	*/
	function _var($ret) {
		$v = $this->getVar(trim($ret['var']));
		if(!$v) return '';

		//List type
		if(is_array($v)) return $this->_list($ret);

		$tag = isset($ret['tag']) ? $ret['tag'] : 'span';
		$ret = $this->clearData($ret);

		//Var span (with class, id, etc);
		if(count($ret) > 0) {
			$d = '<'.$tag;
			foreach ($ret as $k=>$val){
				$d .= ' '.trim($k).'="'.trim($val).'"';
			}
			$v = $d.'>'.$v.'</'.$tag.'>';
		}
		return $v;
	}

	/**
	 * Plugin / Module
	 * Return renderized data for the indicated plugin or module
	 *
	 * Ex.: <x::module name="Article" action="getTitle" class="aTitle" tag="h1" />
	 *   => <h1 class="aTitle">Title of Article</h1>
	 *
	 * @param array $ret ©sTag data
	 * @return string|html Renderized content
	*/
	final function _plugin($ret){return $this->_module($ret);}
	final function _module($ret){
		if(!isset($ret['name'])) return '';
		$module = '\\Module\\'.ucfirst($ret['name']);

		//clear data...
		unset($ret['var'], $ret['-inicio-'], $ret['-tamanho-'], $ret['-final-'], $ret['-tipo-']);

		//object node point
		if(!isset($this->modules[$module])) $this->modules[$module] = new $module($ret);

		//running action
		if(isset($ret['action'])
			&& $ret['action'] != ''
			&& method_exists($this->modules[$module], $ret['action']))
				return $this->modules[$module]->{$ret['action']}();
		else return $this->modules[$module]->render();
	}
}
