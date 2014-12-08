<?php
/**
* Main access - Home Page
*
* @author http://plus.google.com/+BillRocha
*/
namespace Controller;
use Lib\Database as DB;
use Lib\Html\Doc as HTML;
use Lib\Config as CFG;
use Lib\Persist as MEM;

class Main {

	function index(){ 

		$page = 'Article';
        //$pages = ['index', 'about', 'blog', 'contact','details','portfolio'];        
        //if(isset($this->parms[0]) && in_array($this->parms[0], $pages)) $page = 'layout/'.$this->parms[0];
        
        //Carregando a configuração da matéria
        $config = json_decode(file_get_contents(ROOT.'php/Data/1/data.json'));
      
        foreach((explode(',', $config->tags)) as $v){
            $tags[] = trim($v);
        }
        
        //e($tags);

        //Rendizando o HTML (View) - optional chain mode
		$html = new HTML('Article');
		$html->val('siteTitle', '&trade;QPosts')
             
             ->val('authorName', $config->author->name)
             ->val('authorEmail', $config->author->email)

             ->val('articleTitle', $config->title)
             ->val('articleDate', date('d/m/Y - H:i:s', $config->date))
             ->val('articleContent', file_get_contents(ROOT.'php/Data/1/'.$config->id.'.html'))
             ->val('articleTags', $tags)

			 ->render()
			 ->send();
	}
}