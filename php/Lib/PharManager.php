<?php

namespace Lib;
use Phar;

class PharManager{

	private $phar = null; 		//complete path from PharFile (string)
	private $source = [];		//files and/or directorys to compilation (array)
	private $compress = false;	//flag compress (bool)
	private $stripPHP = false;	//flag: stripPHP files (similar -w) (bool)
	private $logFile = false;	//file for insertion of log (string)
	private $node = null;		//mount point for Phar object (object)
	private $stub = true;		//indicates a name of the start (index) file (string/bool)
	private $error = false;
	private $quiet = false;


	function __construct($phar = false){
		if($phar) $this->phar = $phar;
	}


	/* SETS
	 *
	 */
	function __set($name, $value){
		if($name == 'node' || !isset($this->$name)) return false;
		if(($name == 'phar' || $name == 'logFile') && !is_string($value)) return false;
		if($name == 'source' && !is_array($value)) return false;
		if(($name == 'compress' || $name == 'stripPHP') && !is_bool($value)) return false;
		//else:
		$this->$name = $value;
		return $this;
	}

	/* GETs
	 *
	 */
	function __get($name){
		if($name == 'node' || !isset($this->$name)) return false;
		//else:
		return $this->$name;
	}


	//Get/set/reset Error
	function setError($e = false){
		$this->error = $e;
	}
	function getError(){
		return $this->error;
	}
	

	/* SHOW
	 * show data from Phar file
	 * 
	 * @parm file string 	path/to/file.phar [optional]
	 *
	 */

	function show($file = null){

		if($file != null) $this->phar = $file;
		$this->phar = realpath($this->phar);
		if(!file_exists($this->phar)) return $this->setError('File "'.$this->phar.'" not exists!');

		$data = $this->getDir($this->phar, '');
		
		$data['title'] = 'SHOW PHAR DATA';
		$data['phar'] = $this->phar; 

		return $data;
	}

	/* EXTRACT
	 * Extract all data from Phar file into '$to' path
	 * 
	 * @param $phar string 	path/to/file.phar [optional]
	 * @param $to string 	full path to extract all Phar contents
	 *
	 */

	function extract($phar = null, $to = null){

		$this->phar = $this->pharExists($phar);
		if(!$this->phar) return $this->setError('File not exists!');

		//checando se existe e pode ser criado o diretório de destino
		if($to == null) $to = realpath('./').'/sphar';
		if(!is_dir($to)){ 
			//tentando criar o diretório...
			@mkdir($to, 0777, true);
			if(!is_dir($to)) return  $this->setError('Indique um diretorio de destino valido.'); 
		}
		if(!is_readable($this->phar)) return  $this->setError('Verifique a permissao de leitura no diretorio "'.$to.'"!');

		$this->exo("\n # MKPHAR [extract files]\n\n 1 - Lendo arquivo PHAR: $this->phar");
		$list = $this->getDir($this->phar, $to); 		

		$this->exo("\n 3 - Extraindo arquivos: ");
		$files = $this->extractFiles($list['file']);

		$log = '';
		if($this->logFile) {
			file_put_contents($this->logFile, 'Created by MKPHAR [extract files] in '.date('d/m/Y H:i:s')."\n"
										.$this->formatLog(['dir'=>array_keys($list['dir']), 'file'=>$files]));
			$log = "\n   Para detalhamento do trabalho veja o arquivo '".realpath($this->logFile)."'.";
		}

		$this->exo("\n\n  ");
		return $this->exo("# EXTRACT PHAR - finished!$log\n");
	}

	/* CREATE
	 * Make a PHAR file with 'source' contents
	 *
	 * @param $phar string [opcional] indicates a valid Phar file
	 * @param $source array files and/or directorys to compilation
	 */

