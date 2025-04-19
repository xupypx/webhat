<?php namespace ProcessWire;



$starto = microtime(true);
$cssFiles = [
    $_SERVER['DOCUMENT_ROOT'] . '/site/templates/styles/fonts/Oswald/stylesheet.css',
    $_SERVER['DOCUMENT_ROOT'] . '/site/templates/styles/fonts/Roboto/stylesheet.css',
    $_SERVER['DOCUMENT_ROOT'] . '/site/templates/styles/fonts/ttdayssans/stylesheet.css',
    $_SERVER['DOCUMENT_ROOT'] . '/site/templates/styles/main.css',
];

// Список JavaScript-файлов
$jsFiles = [
    $_SERVER['DOCUMENT_ROOT'] . '/site/templates/js/theme-change.js',
//  $_SERVER['DOCUMENT_ROOT'] . '/site/templates/scripts/plugins.js',
];

// Минификация и кэширование CSS
$cssCacheKey = 'minified_css_' . md5(implode('', array_map('md5_file', $cssFiles)));
$minifiedCssMain = $cache->get($cssCacheKey, 3600, function() use ($cssFiles) {
    $combinedCss = '';
    foreach ($cssFiles as $cssFile) {
        if (file_exists($cssFile)) {
            $combinedCss .= file_get_contents($cssFile);
        }
    }
    return minifyCss($combinedCss);
});

// // Минификация и кэширование JavaScript
// $jsCacheKey = 'minified_js_' . md5(implode('', array_map('md5_file', $jsFiles)));
// $minifiedJs = $cache->get($jsCacheKey, 3600, function() use ($jsFiles) {
//     $combinedJs = '';
//     foreach ($jsFiles as $jsFile) {
//         if (file_exists($jsFile)) {
//             $combinedJs .= file_get_contents($jsFile);
//         }
//     }
//     return minifyJs($combinedJs);
// });

// Выводим минифицированный CSS
//echo "<style id='combined_css' pw-append>{$minifiedCssMain}</style>";

// Выводим минифицированный JavaScript
//echo "<script>{$minifiedJs}</script>";

// _main.php template file, called after a page’s template file	
$home = pages()->get('/'); // homepage
$siteTitle = mb_convert_case($_SERVER['SERVER_NAME'], MB_CASE_TITLE, 'UTF-8');
$siteTagline = 'Окна ПВХ. Оконные конструкции из ПВХ и алюминия';
$logo = '<img src="'.urls()->templates.'styles/images/logo.svg" alt="coffee">';
// as a convenience, set location of our 3rd party resources (Uikit and jQuery)...
urls()->set('uikit', 'wire/modules/AdminTheme/AdminThemeUikit/uikit/dist/');
urls()->set('jquery', 'wire/modules/Jquery/JqueryCore/JqueryCore.js');
// ...or if you prefer to use CDN hosted resources, use these instead:
// urls()->set('uikit', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/');
// urls()->set('jquery', 'https://code.jquery.com/jquery-2.2.4.min.js'); 

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?><!DOCTYPE html>
<html lang='ru'>
<head id='html-head'>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title id='html-title'><?=page()->seo_title ? page()->seo_title : page()->get('headline|title')?> | <?= $siteTagline ?></title>
	<meta name="description" content="<?=page()->summary ?: page()->get('headline|title'). '. Изготовление и установка пластиковых окон в Минске, с установкой и монтажом. Дешевые окна - скидки, акции, распродажа стеклопакетов ПВХ'?>">

	<link rel="stylesheet" href="<?=urls()->templates?>styles/uikit.min.css" />
	<link rel="icon" href="<?='//' . $_SERVER['HTTP_HOST']?>/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://www.google.com">
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
<?php if(page()->template == 'home'): ?>
    <link rel="preload" href="/site/templates/images/header-bg1.webp" as="image" fetchpriority="high">
    <link rel="preload" href="/site/templates/images/header-bg2.webp" as="image">
    <link rel="preload" href="/site/templates/images/header-bg3.webp" as="image">
