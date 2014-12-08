ATENÇÃO!!
=========

O .htaccess pode ser definindo para usar start.phar como index !!
Para desativar é só comentar a linha "DirectoryIndex start.phar"


Veja também a linha "RewriteRule ^(.*)$ start.phar/$1 [QSA,L]" -
- troque por        "RewriteRule ^(.*)$ index.php/$1 [QSA,L]"



Usando o Conversor PHAR:
========================

1 - acesse o projeto via terminal (ex.: php /var/www/projeto/index.php);
	...veja o 'help' do comando.

2 - converta todo o projeto: php /var/www/projeto/index.php -z -l start.phar /var/www/projeto

	Onde:
	-z               = compacta o arquivo final;
	-l		 = NÃO cria o arquivo de log;
	start.php        = local/nome do arquivo PHAR produzido;
	/var/www/projeto = diretório a ser convertido.

# O conversor PHAR também pode extrair (-x) um arquivo PHAR e mostrar (-d) uma listagem dos arquivos e pastas em um arquivo '.phar'.


Author: http://google.com/+BillRocha