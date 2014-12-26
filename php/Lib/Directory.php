<?php
/**
 * Directory
 *
 * File/Directory manager
 *
 * @author     Bill Rocha <prbr@ymail.com>
 * @version    1.0 $ 2014-03-07 19:56:33 $
 * @package		Neos\File
 */

namespace Lib;


class Directory{
	
	//Cria uma lista (divs) de arquivos e diret贸rios ... 
	static function listDir($dir){
		$dir = rtrim(str_replace(array('\\','|'), '/',$dir), '/ ').'/';

		//Para evitar/restringir o diretório
		//if(strpos(strtolower($dir), strtolower(\_cfg::this()->sync['path'])) === false) $dir = \_cfg::this()->sync['path'];
		$d = dir($dir);
		$id = 1;
		$o = '<table>
				<tr>
					<th>'.$dir.'</th>
					<th>Perms</th>
					<th>Time</th>
					<th>Size</th>
				</tr>
				<tr class="dirback" onClick="goDir(\''.dirname($dir).'\')">
					<td>...</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>';
		 
		$afile = $adir = array();
		
		while (false !== ($f = $d->read())) {
			$id ++;
			if($f == '.' || $f == '..') continue;
			
			if(is_dir($dir.$f)) $adir[] = $f;
			else $afile[] = $f;
		}

		//Ordenando a listagem
		sort($adir);
		sort($afile);
		
		//Montando a visualização de DIRETÓRIOS
		foreach($adir as $k=>$f){
			$o .= '	<tr class="diretorios" id="dir'.$k.'" sdir="'.$dir.'" sname="'.$f.'" onClick="goDir(\''.$dir.$f.'\')">
						<td>'.$f.'</td>
						<td>'.substr(sprintf('%o', fileperms($dir.$f)), -4).'</td>
						<td>'.date ('d/m/Y', filemtime($dir.$f)).'&nbsp;'.date ('H:i', filemtime($dir.$f)).'</td>
						<td>&nbsp;</td>
					</tr>			
			';
		}

		//Montando a visualização de ARQUIVOS
		foreach($afile as $k=>$f){
			$ext = explode('.', $f); //pegando a extens茫o do arquivo	
			$o .= '	<tr class="arquivos '.end($ext).'" id="file'.$k.'" sdir="'.$dir.'" sname="'.$f.'" 
						onClick="viewFile(\'file'.$k.'\',\''.$dir.$f.'\')">
						<td>'.$f.'</td>
						<td>'.substr(sprintf('%o', fileperms($dir.$f)), -4).'</td>
						<td>'.date ('d/m/Y', filemtime($dir.$f)).'&nbsp;'.date ('H:i', filemtime($dir.$f)).'</td>
						<td>'.(filesize($dir.$f)/1000).'&nbsp;Kb</td>
					</tr>			
			';
		}

		return ($o == '')?'</table><div class="fmList block"><h3>Nenhum arquivo ou diret贸rio encontrado.</h3></div>':$o;		
	}

	//Lista um determinado diretorio retornando em JSON	
	static function jList($dir = RPATH, $preview = false){		
		$dir = ($preview == 1) 
			? dirname(rtrim(str_replace('\\', '/', $dir), '/ ')).'/' 
			: rtrim(str_replace('\\', '/', $dir), '/ ').'/';
			
		if(strpos(strtolower($dir), strtolower(\_cfg::this()->sync['path'])) === false 
				|| !is_dir($dir)) $dir = \_cfg::this()->sync['path'];

		$d = dir($dir); 
		$id = 0;		 
		$afile = $adir = array();
		
		while (false !== ($f = $d->read())) {
			$id ++;
			if($f == '.' || $f == '..') continue;
			
			if(is_dir($dir.$f)) {//para diretorios
				$adir[$id]['name'] = $f;
				$adir[$id]['perm'] = substr(sprintf('%o', fileperms($dir.$f)), -4);
				$adir[$id]['date'] = date ('d/m/Y H:i:s', filemtime($dir.$f));				
			} else { //para arquivos
				$x = explode('.', $f); 
				$afile[$id]['name'] = $f;
				$afile[$id]['ext'] = end($x);
				$afile[$id]['perm'] = substr(sprintf('%o', fileperms($dir.$f)), -4);
				$afile[$id]['date'] = date ('d/m/Y H:i:s', filemtime($dir.$f));
				$sz = filesize($dir.$f);
				if($sz < 1000) $z = $sz.' b';
				if($sz > 1000 && $sz < 1000000) $z = intval($sz/1000).' K';
				if($sz > 1000000) $z = intval($sz/1000000).' M';
				$afile[$id]['size'] = $z;				
			}
		}
		sort($adir);
		sort($afile);
		$diretorio = explode('/', trim($dir, '/'));
		$diretorio = end($diretorio);
		return json_encode(array('base'=>$dir, 'diretorio'=>$diretorio, 'dir'=>$adir, 'file'=>$afile));			
	}
}