<?php endif;?>
    <meta name="ICBM" content="53.844440, 27.474097" />
    <meta name="geo.position" content="53.844440, 27.474097" />
    <meta name="geo.region" content="BY" />
    <meta name="geo.placename" content="Минск" />
	<meta property="og:type" content="website"/>
	<meta property="og:title" content="<?=page()->seo_title ? page()->seo_title : page()->get('headline|title')?> | <?= $siteTagline ?>" />
	<meta property="og:description" content="<?=page()->summary ?: page()->get('headline|title'). '. Изготовление и установка пластиковых окон в Минске, с установкой и монтажом. Дешевые окна - скидки, акции, распродажа стеклопакетов ПВХ'?>" />
	<meta property="og:url" content="https://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>" />
	<meta property="og:site_name" content="<?= $siteTagline ?>">
	<meta property="og:image" content="https://<?=$page->images && $page->images->count() ? $_SERVER['HTTP_HOST'].$page->images->first()->url : $_SERVER['HTTP_HOST'].'/site/templates/images/logo.svg'?>" />
	<meta property="og:image:width" content="<?=$page->images && $page->images->count() ? $page->images->first()->width :'348'?>">
	<meta property="og:image:height" content="<?=$page->images && $page->images->count() ? $page->images->first()->height :'52'?>">
	<meta property="og:image:type" content="image/<?=$page->images && $page->images->count() ? $page->images->first()->ext :'svg+xml'?>">
	<meta name="theme-color" content="#d4e5ea">
<style id="style_main">
<?php echo $minifiedCssMain;
$css = <<<CSS
.ober { color:var(--hh-color);}
.oberfull { color:#046380;}
.uk-navbar-nav > li > a {
  padding: 0 0;
  font-family: Oswald;
  color: var(--hh-color);
  font-size:1rem;
  font-weight: 500;
  text-transform: uppercase;
  transition: .3s ease-in-out;
  transition-property: all;
  transition-property: color,background-color;
}

.nav-fixed .uk-navbar-nav > li > a:hover,.uk-navbar-nav > li > a:hover {
/* 	color:#046380; */
	color: var(--link-none);
	font-weight: 500;
}
.fixed-top {
  position: fixed;
  background-color: var(--bg-tops);
  top: 0;
  right: 0;
  left: 0;
  transition: .3s ease-in-out;
  z-index: 2;
}
.nav-fixed .uk-navbar-nav > li > a { color: var(--hh-color);}

/*.fulls {
  background: var(--bgtops-gradient), url(/site/templates/images/header-bg2.webp)no-repeat center;
  background-size: cover;
  position: relative;
  padding: 12vw 0 0;

}*/
.fulls {
  background: var(--bgtops-gradient), url(/site/templates/images/header-bg1.webp) no-repeat center;
  background-size: cover;
  position: relative;
  padding: 12vw 0 0;
  transition: opacity .3s ease-in-out; /* Плавное изменение opacity */
}
.fulls::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: white; /* Белый фон для перехода */
  z-index: 1;
  opacity: 0;
  transition: opacity .3s ease-in-out;
}
.nav-fixed  {
 background-color: var(--heading-bg);

  transition: 1s ease-in-out;
}
.shape path {
  fill: var(--bg-color);
}
/*.hero-overlay {
  background: transparents;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}*/

.main-margintop { margin-top: 80px !important;}
.uk-navbar-dropdown-nav > li > span, .uk-navbar-dropdown-nav > li > a {
  color: #000;
}
.uk-navbar-dropdown { padding: 0;}
sup { color:var(--primary-color) !important;font-weight: bold;}

#totop-button {
    transition: opacity 0.3s ease-in-out;
    z-index:999;
}

#totop-button[hidden] {
    opacity: 0;
    pointer-events: none;
}
.uk-section-muted { background:var(--bg-section-muted);}
.uk-slidenav {
  color: rgba(255, 255, 255, 0.7);
}
.uk-lightbox-panel .uk-close {
  color: rgba(255, 255, 255, 0.7);
}
.uk-slidenav:hover,.uk-lightbox-panel .uk-close:hover {
  color: rgb(255, 255, 255);
}
#kak_vybrat {scroll-margin-top: 100px;}

