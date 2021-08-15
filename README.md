#Twitter Clone

Aplicação desenvolvida baseando-se na lógica do twitter.

Para que a aplicação funcione é necessário que tenha instalado o COMPOSER e o PHP no sistema. Com o PHP 5.4+ já é possível utilizar o servidor que vem embutido.

No diretório do projeto o composer ja está instalado, mas caso tenha algum problema tente instala-lo novamente.

       # Instalando o Composer caso não
       
       #1 - Baixe o Composer-Setup.exe para instalar em todo o sistema: https://getcomposer.org/download/
       
       #2 - Instale apenas ao diretório do projeto: 
       
              php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
              php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo               'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
              
              php composer-setup.php
              php -r "unlink('composer-setup.php');"
              
       #3 - Configure o o arquivo composer.json de sua preferência.
       
       #4 - Instale o composer.phar
       
              php composer.phar install
              
      
Iniciando o servidor dentro da pasta public:

       #Abra o diretório public no cmd
  
       C:\Users\luian>cd C:\projects\twitter\public
       
       #Iniciando o servidor
       
       C:\projects\twitter\public> php -S localhost:8080
       
       
Crie o banco de dados com as querys do arquivo 'sql.txt'.
       
       