	function create($phar = null, $source = null){
		if($phar != null) $this->phar = $phar;
		if(end((explode('.', $phar))) != 'phar') return $this->setError('Indique um arquivo Phar valido (a extensao deve ser ".phar").');

		if($source != null){
			if(!is_array($source)) $this->source[] = $source;
			elseif(count($source) > 0) $this->source = $source;
		}

		$this->exo("\n # MKPHAR [create Phar]\n\n 1 - Criando arquivo PHAR: ");
		if(!is_writeable(realpath(dirname($this->phar)))) return $this->setError('Sem permissão para criar arquivo "'.$this->phar.'".');
		$this->node = new Phar($this->phar); //creating Phar...
		$this->exo(realpath($this->phar).' - sucesso!');

		$this->exo("\n 2 - Inserindo arquivos\n");
		$insert = $this->insertFiles(); //exit(print_r($insert, true));
		$this->exo("\n\tInseridos ".count($insert['file']).' arquivo(s) em '.count($insert['dir'])." diretorio(s).\n");


		//criando o cabeçalho Stub
		if($this->stub) {
			$bfile = basename($this->phar);
			$stub = is_string($this->stub) ? $this->stub : 'index.php';
			$this->node->setStub('<?php 
		function rewrite(){return "'.$stub.'";}
		Phar::interceptFileFuncs();
		Phar::mungServer(array(\'REQUEST_URI\', \'PHP_SELF\', \'SCRIPT_NAME\', \'SCRIPT_FILENAME\'));
		Phar::webPhar("'.$bfile.'", "'.$stub.'", "404.php", array(), "rewrite");
		Phar::mapPhar("'.$bfile.'");
		if(php_sapi_name() == \'cli\') require "phar://'.$bfile.'/'.$stub.'";
		__HALT_COMPILER();');
		}
		//comprimindo os dados (exceto o Stub)
		if($this->compress){
			if(Phar::canCompress(Phar::GZ)) 		$this->node->compressFiles(Phar::GZ);
			elseif (Phar::canCompress(Phar::BZ2))	$this->node->compressFiles(Phar::BZ2);
			$this->exo("\n 3 - Arquivo compactado!");
		}

		$log = '';
		if($this->logFile) {
			file_put_contents($this->logFile, 'Created by MKPHAR [create Phar] in '.date('d/m/Y H:i:s')."\n"
										.$this->formatLog($insert));
			$log = "\n   Para detalhamento do trabalho veja o arquivo '".realpath($this->logFile)."'.";
		}

		$this->exo("\n\n  ");
		return $this->exo("# MAKE PHAR - finished!$log");
 	}



	// =================== PRIVATE FUNCTONS ========================

	//show message [in cli mode = realtime display]
    private function exo($msg){
        if(SMODE == 'cli' && !$this->quiet) echo $msg;
        return $msg;
    }

	//checando se o PHAR existe e pode ser lido
	private function pharExists($phar = null){
		if($phar == null) $phar = $this->phar;

		$phar = realpath($phar);
		if($phar == '') return false; //exists?
		if(!is_readable($phar)) return false; //is readable?
		return $phar;
	}

	//Lista recursivamente o diretório indicado.
	private function getDir($dir, $base = false){ 
		if(!is_dir($dir)){
			$dir = 'phar://'.$dir;
			if(!is_dir($dir)) return [];
		}

		$base = $base === false ? basename($dir) : $base;

		$lst = scandir($dir);
		$o = ['dir'=>[], 'file'=>[]];
		$o['dir'][$base] = $dir;

		foreach ($lst as $file) {
			if($file == '.' || $file == '..') continue;
			$path = $dir.'/'.$file;

			if(is_dir($path)) $o = array_merge_recursive($o, $this->getDir($path, $base.'/'.$file));
			else $o['file'][$base.'/'.$file] = $path;
		}
		return $o;
	}

	//extrai arquivos de um Phar a partir do array de solicitação
	private function extractFiles($files){
	    $msg = [];
	    $c = count($files)/100;
	    $i = 0;
	    foreach($files as $file=>$phar){
	    	$i++;
	    	if(!is_dir(dirname($file))) mkdir(dirname($file), 0777, true);
	        if(file_put_contents($file, file_get_contents($phar))) $msg[] = $file;
	        else $msg[] = $file;

	        $this->exo(chr(13)." 3 - Extraindo arquivos: ".number_format($i/$c, 2,',',' ').'%');
	    }
	    $this->exo(chr(13)." 3 - Extraindo arquivos: concluido!");
	    return $msg;
	}

	//checa a origem e insere os arquivos (& diretórios)
	private function insertFiles(){

		$msg = ['file'=>[], 'dir'=>[]];
		$single = (count($this->source) == 1 && is_dir(realpath($this->source[0]))) ? '' : false;

		// Inserção de cada arquivo e diretório separadamente 
		foreach ($this->source as $file) {
			//checando se o diretório de origem existe:
			$file = realpath(trim($file, ' \\/'));
			$name = basename($file); 

			//inserindo um arquivo 	
			if(!is_dir($file)) { //is file??
				if(is_file($file)) {
					$this->exo("\n\tInserindo arquivo '".$file."': ".number_format($i/$c, 2,',',' ').'%');

					//Stripe Writespace or no
					if($this->stripPHP) $this->node->addFromString($name, php_strip_whitespace($file));
					else $this->node->addFile($file, $name);

					$msg['file'][] = $file;
				} //else $msg['file'][] = $file.' || não encontrado';

				$this->exo(chr(13)."\tInserindo arquivo '".$file."': concluido!"); 
		
			//inserindo diretório 
			} else {
				$msg['dir'][] = $file; 
				//$x = $single === false ? 'false' : 'null';
				$gd = $this->getDir($file, $single);
				$msg['dir'] = array_merge_recursive($msg['dir'], $gd['dir']);
				$c = count($gd['file'])/100;
				$i = 0;
				$this->exo("\n\tInserindo diretorio ".$file." : ");
				foreach($gd['file'] as $local=>$f){
					$i++;

					//Stripe Writespace or no
					if($this->stripPHP) $this->node->addFromString($local, php_strip_whitespace($f));
					else $this->node->addFile($f, $local);
					
					$msg['file'][] = $f;
					$this->exo(chr(13)."\tInserindo diretorio ".$file." : ".number_format($i/$c, 2,',',' ').'%');
				}
				$this->exo(chr(13)."\tInserindo diretorio ".$file." : concluido!");
			}
		}
		return $msg;
	}

	//formated!!
    function formatLog($data){
        $out = isset($data['title']) ? "\n  # $data[title]\n" : '';

        if(isset($data['phar']) && $data['phar'] != null && file_exists($data['phar'])) {
            $out.= "\n  File: $data[phar]\n  size: ".number_format(filesize($data['phar']), 0, ',','.')." bytes\n  time: ".date('d/m/Y H:i:s', filemtime($data['phar']))."\n";
        }
        
        $out.= "\n  Directory:\n";

        if(isset($data['dir'])){
            foreach($data['dir'] as $dir){ $out.= "\n  ".str_replace('phar://','',$dir);}
        } else $out.= "  Nenhum diretorio encontrado.";
        
        $out.= "\n\n  File:\n";
        
        if(isset($data['file'])){   
            $out.= "\n       LENGTH  NAME";    
            foreach ($data['file'] as $file) {
                $fs = substr('                                            '.filesize($file), -9);
                $out.= "\n  ".$fs." b  ".str_replace('phar://', '', $file);
            }
        } else $out.= "  Nenhum arquivo encontrado.";          
        
        return $out."\n";
    }

}