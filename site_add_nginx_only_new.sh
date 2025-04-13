#!/bin/bash

# COLORS
SETCOLOR_SUCCESS="\033[1;32m"
SETCOLOR_FAILURE="\033[1;31m"
SETCOLOR_NORMAL="\033[0;39m"
SETCOLOR_NOTICE="\033[1;33;40m"
SETCOLOR_GREEN="\033[1;32m"
SETCOLOR_CLEAR="\033[00m"
SET_BORD= | tr '\n' '\n'
SET_RED="\033[1;31m"
SET_GREEN="\033[1;32m"
SET_UNDERLINE="\033[4m"
SET_BOLD="\033[1m"
SET_CLOSE="\033[0m"
set_prestashop="
# ======================
# PrestaShop 8.x NGINX Configuration
# ======================

    # Basic security blocks
    location ~ /\.env {
        deny all;
        return 404;
    }
    
    location ~* ^/(app|bin|cache|classes|config|controllers|docs|localization|override|src|tests|tools|translations|var|vendor)/ {
        deny all;
        return 403;
    }

    # Disable access to sensitive files
    location ~* ^/(\.git|composer\.(json|lock)|config(_defines)?\.php|docker-compose\.yml)$ {
        deny all;
        return 403;
    }

    # API rewrite
    rewrite ^/api/?(.*)$ /webservice/dispatcher.php?url=\$1 last;

    # Image rewrites (optimized for PS 8)
    rewrite ^/([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/\$1/\$1\$2.jpg last;
    rewrite ^/([0-9]{2})(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/\$1/\$2\$3.jpg last;
    rewrite ^/([0-9]{3})(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/\$1/\$2/\$3\$4.jpg last;
    rewrite ^/([0-9]{4})(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/\$1/\$2/\$3/\$4\$5.jpg last;

    # Category images
    rewrite ^/c/([0-9]+)(-[_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ /img/c/\$1\$2.jpg last;
    rewrite ^/c/([a-zA-Z-]+)(-[0-9]+)?/.+\.jpg$ /img/c/\$1.jpg last;

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2?|ttf|eot)$ {
        expires 365d;
        add_header Cache-Control \"public, no-transform\";
        try_files \$uri \$uri/ /index.php?\$args;
    }

    # Admin protection
    location ~* ^/admin([0-9a-zA-Z]{0,})$ {
        add_header X-Frame-Options \"SAMEORIGIN\";
        add_header X-Content-Type-Options \"nosniff\";
        add_header X-XSS-Protection \"1; mode=block\";
        try_files \$uri \$uri/ /index.php?\$args;
    }

    # Main rewrite rule
    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PHP_VALUE \"upload_max_filesize=128M \n post_max_size=128M\";
        fastcgi_read_timeout 300;
    }
"

set_processwire="
    location ~ ^/site(-[^/]+)?/assets/(.*\.php|backups|cache|config|install|logs|sessions) {
	    deny  all;
    }
    location ~ ^/site(-[^/]+)?/install {
	    deny  all;
    }
    location ~ ^/(site(-[^/]+)?|wire)/(config(-dev)?|index\.config)\.php {
	    deny  all;
    }
    location ~ ^/((site(-[^/]+)?|wire)/modules|wire/core)/.*\.(inc|module|php|tpl) {
	    deny  all;
    }
    location ~ ^/(site(-[^/]+)?|wire)/templates(-admin)?/.*\.(inc|html?|php|tpl) {
	    deny  all;
    }
"

set_opencart="
	location = /sitemap.xml {
		rewrite ^(.*)$ /index.php?route=extension/feed/google_sitemap last;
	}

	location = /googlebase.xml {
	    rewrite ^(.*)$ /index.php?route=extension/feed/google_base last;
	}

	location /system {
	    rewrite ^/system/storage/(.*) /index.php?route=error/not_found last;
	}
	
	location /admin { index index.php; }
"

# echo "***************************************************";
# $SETCOLOR_NOTICE
# ls /etc/nginx/sites-available/
# $SETCOLOR_NORMAL
# echo "___________________________________________________";
run_script() {
echo ""
echo "******************************************************"

echo "Available virtual hosts:"
echo -en ${SETCOLOR_FAILURE}
ls /etc/nginx/sites-available/ | grep -v -E "template" | wc -l
echo -en ${SETCOLOR_NORMAL}
echo "------------------------------------------------------"
#	ls /etc/nginx/sites-available/ | grep -v -E "template" | tr '\n' '\n'
ls /etc/nginx/sites-available/
echo ""
echo ""
echo "******************************************************"
echo "Enabled virtual hosts:"
echo -en ${SETCOLOR_GREEN}
ls /etc/nginx/sites-enabled/ | grep -v -E "template" | wc -l
echo -en ${SETCOLOR_CLEAR}
echo "------------------------------------------------------"
#	ls /etc/nginx/sites-enabled/ | grep -v -E "template" | tr '\n' '\n'
ls /etc/nginx/sites-enabled/
echo ""
echo ""

echo -e "\033[4mВведите название проекта:\033[0m"$SET_BORD; 
read NAME_OF_PROJECT

DHM=/etc/nginx/sites-available/$NAME_OF_PROJECT;
 
if [ -f $DHM ]
then
    echo -e "\033[7mДомен уже существует. Выберите другое имя!!!\033[0m";
else
    echo -e '\033[7mДомен свободен для регистации!!!\033[0m';
# Cоздаем папки проекта
#WORK_DIR_DEFAULT="/home/xupypx/web/domains"
WORK_DIR_DEFAULT="/media/files/home/xupypx/web/domains"
echo ""
echo -e "${SET_UNDERLINE}Выберите версию PHP-FPM:${SET_CLOSE}"
echo "1) PHP 5.6"
echo "2) PHP 7.4"
echo "3) PHP 8.2"
echo "4) PHP 8.3"
echo "5) PHP 8.4"
read -rp "Введите номер версии (1-5): " php_choice

case "$php_choice" in
    1) PHP_FPM="5.6" ;;
    2) PHP_FPM="7.4" ;;
    3) PHP_FPM="8.2" ;;
    4) PHP_FPM="8.3" ;;
    5) PHP_FPM="8.4" ;;
    *)
        echo -e "${SET_RED}Неверный выбор. По умолчанию будет установлена версия 7.4${SET_CLOSE}"
        PHP_FPM="7.4"
        ;;
