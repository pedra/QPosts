ATEN��O!!
=========

O .htaccess pode ser definindo para usar start.phar como index !!
Para desativar � s� comentar a linha "DirectoryIndex start.phar"


Veja tamb�m a linha "RewriteRule ^(.*)$ start.phar/$1 [QSA,L]" -
- troque por        "RewriteRule ^(.*)$ index.php/$1 [QSA,L]"



Usando o Conversor PHAR:
========================

1 - acesse o projeto via terminal (ex.: php /var/www/projeto/index.php);
	...veja o 'help' do comando.

2 - converta todo o projeto: php /var/www/projeto/index.php -z -l start.phar /var/www/projeto

	Onde:
	-z               = compacta o arquivo final;
	-l		 = N�O cria o arquivo de log;
	start.php        = local/nome do arquivo PHAR produzido;
	/var/www/projeto = diret�rio a ser convertido.

# O conversor PHAR tamb�m pode extrair (-x) um arquivo PHAR e mostrar (-d) uma listagem dos arquivos e pastas em um arquivo '.phar'.


Author: http://google.com/+BillRocha