.agreement-block {
    opacity: 0;
    height: 0;
    overflow: hidden;
    transition: opacity 0.3s ease, height 0.3s ease;
}

.agreement-block.visible {
    opacity: 1;
    height: auto; /* Используем auto для динамической высоты */
}
.Telegram:hover span{ color: blue;}
CSS;

$cacheKey = 'minified_css_' . md5($css);
$minifiedCss = $cache->get($cacheKey, 3600, function() use ($css) {
    return minifyCss($css);
});
echo $minifiedCss;
?>
</style>
</head>
<body id='html-body'>
<?php include('_svg-in.php');?>
	<!-- MASTHEAD -->
	<header  id="site-header" class='fixed-top'>
		<div id='masthead' class="uk-container">
			<nav id='masthead-navbar' class="" uk-navbar="align: center;boundary: !.uk-navbar-nav; stretch: x; flip: false; animation: slide-left; animate-out: true; duration: 700">
			<div id='masthead-logo'>
			<?=($page->id == '1' ? '<span><svg class="rolki"><use xlink:href="#logo"></use></svg></span>':'<a href="/"><svg class="rolki" ><use xlink:href="#logo"></use></svg></a>')?>
			</div>
				<div class="uk-navbar-right uk-visible@m">

<?php if(MENU_DROPDOWN):?>
<?php echo ukNavbarNav($home->and($home->children), [ 'dropdown' => [ 'basic-page', 'categories', 'product-cat' ]]);?>
<?php else:?>
					<ul class="uk-navbar-nav">

					<?php foreach($home->children as $item):if($item->show_in_menu == true):?>
					<?=($item->id == wire('page')->id ? '<li class="uk-active"><span class="link-drop-none">'.$item->title.'</span></li>':'<li><a href="'.$item->url.'">'.$item->title.'</a></li>')?>

					<?php endif;endforeach;?>
					</ul>
<?php endif;?>
				</div>
			</nav>
		</div>
				<!-- toggle switch for light and dark theme -->
		<div class="mobile-position">
			<nav class="navigation">
				<div class="theme-switch-wrapper">
					<label class="theme-switch" for="checkbox">
						<input type="checkbox" id="checkbox">
						<div class="mode-container">
							<i class="gg-sun"></i>
							<i class="gg-moon"></i>
						</div>
					</label>
				</div>
			</nav>
		</div>
	</header>