esac

echo -e "${SETCOLOR_GREEN}Вы выбрали PHP-FPM версии ${PHP_FPM}${SET_CLOSE}"

echo -e "\033[1mНазначить произвольный путь папки проекта? (Y/N)\033[0m"
read CREATE_WORK_DIR

if  [ "$CREATE_WORK_DIR" = "yes" -o "$CREATE_WORK_DIR" = "y" -o "$CREATE_WORK_DIR" = "YES" ]; then
	echo -e "\033[1mВведите произвольный путь папки проекта $NAME_OF_PROJECT:\033[0m";
	read DIR_HOST
	echo -e "\033[1mНазначен путь $DIR_HOST/$NAME_OF_PROJECT папки проекта $NAME_OF_PROJECT.\033[0m";

else

echo -e "\033[1mБудет использован путь по умолчанию $WORK_DIR_DEFAULT\033[0m";
DIR_HOST="$WORK_DIR_DEFAULT"
fi

#WORK_DIR="$NAME_OF_PROJECT"
#DIR_HOST="/home/xupypx/web/domains"
sudo mkdir -p $DIR_HOST/$NAME_OF_PROJECT
sudo chown -R $USER:$USER $DIR_HOST/$NAME_OF_PROJECT
cd $DIR_HOST/$NAME_OF_PROJECT
sudo mkdir cgi-bin logs www

#указываем владельца и права на папку "www"
#sudo chmod -R 755 $DIR_HOST/$NAME_OF_PROJECT/
sudo chown -R $USER:$USER $DIR_HOST/$NAME_OF_PROJECT/www
#sudo chmod -R a+r $DIR_HOST/$NAME_OF_PROJECT/www

echo
echo -e "${SETCOLOR_BLUE}Выберите тип CMS для виртуального хоста:${SETCOLOR_NORMAL}"
echo "1) Prestashop"
echo "2) ProcessWire"
echo "3) OpenCart"
echo "4) Без CMS (универсальная конфигурация)"
read -p "Введите номер варианта (1-4): " CMS_CHOICE

