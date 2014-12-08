Breafing
========

QPosts é uma plataforma de publicação jornalística de carater pessoal (blog) ou profissional (e-magazine) com as seguintes características:

	* Fácil de usar por *qualquer* pessoa - intuitivo;
	* UI de fácil configuração - maior padronização, menos configuração (Rails !?);
	* Rápido acesso web, com optimização para celular, mobile, banda lenta, desktop, etc.(tela & banda);
	* App de publicação para Android, IOS e Windows Phone;
	* Publicação de videos diretamente na conta de usuário no Youtube;
	* Publicação de imagens no próprio site ou em site de images;
	* Publicação de áudio no próprio site ou em servidor dedicado;
	* Configuração de design fácil e prática - *template fixo;
	* Armazenamento dos posts em Banco de Dados, SqLite ou File - praticidade na instalação;
 


QPosts
======

Nome provisório que significa Quickly Posts ou Postagens Rápidas.
A intensão é divulgar a facilidade na criação e manutenção de uma plataforma de publicação de texto, imagens, áudio e vídeo (medias web) como uma ferramenta pessoal ou profissional (blog/e-magazine). Em plural para demonstrar que o sistema é tão rápido e tão fácil de usar que muitiplas postagens é uma rotina trivial.


Template Fixo
=============

O sistema, para ganhar performance e praticidade, não permite a troca automática de template e instalação de vários templates como acontece com o WordPress, por exemplo. A configuração (design) é feita de forma única, fácilmente por qualquer web design familiarizado com html, css, js. A troca de template, neste caso, se dá pela simples copia direta dos arquivos.

Um template desenvolvido no GitHub (ou outro Git) pode ser automaticamente instalado, por outro lado, usando *COMPOSER*.

Considere que normalmente o template (design) de um site é sua própria identidade e muitas vezes sua marca. Não é comum a troca constante de design. Com isso o projeto ganha em facilidade de uso, simplicidade, velocidade no acesso e ainda menos uso do servidor com configurações de templates - quase nunca usados.


Armazenamento
=============

Banco de Dados compatíveis com o PDO (Oracle, Mysql, MSSql, etc), SQLITE para pequenos projetos e ainda a possibilidade de armazenamento em arquivos simples.

Neste último, uma pasta cujo nome é o ID da publicação, contendo os arquivos texto e de medias (imagem, audio, video) que pode ainda ser compactada (PHAR) e distribuida para outros QPosts. Um arquivo no formato 'json' indexa as informações da postagem.
Isso possibilita uma série de facilidades e praticidades: fácil instalação (não precisa configurar banco de dados), rápido update, fácil edição e manipulação de arquivos, rápida distribuição do conteúdo entre vários QPosts, etc.

O armazenamento em bancos de dados segue a tradicional prática de guardar somente os textos e metadados, ficando os arquivos de media em uma pasta como descrita acima.


===== end =====

  