<?php if(page()->template == 'home'):?>

    <div class="fulls" id="fulls" style="background-image: var(--bgtops-gradient), url(/site/templates/images/header-bg1.webp)">
        <div class="uk-container for-banner-text">
            <div class="banner-text">
                <h1><?=page()->h1_for_home?></h1>
                <h2 class="uk-margin-remove"><?=page()->headline?></h2>
                <p class="uk-text-danger"><?=page()->infohead?></p>
                <div class="phone-main uk-child-width-expand@s uk-grid-collapse uk-text-center" uk-grid>
                    <div>
                        <svg class="uk-text-danger"><use xlink:href="#phone-1"></use></svg>
                        <a href="tel:<?=str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_a1)?>"><?=$contacts->phone_a1?></a>
                    </div>
                    <div>
                        <svg class="uk-text-danger"><use xlink:href="#phone-1"></use></svg>
                        <a href="tel:<?=str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_mts)?>"><?=$contacts->phone_mts?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="shape">
            <!-- Оптимизированная SVG анимация -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 280" aria-hidden="true">
                <path fill-opacity="1">
                    <animate attributeName="d" dur="20000ms" repeatCount="indefinite"
                             values="M0,160L48,181.3C96,203,192,245,288,261.3C384,277,480,267,576,234.7C672,203,768,149,864,117.3C960,85,1056,75,1152,90.7C1248,107,1344,149,1392,170.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                                    M0,160L48,181.3C96,203,192,245,288,234.7C384,224,480,160,576,133.3C672,107,768,117,864,138.7C960,160,1056,192,1152,197.3C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                                    M0,64L48,74.7C96,85,192,107,288,133.3C384,160,480,192,576,170.7C672,149,768,75,864,80C960,85,1056,171,1152,181.3C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                                    M0,160L48,181.3C96,203,192,245,288,261.3C384,277,480,267,576,234.7C672,203,768,149,864,117.3C960,85,1056,75,1152,90.7C1248,107,1344,149,1392,170.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;">
                    </animate>
                </path>
            </svg>
        </div>
    </div>

    <!-- Отложенная загрузка JavaScript -->
    <script defer>
    document.addEventListener('DOMContentLoaded', function() {
        const fullsElement = document.getElementById('fulls');
        const images = [
            '/site/templates/images/header-bg1.webp',
            '/site/templates/images/header-bg2.webp',
            '/site/templates/images/header-bg3.webp'
        ];

        // Предзагрузка следующих изображений
        images.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img;
            document.head.appendChild(link);
        });

        let currentIndex = 0;

        function changeBackground() {
            fullsElement.style.opacity = 0;

            setTimeout(() => {
                currentIndex = (currentIndex + 1) % images.length;
                fullsElement.style.backgroundImage = `var(--bgtops-gradient), url(${images[currentIndex]})`;
                fullsElement.style.opacity = 1;
            }, 200);
        }

        // Запускаем смену фона только после полной загрузки страницы
        window.addEventListener('load', function() {
            setTimeout(changeBackground, 7000);
            setInterval(changeBackground, 7000);
        });
    });
    </script>
<?php endif;?>


	<!-- MAIN CONTENT -->
	<main id='main' class='uk-container uk-margin uk-margin-large-bottom <?php if(page()->template != 'home')echo 'main-margintop';?>'>
		<?php if(page()->parent->id > $home->id) echo ukBreadcrumb(page(), [ 'class' => 'uk-visible@m uk-margin-remove-bottom' ]); ?>


		<div class='uk-grid uk-grid-large' uk-grid>
			<div id='content' class='uk-width-expand'>
				<h1 id='content-head' class='uk-margin-small-top'>
					<?=page()->get('headline|title')?>
				</h1>
				<div id='content-body'>
					<?=page()->body?>
				</div>
			</div>
			<aside id='sidebar' class='uk-width-1-3@m'>
				<?=page()->sidebar?>
			</aside>
		</div>
<?php //include('_modals.php');?>

	</main>

<?php if($page->name != 'contacts'):?>

<?php include('_how-order.php');?>

<?php endif;?>

<?php include('apps/galereya_in.php');?>

<?php if($page->name != 'contacts'):?>
<section class="uk-section bg-style-rolki">
<div class="uk-container uk-text-center">
<h2 class="">Хотите записаться на замер? </h2>
<p>Наши специалисты свяжутся с Вами для уточнения даты и времени замера.</p>
<form class="all uk-margin-auto" action="#" method="post">
    <input id="store" type="hidden" name="product" value="Запись на замер со страницы: <?=mb_strtolower($page->title)?>">
    <div class="uk-margin" uk-margin>
        <div uk-form-custom="target: true">
            <input class="radius-left uk-input uk-form-width-medium InputPhone" type="tel" placeholder="Ваш номер телефона" name="phone" aria-labelledby="Ваш номер телефона" required>
        </div>
        <button type="submit" class="radius-right uk-button uk-button-primary">Отправить</button>
    </div>
    <!-- Блок с соглашением, изначально скрытый -->
    <div class="uk-margin agreement-block">
        <input class="uk-checkbox form_agree" type="checkbox" name="agreement" value="да" aria-labelledby="Согласен на обработку данных" required>
        <label class="form-in uk-form-label" for="form_agree">Согласен на обработку данных, <a href="/about/pravovaya-informatciya/">правовая информация</a></label>
    </div>
</form>