case $CMS_CHOICE in
    1)
        to_prestashop=$set_prestashop
        suma="    location / {try_files \$uri \$uri/ /index.php?it=\$uri&\$args;}"
        echo -e "${SETCOLOR_GREEN}Выбран шаблон для Prestashop${SETCOLOR_NORMAL}"
        ;;
    2)
        to_processwire=$set_processwire
        suma="    location / {try_files \$uri \$uri/ /index.php?it=\$uri&\$args;}"
        echo -e "${SETCOLOR_GREEN}Выбран шаблон для ProcessWire${SETCOLOR_NORMAL}"
        ;;
    3)
        to_opencart=$set_opencart
        suma="    location / { try_files \$uri @opencart; }
    location @opencart { rewrite ^/(.+)$ /index.php?_route_=\$1 last; }"
        echo -e "${SETCOLOR_GREEN}Выбран шаблон для OpenCart${SETCOLOR_NORMAL}"
        ;;
    4)
        suma="    location / {try_files \$uri \$uri/ /index.php?it=\$uri&\$args;}"
        echo -e "${SETCOLOR_YELLOW}Выбран универсальный шаблон (без CMS)${SETCOLOR_NORMAL}"
        ;;
    *)
        echo -e "${SETCOLOR_RED}Неверный выбор. Выход.${SETCOLOR_NORMAL}"
        exit 1
        ;;
esac


# Создаем страничку в www для того чтобы сайт хоть что-то отражал
echo -e "\033[1mСоздать стартовую страницу?(yes/no)\033[0m"
read cr_start_page
if [ "$cr_start_page" == 'y' -o "$cr_start_page" == 'Y' ]; then
touch $DIR_HOST/$NAME_OF_PROJECT/www/index.html
echo "<?php 
\$title_page=\"Стартовая страница $NAME_OF_PROJECT\";
\$description_page=\"Стартовая страница $NAME_OF_PROJECT\";
require_once('./head.inc');?>
<main role=\"main\" class=\"container\">
<h1>It Works! <span>$NAME_OF_PROJECT</span></h1>
<div class='text-center'>
<h2>Шифрование</h2>
<form name='form' action='' method='post'>
  <div>
    <input type='text' name='x' required minlength='2' maxlength='21' placeholder='Введите фразу'>
    <br>
    <input class='btn btn-info' type='submit' value='Шифрануть' name='psw'>
  </div>
</form>
<?php
  session_start();
  if (!empty(\$_POST['psw'])) {
    \$_SESSION['x'] = \$_POST['x'];
    header('Location: '.\$_SERVER['REQUEST_URI']);
    exit;
  }
  if (isset(\$_SESSION['x']))  echo '<h3>Значение фразы: <span>'.\$_SESSION['x'].'</span> зашифровано и имеет вид: <span>'.crypt(\$_SESSION['x'], base64_encode(\$_SESSION['x'])).'</span></h3>';
?>
</div>
<div class=\"row mb-2\">
<div class=\"col-md-6\">
    <div class=\"card flex-md-row mb-4 shadow-sm h-md-250\">
    <div class=\"card-body d-flex flex-column align-items-start\">
        <strong class=\"d-inline-block mb-2 text-primary\">Полезное</strong>
        <h3 class=\"mb-0\">
        <a class=\"text-dark\" href=\"#\">Транслитерация</a>
        </h3>
        <div class=\"mb-1 text-muted\">
        <?php
\$_tfile = filemtime('head.inc');//файл по которому определяем время
// Вывод даты на русском
\$monthes = array(
    1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
    5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
    9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
);
echo(date('Создана: d ',\$_tfile) . \$monthes[(date('n',\$_tfile))] . date(' Y г. ',\$_tfile));
// Вывод дня недели

\$days = array(
    'в <span>Воскресенье</span>', 'в Понедельник', 'во Вторник', 'в Среду',
    'в Четверг', 'в Пятницу', 'в <span>Субботу</span>'
);
echo(\$days[(date('w',\$_tfile))] . date(', H часов i минут',\$_tfile));


?>
        </div>
        <p class=\"card-text mb-auto\">
  <script>
function translit() {
    var str = document.getElementById('name').value;
    var space = '-';
    var link = '';
    var transl = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
        'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
        'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh','ъ': space,
        'ы': 'y', 'ь': space, 'э': 'e', 'ю': 'yu', 'я': 'ya'
    }
if (str != '')
    str = str.toLowerCase();
 
for (var i = 0; i < str.length; i++){
    if (/[а-яё]/.test(str.charAt(i))){ // заменяем символы на русском
        link += transl[str.charAt(i)];
    } else if (/[a-z0-9]/.test(str.charAt(i))){ // символы на анг. оставляем как есть
        link += str.charAt(i);
    } else {
        if (link.slice(-1) !== space) link += space; // прочие символы заменяем на space
    }
}
    document.getElementById('code').value = link;
}
function myFunction() {
  var copyText = document.getElementById('code');
  copyText.select();
  document.execCommand('copy');
  
  var tooltip = document.getElementById('myTooltip');
  tooltip.innerHTML = 'Скопировано' /*+ copyText.value*/;
}

