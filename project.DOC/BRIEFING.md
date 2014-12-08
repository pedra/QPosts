Breafing
========

QPosts � uma plataforma de publica��o jornal�stica de carater pessoal (blog) ou profissional (e-magazine) com as seguintes caracter�sticas:

	* F�cil de usar por *qualquer* pessoa - intuitivo;
	* UI de f�cil configura��o - maior padroniza��o, menos configura��o (Rails !?);
	* R�pido acesso web, com optimiza��o para celular, mobile, banda lenta, desktop, etc.(tela & banda);
	* App de publica��o para Android, IOS e Windows Phone;
	* Publica��o de videos diretamente na conta de usu�rio no Youtube;
	* Publica��o de imagens no pr�prio site ou em site de images;
	* Publica��o de �udio no pr�prio site ou em servidor dedicado;
	* Configura��o de design f�cil e pr�tica - *template fixo;
	* Armazenamento dos posts em Banco de Dados, SqLite ou File - praticidade na instala��o;
 


QPosts
======

Nome provis�rio que significa Quickly Posts ou Postagens R�pidas.
A intens�o � divulgar a facilidade na cria��o e manuten��o de uma plataforma de publica��o de texto, imagens, �udio e v�deo (medias web) como uma ferramenta pessoal ou profissional (blog/e-magazine). Em plural para demonstrar que o sistema � t�o r�pido e t�o f�cil de usar que muitiplas postagens � uma rotina trivial.


Template Fixo
=============

O sistema, para ganhar performance e praticidade, n�o permite a troca autom�tica de template e instala��o de v�rios templates como acontece com o WordPress, por exemplo. A configura��o (design) � feita de forma �nica, f�cilmente por qualquer web design familiarizado com html, css, js. A troca de template, neste caso, se d� pela simples copia direta dos arquivos.

Um template desenvolvido no GitHub (ou outro Git) pode ser automaticamente instalado, por outro lado, usando *COMPOSER*.

Considere que normalmente o template (design) de um site � sua pr�pria identidade e muitas vezes sua marca. N�o � comum a troca constante de design. Com isso o projeto ganha em facilidade de uso, simplicidade, velocidade no acesso e ainda menos uso do servidor com configura��es de templates - quase nunca usados.


Armazenamento
=============

Banco de Dados compat�veis com o PDO (Oracle, Mysql, MSSql, etc), SQLITE para pequenos projetos e ainda a possibilidade de armazenamento em arquivos simples.

Neste �ltimo, uma pasta cujo nome � o ID da publica��o, contendo os arquivos texto e de medias (imagem, audio, video) que pode ainda ser compactada (PHAR) e distribuida para outros QPosts. Um arquivo no formato 'json' indexa as informa��es da postagem.
Isso possibilita uma s�rie de facilidades e praticidades: f�cil instala��o (n�o precisa configurar banco de dados), r�pido update, f�cil edi��o e manipula��o de arquivos, r�pida distribui��o do conte�do entre v�rios QPosts, etc.

O armazenamento em bancos de dados segue a tradicional pr�tica de guardar somente os textos e metadados, ficando os arquivos de media em uma pasta como descrita acima.


===== end =====

  