</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('.InputPhone');
    const agreementBlock = document.querySelector('.agreement-block');

    let isFocused = false; // Флаг для отслеживания фокуса

    // Показываем блок при фокусе
    phoneInput.addEventListener('focus', function() {
        isFocused = true;
        agreementBlock.style.height = agreementBlock.scrollHeight + 'px';
        agreementBlock.classList.add('visible');
    });

    // Скрываем блок при потере фокуса (с задержкой)
    phoneInput.addEventListener('blur', function() {
        isFocused = false;
        setTimeout(function() {
            if (!isFocused && !phoneInput.value.trim()) {
                agreementBlock.style.height = '0';
                agreementBlock.classList.remove('visible');
            }
        }, 100); // Задержка 100 мс
    });
});
</script>
</section>
<?php endif;?>

<?php include('_modal-call-form-order.php');?>

<?php include 'apps/g-maps.php';?>


<?php if(config()->debug && user()->isSuperuser()): // display region debugging info ?>
<section id='debug' class='uk-section uk-section-muted'>
    <div class='uk-container'>
        <!--PW-REGION-DEBUG-->
    </div>
</section>
<?php endif; ?>

	<!-- FOOTER -->
<footer class='uk-section uk-section-secondary'  uk-sticky="position: bottom">
<div id='footer' class='uk-container'>
<div class="uk-child-width-1-2@s uk-child-width-1-4@m uk-grid-small uk-text-center" uk-grid>

    <div>
        <div class=""><span class="uk-margin-small-right" uk-icon="bookmark"></span>СТРАНИЦЫ</div>
        <div class="uk-flex uk-flex-center@m">
		<ul class="uk-list uk-text-left uk-margin-top">

		<?php foreach($home->children as $item):if($item->show_in_menu == true):?>
		<li><span class="uk-margin-small-right" uk-icon="arrow-right"></span><a href="<?=$item->url?>"><?=$item->title?></a></li>
		<?php endif;endforeach;?>

		</ul>
		</div>
    </div>
    <div>
        <div class=""><span class="uk-margin-small-right" uk-icon="info"></span>ИНФОРМАЦИЯ</div>
        <div class="uk-flex uk-flex-center@m">
		<ul class="uk-list uk-text-left uk-margin-top">

<?php foreach(pages()->get('/about/')->children as $item):if($item->show_in_menu == true):?>
		<li><span class="uk-margin-small-right" uk-icon="arrow-right"></span><a href="<?=$item->url?>"><?=$item->title?></a></li>
<?php endif;endforeach;?>
		</ul>
		</div>
    </div>
    <div class="uk-flex-first@m">
		<div class=""><span class="uk-margin-small-right uk-text-danger" uk-icon="users"></span>КОНТАКТЫ</div>
<ul class="uk-list uk-text-left">
	<li><span class="uk-text-bold suptm"><?=$contacts->name_org?> <span> <?=$contacts->reg_num?></span></span></li>
	<li><span class="uk-margin-small-right" uk-icon="location"></span><?=$contacts->adress_org?></li>
	<li><span class="uk-margin-small-right" uk-icon="receiver"></span><a href="tel:<?=str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_a1)?>"><?=$contacts->phone_a1?></a></li>
	<li><span class="uk-margin-small-right" uk-icon="receiver"></span><a href="tel:<?=str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_mts)?>"><?=$contacts->phone_mts?></a></li>
	<li><span class="uk-margin-small-right" uk-icon="clock"></span><?=$contacts->work_time?> - <span>без выходных</span></li>
	<li><span class="uk-margin-small-right" uk-icon="mail"></span><a href="mailto:<?=$contacts->email_org?>"><?=$contacts->email_org?></a></li>