function outFunc() {
  var tooltip = document.getElementById('myTooltip');
  tooltip.innerHTML = 'Копировать в буфер';
}
</script>

Транслит:<br>
<textarea name='name' type='text' id='name' rows='2' cols='40' onKeyUp='translit()'>Глушим танки</textarea><br>
Код для URL:<br>
<textarea name='code' type='text' id='code' rows='2' cols='40' readonly></textarea>
        </p>
        <div class='tooltop'>
            <button type='button' class='btn btn-info' onclick='myFunction()' onmouseout='outFunc()'>
            <span class='tooltiptext' id='myTooltip'>Копировать в буфер</span>
            Копировать текст
            </button>
        </div>
    </div>
    <img class=\"card-img-right flex-auto d-none d-lg-block\" data-src=\"holder.js/200x400?theme=thumb\" alt=\"Card image cap\">
    </div>
</div>
<div class=\"col-md-6\">
    <div class=\"card flex-md-row mb-4 shadow-sm h-md-250\">
    <div class=\"card-body d-flex flex-column align-items-start\">
        <strong class=\"d-inline-block mb-2 text-success\">Сервер</strong>
        <h3 class=\"mb-0\">
        <a class=\"text-dark\" href=\"#\">Тех. данные</a>
        </h3>
        <div class=\"mb-1 text-muted\"><?php echo(date('d ') . \$monthes[(date('n'))] . date(' Y г.'));?></div>
        <p class=\"card-text mb-auto\">

