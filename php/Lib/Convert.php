<?php
/**
 * Convert
 *
 * File/Directory transformations & convertions
 *
 * @author     Bill Rocha <prbr@ymail.com>
 * @version    1.0 $ 2014-03-07 19:56:33 $
 * @package		Neos\File
 */

namespace Lib;
use Phar;
use ZipArchive;

class Convert {


	/* Compacta um diretorio!
	 * ex.: (new Convert())->zip('/var/www/file.zip', '/var/www/sync', '/');
	 */
	function zip($zipFile, $dir, $newDir = ''){
		$Hzip = new ZipArchive();
		// abre o arquivo .zip
		if ($Hzip->open($zipFile, ZIPARCHIVE::CREATE) !== true) 
			return trigger_error('Não foi possível abrir o arquivo de DESTINO ('.$zipFile.')!');
		
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		
		// itera cada pasta/arquivo contido no diretório especificado
		foreach ($iterator as $key=>$value) {
			// adiciona o arquivo ao .zip
			if(!$Hzip->addFile(realpath($key), $newDir.str_replace('\\', '/', str_replace($dir,'',realpath($key)))))
				return trigger_error('Não é possível adicionar o arquivo "'.$key.'"!');
		}
		// fecha e salva o arquivo .zip gerado
		return $Hzip->close();
	}
	
	/* Adicionando um ARQUIVO ao zip.file
	 * ex.: (new Convert())->insert('/var/www/file.zip', '/var/www/sync/index.php', '/index.php');
	 */ 
	function insert($zipFile, $file, $asFile = ''){
		$Hzip = new ZipArchive();
		// abre o arquivo .zip
		if ($Hzip->open($zipFile, ZIPARCHIVE::CREATE) !== true) 
			return trigger_error('Não foi possível abrir o arquivo "'.$zipfile.')"!');
     
    	$Hzip->addFile($file, (($asFile == '') ? $file : $asFile));
		return $Hzip->close();
	}
	
	
	/* lista os arquivos compactados. 
	 * ex.: $listArray = (new Convert())->view('/var/www/file.zip');
	 */ 
	function view($zipFile){
		$Hzip = new ZipArchive();
		if($Hzip->open($zipFile) !== true) 
			return trigger_error('Não foi possível abrir o arquivo "'.$zipFile.'"!');
			
		$temp = array();
		for ($i = 0; $i < $Hzip->numFiles; $i++) { $temp[] = $Hzip->getNameIndex($i);}
		return $temp;		
	}
	
	/* Descompatando um diretório/arquivo
	 * ex.: (new Convert())->unZip('/var/www/file.zip', '/var/www/destino/');
	 */ 
	function unZip($zipFile, $dir = ''){
		$Hzip = new ZipArchive();
		if($dir == '') $dir = dirname(__FILE__);
		if($Hzip->open($zipFile) !== true) 
			trigger_error('Não foi possível abrir o arquivo '.$zipFile.'!');

		$Hzip->extractTo($dir);
		return $Hzip->close();	
	}	

	/** toPhar
	 * Conversor de DIRETORIO em arquivo PHAR
	 *
	 * Usage: (new Convert())->toPhar('/var/www/site', '/var/www/site.phar');
	 * 
	 * $dir			- diretório (caminho completo);
	 * $file		- arquivo de saída - indique o caminho completo, nome e extensão '.phar'(obrigatório).
	 * $compress	- true/false para compactação do arquivo.
	 * $signature	- arquivo da chave de segurança para o phar (opcional).
	 * nesta versão somente use uma chave MD5.
	 */

	function toPhar($dir, $file, $stub = '', $compress = true, $type = 'ex', $signature = ''){
		
		//conferindo os dados...
		if(!is_dir($dir)) trigger_error('Diretório "'.$dir.'" inexistente!');
		$u = true;
		if(is_file($file)) $u = unlink($file);
		if(!$u) trigger_error('O arquivo de destino ["'.$file.'"] não pode ser manipulado. Talvez algum problema de acesso ou permissão.');
		
		/* aumentando a memoria e o tempo de execução
		 * pode ser muito significante em sistemas lentos e diretórios muito grandes */
		ini_set('memory_limit', '30M');
				ini_set('max_execution_time', 180);
		
		//pegando o diretório (e sub-diretórios) e arquivos contidos
		$phar = new Phar($file);
		$phar->buildFromDirectory($dir);
		
		//Convertendo para DATA e retornando
		if($type == 'dt'){
			$ret = $phar->convertToData(Phar::TAR, ((Phar::canCompress(Phar::GZ))?Phar::GZ:Phar::NONE), '.dphar');
			$phar = null;
			unlink($file);
			return $ret;
		}
		
		//criando o cabeçalho Stub
		if($type == 'ex') $stub = 'include(\'phar://\' . __FILE__ . \'/' . $stub . '\');';
			if($type == 'wp') $stub = 'Phar::webPhar(\'\', \'\', \'404.php\');';
			$phar->setStub('<?php Phar::interceptFileFuncs();Phar::mungServer(array(\'REQUEST_URI\', \'PHP_SELF\', \'SCRIPT_NAME\', \'SCRIPT_FILENAME\'));'.$stub.'__HALT_COMPILER();');
		
		//carregando a assinatura
		if($signature != '' && is_file($signature))
			$phar->setSignatureAlgorithm(Phar::MD5, file_get_contents($signature));
	
		//Comprimindo
		if($compress && Phar::canCompress(Phar::GZ)) return $phar->compressFiles(Phar::GZ);
	}



}