</ul>
    </div>
    <div>
        <div class=""><span class="uk-margin-small-right uk-text-danger" uk-icon="social"></span>СОЦСЕТИ</div>
        <div class="uk-margin-top">
        <a title="Telegram" href="https://t.me/<?=str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_a1)?>" target="_blank" aria-label="Связь в Telegram"><span class="uk-margin-small-right" uk-icon="icon: telegram; ratio: 2"></span></a><a title="Viber" href="viber://chat?number=<?=substr(str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_a1),1)?>" target="_blank" aria-label="Связь в Viber"><span class="uk-margin-small-right"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512"><path d="M444 49.9C431.3 38.2 379.9 .9 265.3 .4c0 0-135.1-8.1-200.9 52.3C27.8 89.3 14.9 143 13.5 209.5c-1.4 66.5-3.1 191.1 117 224.9h.1l-.1 51.6s-.8 20.9 13 25.1c16.6 5.2 26.4-10.7 42.3-27.8 8.7-9.4 20.7-23.2 29.8-33.7 82.2 6.9 145.3-8.9 152.5-11.2 16.6-5.4 110.5-17.4 125.7-142 15.8-128.6-7.6-209.8-49.8-246.5zM457.9 287c-12.9 104-89 110.6-103 115.1-6 1.9-61.5 15.7-131.2 11.2 0 0-52 62.7-68.2 79-5.3 5.3-11.1 4.8-11-5.7 0-6.9 .4-85.7 .4-85.7-.1 0-.1 0 0 0-101.8-28.2-95.8-134.3-94.7-189.8 1.1-55.5 11.6-101 42.6-131.6 55.7-50.5 170.4-43 170.4-43 96.9 .4 143.3 29.6 154.1 39.4 35.7 30.6 53.9 103.8 40.6 211.1zm-139-80.8c.4 8.6-12.5 9.2-12.9 .6-1.1-22-11.4-32.7-32.6-33.9-8.6-.5-7.8-13.4 .7-12.9 27.9 1.5 43.4 17.5 44.8 46.2zm20.3 11.3c1-42.4-25.5-75.6-75.8-79.3-8.5-.6-7.6-13.5 .9-12.9 58 4.2 88.9 44.1 87.8 92.5-.1 8.6-13.1 8.2-12.9-.3zm47 13.4c.1 8.6-12.9 8.7-12.9 .1-.6-81.5-54.9-125.9-120.8-126.4-8.5-.1-8.5-12.9 0-12.9 73.7 .5 133 51.4 133.7 139.2zM374.9 329v.2c-10.8 19-31 40-51.8 33.3l-.2-.3c-21.1-5.9-70.8-31.5-102.2-56.5-16.2-12.8-31-27.9-42.4-42.4-10.3-12.9-20.7-28.2-30.8-46.6-21.3-38.5-26-55.7-26-55.7-6.7-20.8 14.2-41 33.3-51.8h.2c9.2-4.8 18-3.2 23.9 3.9 0 0 12.4 14.8 17.7 22.1 5 6.8 11.7 17.7 15.2 23.8 6.1 10.9 2.3 22-3.7 26.6l-12 9.6c-6.1 4.9-5.3 14-5.3 14s17.8 67.3 84.3 84.3c0 0 9.1 .8 14-5.3l9.6-12c4.6-6 15.7-9.8 26.6-3.7 14.7 8.3 33.4 21.2 45.8 32.9 7 5.7 8.6 14.4 3.8 23.6z" fill="var(--theme-white)" /></svg></span></a><a title="Whatsapp" href="https://api.whatsapp.com/send?phone=<?=substr(str_replace(['-', '.', '(', ')', ' '], '', $contacts->phone_a1),1)?>" target="_blank" aria-label="Связь в Whatsapp"><span class="uk-margin-small-right" uk-icon="icon: whatsapp; ratio: 2"></span></a>
        </div>
    </div>
</div>
</div>
<div class='uk-container copyright'>
	<div class="uk-grid-small uk-child-width-expand@s uk-text-center" uk-grid>
		<div>
			<small class='uk-text-small'>&copy; <?=date('Y')?> &bull;</small><a href='https://webhat.by' target="_blank">WebHat Studio</a>
		</div>
	</div>
</div>
</footer>

<!-- OFFCANVAS NAV TOGGLE -->
<a id='offcanvas-toggle' class='uk-hidden@m' href="#offcanvas-nav" uk-toggle>
	<?=ukIcon('menu', 1.3)?>