<?php
  echo \"Имя сервера: \".\$_SERVER['SERVER_NAME'].\"<br />\";
  echo \"IP-адрес сервера: \".\$_SERVER['SERVER_ADDR'].\"<br />\";
  echo \"Порт сервера - \".\$_SERVER['SERVER_PORT'].\"<br />\";
  echo \"Web-сервер - \".\$_SERVER['SERVER_SOFTWARE'].\"<br />\";
  echo \"Версия HTTP-протокола - \".\$_SERVER['SERVER_PROTOCOL'].\"<br />\";
  \$user_agent = \$_SERVER[\"HTTP_USER_AGENT\"];
  if (strpos(\$user_agent, \"Firefox\") !== false) \$browser = \"Firefox\";
  elseif (strpos(\$user_agent, \"Opera\") !== false) \$browser = \"Opera\";
  elseif (strpos(\$user_agent, \"Chrome\") !== false) \$browser = \"Chrome\";
  elseif (strpos(\$user_agent, \"Epiphany\") !== false) \$browser = \"Epiphany\";
  elseif (strpos(\$user_agent, \"MSIE\") !== false) \$browser = \"Internet Explorer\";
  elseif (strpos(\$user_agent, \"Safari\") !== false) \$browser = \"Safari\";
  else \$browser = \"Неизвестный\";
  echo \"Ваш браузер: \$browser\";
?>

        </p>
        <a href=\"info.php\" target=\"_blank\">PHP info</a>
    </div>
    <img class=\"card-img-right flex-auto d-none d-lg-block\" data-src=\"holder.js/200x400?theme=thumb\" alt=\"Card image cap\">
    </div>
</div>
</div>
<table class=\"table table-bordered table-sm table-dark\">
  <thead>
    <tr>
      <th scope=\"col\">#</th>
      <th scope=\"col\">First</th>
      <th scope=\"col\">Last</th>
      <th scope=\"col\">Handle</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope=\"row\">1</th>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <th scope=\"row\">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
    <tr>
      <th scope=\"row\">3</th>
      <td colspan=\"2\">Larry the Bird</td>
      <td>@twitter</td>
    </tr>
  </tbody>
</table>
<?php
//Увеличение числа каждый день 
\$dateStart = new DateTime('15.02.2019');
\$now = new DateTime();
\$number = 1000;
\$step = 1;
\$diff = \$dateStart->diff(\$now)->format('%R%a');
echo  \$step * \$diff + \$number;?>
<?php require_once('./footer.inc');?>" >> $DIR_HOST/$NAME_OF_PROJECT/www/index.html
touch $DIR_HOST/$NAME_OF_PROJECT/www/404.html
echo "<?php 
\$title_page=\"Страница - 404 - $NAME_OF_PROJECT\";
\$description_page=\"Страница - 404 - $NAME_OF_PROJECT\";
require_once('./head.inc');?>
<main role=\"main\" class=\"container\">
<h1>Страница - 404 - <span>$NAME_OF_PROJECT</span></h1>
<?php require_once('./footer.inc');?>" >> $DIR_HOST/$NAME_OF_PROJECT/www/404.html
touch $DIR_HOST/$NAME_OF_PROJECT/www/head.inc
echo "<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\" />
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
<title><?php echo \$title_page;?></title>
<meta name=\"description\" content=\"<?php echo \$description_page;?>\">
<link rel=\"icon\" href=\"favicon.svg\" type=\"image/svg+xml\">
<link rel=\"alternate icon\" href=\"favicon.ico\">
<link rel=\"mask-icon\" href=\"favicon-safari-pin.svg\" color=\"#CE0000\">
<link href=\"css/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
<style>.container{width:100%;padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}.col-md-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%}textarea{width:100%;}@media(min-width:1200px){.container{max-width:1140px}}.row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px}h1{font-size:50px;margin:0;text-align:center;text-transform:capitalize}h3{font-size:1.5em}span{color:red}.text-center{text-align:center}input{margin:.5em 0;padding:.5em}</style>
<link href=\"css/style.css\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
</head>
<body>
<header>
    <nav class=\"navbar navbar-dark fixed-top bg-dark\">
    <a class=\"navbar-brand\" href=\"/\">$NAME_OF_PROJECT</a>
    <button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbar_new_loc\" aria-controls=\"navbar_new_loc\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
    <span class=\"navbar-toggler-icon\"></span>
    </button>
    <div class=\"collapse navbar-collapse\" id=\"navbar_new_loc\">
    <ul class=\"navbar-nav mr-auto\">
        <li class=\"nav-item active\">
        <a class=\"nav-link\" href=\"/\">Home <span class=\"sr-only\">(current)</span></a>
        </li>
        <li class=\"nav-item\">
        <a class=\"nav-link\" href=\"//localhost/phpmyadmin/\" target=\"_blank\">phpMyAdmin</a>
        </li>
        <li class=\"nav-item\">
        <a class=\"nav-link disabled\" href=\"#\">Disabled</a>
        </li>
        <li class=\"nav-item dropdown\">
        <a class=\"nav-link dropdown-toggle\" href=\"https://example.com\" id=\"dropdown01\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Dropdown</a>
        <div class=\"dropdown-menu\" aria-labelledby=\"dropdown01\">
            <a class=\"dropdown-item\" href=\"info.php\" target=\"_blank\">PHP info</a>
            <a class=\"dropdown-item\" href=\"#\">Another action</a>
            <a class=\"dropdown-item\" href=\"#\">Something else here</a>
        </div>
        </li>
    </ul>
    <form class=\"form-inline my-2 my-md-0\">
        <input class=\"form-control\" type=\"text\" placeholder=\"Search\" aria-label=\"Search\">
    </form>
    </div>
</nav>   
</header>" >> $DIR_HOST/$NAME_OF_PROJECT/www/head.inc
touch $DIR_HOST/$NAME_OF_PROJECT/www/footer.inc
echo "</main>
<!-- FOOTER -->
<footer class=\"footer\">
<div class=\"container\">
<span class=\"float-right text-muted\"><a href=\"#\">Вверх</a></span>
<span class=\"text-muted\">&copy; 2017-2019 Webhat  Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href=\"#\">Terms</a></span>
</div>
</footer>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src=\"js/jquery-slim.min.js\"></script>
<script src=\"js/popper.min.js\"></script>
<script src=\"js/bootstrap.min.js\"></script>
<!-- Just to make our placeholder images work. Don't actually copy the next line! -->
<script src=\"js/holder.min.js\"></script>
</body>
</html>" >> $DIR_HOST/$NAME_OF_PROJECT/www/footer.inc
mkdir -p $DIR_HOST/$NAME_OF_PROJECT/www/css
mkdir -p $DIR_HOST/$NAME_OF_PROJECT/www/images
cp ~/www/css/bootstrap.min.css $DIR_HOST/$NAME_OF_PROJECT/www/css
cp ~/www/css/bootstrap.min.css.map $DIR_HOST/$NAME_OF_PROJECT/www/css
cp ~/www/images/favicon.svg $DIR_HOST/$NAME_OF_PROJECT/www
cp ~/www/images/favicon-safari-pin.svg $DIR_HOST/$NAME_OF_PROJECT/www
cp ~/www/images/favicon.ico $DIR_HOST/$NAME_OF_PROJECT/www
touch $DIR_HOST/$NAME_OF_PROJECT/www/css/style.css
echo "/*--
	Author: WebHat Web-Studio
	Author URL: http://webhat.by
	License: Free
	License URL: 
--*/

html {
  position: relative;
  min-height: 100%;
}

/* Show it is fixed to the top */
body {
    font-size: 100%;
    font-family: 'Dosis', sans-serif;
    background: #fff;
    margin-bottom: 60px;
    min-height: 75rem;
    padding-top: 4.5rem;
}
body a {
    text-decoration: none;
    transition: 0.5s all;
    -webkit-transition: 0.5s all;
    -moz-transition: 0.5s all;
    -o-transition: 0.5s all;
    -ms-transition: 0.5s all;
    font-family: 'Dosis', sans-serif;
}

a:hover {
    text-decoration: none;
}
a,a:focus,a:hover,a:active,a:visited {
    outline-style: none;
}
input[type=\"button\"],
input[type=\"submit\"],
input[type=\"text\"],
input[type=\"email\"],
input[type=\"search\"] {
    transition: 0.5s all;
    -webkit-transition: 0.5s all;
    -moz-transition: 0.5s all;
    -o-transition: 0.5s all;
    -ms-transition: 0.5s all;
    font-family: 'Dosis', sans-serif;
}

h1,
h2,
h3,
h4,
h5,
h6 {
    margin: 0;
    font-family: 'Dosis', sans-serif;
    letter-spacing: 1px;
    font-weight: 500;
}

p {
    font-size: 0.9em;
    color: #8c9398;
    line-height: 2em;
    letter-spacing: 1px;
}

ul {
    margin: 0;
    padding: 0;
}
.navbar-brand {
    text-transform: uppercase;
}
/*--/header --*/
.tooltop {
    position: relative;
    display: inline-block;
}
.tooltop .tooltiptext {
    visibility: hidden;
    width: 180px;
    background-color: #343a40;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 150%;
    left: 50%;
    margin-left: -75px;
    opacity: 0;
    transition: opacity 0.3s;
}
.tooltop .tooltiptext::after {
    content: \"\";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #343a40 transparent transparent transparent;
}
.tooltop:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
.footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  height: 60px; /* Set the fixed height of the footer here */
  line-height: 60px; /* Vertically center the text there */
  background-color: #343a40;
}
.text-muted {
    color: #fff !important;
}
" >> $DIR_HOST/$NAME_OF_PROJECT/www/css/style.css
mkdir -p $DIR_HOST/$NAME_OF_PROJECT/www/js
cp ~/www/js/jquery-slim.min.js $DIR_HOST/$NAME_OF_PROJECT/www/js
cp ~/www/js/popper.min.js $DIR_HOST/$NAME_OF_PROJECT/www/js
cp ~/www/js/bootstrap.min.js $DIR_HOST/$NAME_OF_PROJECT/www/js
cp ~/www/js/holder.min.js $DIR_HOST/$NAME_OF_PROJECT/www/js
touch $DIR_HOST/$NAME_OF_PROJECT/www/js/main.js
echo "/*--
	Author: WebHat Web-Studio
	Author URL: http://webhat.by
	License: Free
	License URL: 
--*/
" >> $DIR_HOST/$NAME_OF_PROJECT/www/js/main.js
touch $DIR_HOST/$NAME_OF_PROJECT/www/.htaccess
echo "Options +FollowSymLinks
Options -Indexes
ErrorDocument 404 /404.html
AddDefaultCharset UTF-8
DirectoryIndex index.html
AddHandler application/x-httpd-php .html .htm .php

#Для hoster.by
#AddHandler fcgid-script .php5 .php .php3 .php2 .phtml .html
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .php5
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .php
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .php3
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .php2
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .phtml
#FCGIWrapper /usr/local/cpanel/cgi-sys/php5 .html

<IfModule mod_rewrite.c>
RewriteEngine on
RewriteBase /
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.html\ HTTP/ 
RewriteRule ^index\.html$ / [R=301,L]
</IfModule>
" >> $DIR_HOST/$NAME_OF_PROJECT/www/.htaccess
touch $DIR_HOST/$NAME_OF_PROJECT/www/info.php
echo "<?php phpinfo();?>" >> $DIR_HOST/$NAME_OF_PROJECT/www/info.php

echo -e "\033[1mСтартовая страница создана!\033[0m";
else
echo -e "\033[0;31mСоздание стартовой страницы пропущено!\033[0m";
fi
# #добавляем правила в конфигурационый файл апача
# add_to_apache_conf="
# <VirtualHost *:8080>
# ServerName $NAME_OF_PROJECT
#         ServerAlias $NAME_OF_PROJECT
#         ServerAdmin admin@$NAME_OF_PROJECT
#         DocumentRoot $DIR_HOST/$NAME_OF_PROJECT/www
#         <Directory $DIR_HOST/$NAME_OF_PROJECT/>
#                 Options Indexes FollowSymLinks MultiViews
#                 AllowOverride All
# 		Require all granted
#         </Directory>
# 
#         ScriptAlias /cgi-bin/ $DIR_HOST/$NAME_OF_PROJECT/cgi-bin/
#         <Directory "$DIR_HOST/$NAME_OF_PROJECT/cgi-bin">
#                 AllowOverride All
#                 Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
#                 Order allow,deny
#                 Allow from all
#         </Directory>
# 
#         ErrorLog $DIR_HOST/$NAME_OF_PROJECT/logs/error.log
#         LogLevel warn
#         CustomLog $DIR_HOST/$NAME_OF_PROJECT/logs/access.log combined
# </VirtualHost>"

add_to_hosts_conf="127.0.0.1 $NAME_OF_PROJECT"

add_to_nginx_conf="server {
	listen 80;
#	listen [::]:80;
    charset utf-8;
    client_max_body_size  300m;

    root $DIR_HOST/$NAME_OF_PROJECT/www/;
    index index.html index.htm index.php;

    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header x-xss-protection \"1; mode=block\" always;
    add_header X-Content-Type-Options \"nosniff\" always;

    server_name $NAME_OF_PROJECT;
    access_log $DIR_HOST/$NAME_OF_PROJECT/logs/nginx_access.log;
    error_log $DIR_HOST/$NAME_OF_PROJECT/logs/nginx_error.log;   

    location ~* \.(jpg|jpeg|gif|png|ico|eot|ttf|woff|woff2|css|bmp|svg|swf|webp|pdf|js)$ {
        root $DIR_HOST/$NAME_OF_PROJECT/www/;
    }    
   
$suma
$to_opencart
    location ~ \.(php|html)$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php$PHP_FPM-fpm.sock;
    fastcgi_send_timeout 300;
    fastcgi_read_timeout 300;
#    fastcgi_param  HTTP_MOD_REWRITE  On;
    }   
$to_prestashop 
    ### SECURITY - Protect crucial files
location ~* \/\.ht {
		deny all;
	}	
	
	location ~* (\.tpl|.twig|\.ini|\.log|(?<!robots)\.txt) {
		deny all;
	}
	
	location ~* \/\.git {
		deny all;
	}
	
	location ~* \/image.+(\.php) {
		deny all;
	}

	location = /favicon.ico {
		log_not_found off;
		access_log off;
	}

	location = /robots.txt {
		allow all;
		log_not_found off;
		access_log off;
	}	

    location ~ /(COPYRIGHT|LICENSE|README|htaccess)\.txt {
	    deny  all;
    }

    ### Большинство хакерских сканеров
    if ( \$http_user_agent ~* (nmap|nikto|wikto|sf|sqlmap|bsqlbf|w3af|acunetix|havij|appscan) ) {
        return 403;
    }

$to_processwire
}"

#Добавляем новый хост
sudo sh -c "echo '$add_to_hosts_conf' >> /etc/hosts"
# sudo sh -c "touch /etc/apache2/sites-available/$NAME_OF_PROJECT\.conf"
# sudo sh -c "echo '$add_to_apache_conf' >> /etc/apache2/sites-available/$NAME_OF_PROJECT\.conf"
# #Включаем конфигурацию сайта
# sudo a2ensite $NAME_OF_PROJECT
# #sudo a2dissite

#Добавляем новый хост в nginx
sudo sh -c "touch /etc/nginx/sites-available/$NAME_OF_PROJECT"
sudo sh -c "echo '$add_to_nginx_conf' >> /etc/nginx/sites-available/$NAME_OF_PROJECT"
#Включаем хост в nginx
sudo ln -s /etc/nginx/sites-available/$NAME_OF_PROJECT /etc/nginx/sites-enabled/$NAME_OF_PROJECT

#Создаем БД
echo -e "\033[1mСоздать БД для проекта?(yes/no)\033[0m"
read CREATE_DB

if  [ "$CREATE_DB" = "yes" -o "$CREATE_DB" = "y" -o "$CREATE_BAZA" = "YES" ]; then
	echo -e '\033[1mВведите имя базы данных(символы "-" будут заменены на "_"):\033[0m';
	read DB_NAME
	DB_NAME=${DB_NAME//[^a-zA-Z0-9]/_}
	echo -e "\033[1mВедите пароль для нового пользователя ${DB_NAME} который будет обладать всем правами на вновь созданную базу:\033[0m";
#	read -s DB_PASS
    read DB_PASS
	# Создаем базу данных имя которой мы ввели
echo -e "\033[1mТеперь будет необходимо ввести пароль $USER MySQL, если пароля нет, просто нажмите Enter\033[0m";
#	mysql -u$USER -p -e "CREATE DATABASE ${DB_NAME};"
	# Создаем нового пользователя. Используем замену символов в переменной. заменяем "-" на "_" вот так ${имя переменной/-/_} заменит первое вхождение "-". ${имя переменной//-/_} заменит все.
#	mysql -u$USER -p -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO ${DB_NAME}@localhost IDENTIFIED by '${DB_PASS}'  WITH GRANT OPTION;FLUSH PRIVILEGES;"
# echo -e "CREATE USER ${DB_NAME}@localhost IDENTIFIED BY '${DB_PASS}';"
# 	sudo mysql -e "CREATE DATABASE ${DB_NAME};"
# 	sudo mysql -e "CREATE USER ${DB_NAME}@localhost IDENTIFIED BY '${DB_PASS}';"
# 	# Создаем нового пользователя. Используем замену символов в переменной. заменяем "-" на "_" вот так ${имя переменной/-/_} заменит первое вхождение "-". ${имя переменной//-/_} заменит все.
# 	sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO ${DB_NAME}@localhost IDENTIFIED by '${DB_PASS}'  WITH GRANT OPTION;"
#     sudo mysql -e "FLUSH PRIVILEGES;"
# 	
    echo "Please enter $USER user MySQL password!"
    echo "Note: password will be hidden when typing"
#    echo "Примечание: пароль будет скрыт при вводе"
#    read -s rootpasswd
    read rootpasswd
#    mysql -u$USER -p${rootpasswd} -e "CREATE DATABASE ${DB_NAME} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
    mysql -u$USER -p${rootpasswd} -e "CREATE DATABASE ${DB_NAME};"
    mysql -u$USER -p${rootpasswd} -e "CREATE USER ${DB_NAME}@localhost IDENTIFIED BY '${DB_PASS}';"
    mysql -u$USER -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_NAME}'@'localhost' WITH GRANT OPTION;"
    mysql -u$USER -p${rootpasswd} -e "FLUSH PRIVILEGES;"	
	

echo -e "\033[1mБаза данных для $NAME_OF_PROJECT создана.\033[0m";

else
     echo -e "\033[1mБаза данных не была создана\033[0m";
fi

sudo chmod -R 777 $DIR_HOST/$NAME_OF_PROJECT/www
# echo "Перезапускаем apache и nginx..."
echo "Перезапускаем nginx..."
#перезапускаем апач и nginx
#sudo systemctl reload apache2 && sudo systemctl reload nginx && sudo systemctl restart php$PHP_FPM-fpm.service
sudo systemctl reload nginx && sudo systemctl restart php$PHP_FPM-fpm.service
echo -e "\033[1mСоздаем конфигурационный файл проекта\033[0m";
touch $DIR_HOST/$NAME_OF_PROJECT/config.txt
echo -e "Name Project: http://$NAME_OF_PROJECT\nDB_NAME:${DB_NAME//-/_}\nDB_USER:${DB_NAME//-/_}\nDB_PASS:$DB_PASS\nDirectory Project:$DIR_HOST/$NAME_OF_PROJECT/www" >> $DIR_HOST/$NAME_OF_PROJECT/config.txt
echo "*************************************"
echo -e "\033[1mЛокальный сайт http://$NAME_OF_PROJECT готов к работе.Файл конфигурации находится в $DIR_HOST/$NAME_OF_PROJECT/  \n\033[0;34mДля завершения работы, \033[0;31mнажмите Enter...\033[0m";
fi
}

# ─── Основной цикл ───────────────────────────────────────────────
while true; do
    run_script

    echo ""
    echo -e "${SET_BOLD}Вы действительно хотите выйти? [y/n]${SET_CLOSE}"
    read -r item

    case "$item" in
        y|Y)
            echo -e "${SET_RED}Выходим...${SET_CLOSE}"
            exit 0
            ;;
        n|N)
            echo -e "${SET_GREEN}Ок, продолжаем работу.${SET_CLOSE}"
            ;;
        *)
            echo -e "${SET_RED}Неверный ввод. Продолжаем по умолчанию.${SET_CLOSE}"
            ;;
    esac
done