</a>

<!-- OFFCANVAS NAVIGATION -->
<div id="offcanvas-nav" uk-offcanvas>
	<div class="uk-offcanvas-bar">
		<h3><a href='<?=urls()->root?>'><?=$siteTitle?></a></h3>
		<?php
		// offcanvas navigation
		// example of caching generated markup (for 600 seconds/10 minutes)
		echo cache()->get('offcanvas-nav', 10, function() {
			return ukNav(pages()->get('/')->children(), [
				'depth' => 1,
				'accordion' => true,
				'blockParents' => [ 'blog' ],
				'repeatParent' => true,
				'noNavQty' => 20
			]);
		});
		?>
	</div>
</div>

<?php if(page()->editable): ?>
<!-- PAGE EDIT LINK -->
<a id='edit-page' href='<?=page()->editUrl?>'>
	<?=ukIcon('pencil')?> Edit
</a>
<?php endif; ?>


<div id="totop-button" class="uk-position-fixed uk-position-bottom-right uk-margin-xlarge-bottom uk-margin-medium-right" hidden>
    <a href="#" class="uk-icon-button uk-text-primary" uk-totop uk-scroll></a>
</div>


<!-- UIkit JS -->
<script src="<?=urls()->jquery?>"></script>
<script src="<?=urls()->templates?>js/jquery.maskedinput.min.js"></script>
<script defer src="<?=urls()->templates?>js/uikit.min.js"></script>
<script defer src="<?=urls()->templates?>js/uikit-icons.min.js"></script>


<script>
    function loadRecaptcha() {
        if (window.grecaptchaScriptLoaded) return;

        window.grecaptchaScriptLoaded = true;

        const script = document.createElement('script');
        script.src = "https://www.google.com/recaptcha/api.js?render=6LfwXwYrAAAAABoUX0zY-90Cc1wMgy-34EuZq72N";
        script.async = true;
        script.defer = true;
        script.onload = () => {
            document.querySelectorAll('form.all, form.CommentForm').forEach(form => {
                const recaptchaInput = document.createElement('input');
                recaptchaInput.type = 'hidden';
                recaptchaInput.name = 'recaptcha_response';
                form.appendChild(recaptchaInput);
                grecaptcha.ready(() => {
                    grecaptcha.execute('6LfwXwYrAAAAABoUX0zY-90Cc1wMgy-34EuZq72N', { action: 'submit' }).then(token => {
                        recaptchaInput.value = token;
                    });
                });
            });
        };

        document.body.appendChild(script);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('form.all, form.CommentForm');
        forms.forEach(form => {
            form.addEventListener('focusin', loadRecaptcha, { once: true });
            form.addEventListener('mouseenter', loadRecaptcha, { once: true });
        });
    });
</script>




<script id="scripts"><?php
// // Список JavaScript-файлов
// $jsFiles = [
//     $_SERVER['DOCUMENT_ROOT'] . '/site/templates/js/theme-change.js',
// //  $_SERVER['DOCUMENT_ROOT'] . '/site/templates/scripts/plugins.js',
// ];
// Встроенный JavaScript-код
$inlineJs = <<<JS
window.addEventListener("scroll", function(){
  var element1 = document.querySelector(".fixed-top");
  var element2 = document.querySelector("#offcanvas-toggle");


  if(window.scrollY > 80){
    element1.classList.add("nav-fixed");
    element2.classList.add("nav-fixed");

  } else {
    element1.classList.remove("nav-fixed");
	element2.classList.remove("nav-fixed");

  }
});

document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll('.ajax-form, form.all');

  forms.forEach(form => {
    const submitButton = form.querySelector('.submit-button') || form.querySelector('button[type="submit"]');
    const messageBox = form.closest('.uk-modal-body')?.querySelector('.form-message') || document.createElement('div');
    const phoneInput = form.querySelector('.InputPhone'); // Находим поле телефона
    const agreementBlock = form.querySelector('.agreement-block'); // Находим блок с соглашением

    // Если сообщение не существует, создаем его
    if (!form.closest('.uk-modal-body')) {
      form.appendChild(messageBox);
      messageBox.classList.add('uk-alert', 'form-message');
      messageBox.style.display = 'none';
    }

    // Очистка сообщения при открытии модального окна
    const modal = form.closest('.uk-modal');
    if (modal) {
      UIkit.util.on(modal, 'shown', function () {
        messageBox.style.display = 'none'; // Скрываем сообщение
        messageBox.textContent = ''; // Очищаем текст
        messageBox.classList.remove('uk-alert-success', 'uk-alert-danger'); // Убираем стили
        form.reset(); // Сбрасываем форму
        submitButton.disabled = !form.checkValidity(); // Активируем/деактивируем кнопку
        submitButton.textContent = 'Отправить'; // Возвращаем текст кнопки
      });
    }

    // Валидация формы при вводе данных
    form.addEventListener('input', function () {
      submitButton.disabled = !form.checkValidity();
      submitButton.classList.toggle('uk-disabled', !form.checkValidity());
    });

    // Функция для скрытия блока с соглашением
    function hideAgreementBlock() {
      if (agreementBlock) {
        agreementBlock.style.height = '0';
        agreementBlock.classList.remove('visible');
      }
    }

    // Отправка формы
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      let formData = new FormData(form);
      submitButton.disabled = true;
      submitButton.textContent = 'Отправка...';

      fetch('<?=$page->url?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          messageBox.textContent = 'Ваша заявка успешно отправлена!';
          messageBox.classList.add('uk-alert-success');

          // Создаем элемент для закрытия
          const closeButton = document.createElement('a');
          closeButton.classList.add('uk-alert-close');
          closeButton.setAttribute('href', '#');
          closeButton.setAttribute('uk-close', '');

          // Добавляем кнопку закрытия в messageBox
          messageBox.appendChild(closeButton);

          form.reset(); // Сбрасываем форму
          submitButton.disabled = true; // Блокируем кнопку после отправки
          submitButton.textContent = 'Отправить';

          // Скрываем блок с соглашением
          hideAgreementBlock();

          // Закрываем модальное окно через 2 секунды
          if (modal) {
            setTimeout(() => {
              UIkit.modal(modal).hide();
            }, 2000);
          }
        } else {
          messageBox.textContent = 'Ошибка при отправке. Попробуйте позже.';
          messageBox.classList.add('uk-alert-danger');
          submitButton.textContent = 'Отправить';
        }
        messageBox.style.display = 'inline-block';
        messageBox.setAttribute('uk-alert', '');
      })
      .catch(error => {
        messageBox.textContent = 'Ошибка соединения с сервером!';
        messageBox.classList.add('uk-alert-danger');
        messageBox.style.display = 'block';
        submitButton.textContent = 'Отправить';
      });
    });
  });
});

document.addEventListener('DOMContentLoaded', function() {
    var totopButton = document.getElementById('totop-button');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 500) {
            totopButton.removeAttribute('hidden');
        } else {
            totopButton.setAttribute('hidden', true);
        }
    });
});
JS;

// Объединяем содержимое внешних файлов и встроенного кода
$combinedJs = '';
foreach ($jsFiles as $jsFile) {
    if (file_exists($jsFile)) {
        $combinedJs .= file_get_contents($jsFile);
    }
}
$combinedJs .= $inlineJs;

// Ключ кэша на основе содержимого
$cacheKey = 'minified_combined_js_' . md5($combinedJs);

// Получаем минифицированный JavaScript из кэша или минифицируем заново
$minifiedJs = $cache->get($cacheKey, 3600, function() use ($combinedJs) {
    return minifyJs($combinedJs);
});
echo $minifiedJs;
?></script>
<?php //include('apps/modal_out.php');?>


<?php $end = microtime(true);
echo "/*Execution time: " . round(($end - $starto) * 1000, 2) . " ms*/";
// if (function_exists('curl_init')) { echo 'Работает';} else { echo 'Не работает';}
?>

</body>

</html>

