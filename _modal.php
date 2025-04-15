<div class="modal fade" id="Modal1" aria-hidden="true" aria-labelledby="ModalLabel" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="ModalLabel">Связь с нами</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="row">
      <div class="col-xl-6 mb-3">
        Время работы: <?=$pages->get('/')->time_work?><br>        
		Адрес: <?=$pages->get('/')->adress?><br>
		Телефон: <a title="Позвонить" href="tel:<?=strip_tags(str_replace(array('.',' ', '(', ')', '-'), "", $pages->get('/')->phone));?>"><?=$pages->get('/')->phone?></a><br>
		Email: <a href='mailto:<?= $pages->get('/')->email ?>'><?=$pages->get('/')->email?></a><br>
		<a type="button" class="btn btn-primary btn-style mt-lg-2 mt-2" href="https://yandex.by/maps/157/minsk/?indoorLevel=1&ll=27.528331%2C53.903831&mode=routes&rtext=~53.904462%2C27.528084&rtt=auto&ruri=~&z=15.11" target="_blank">КАК К НАМ ДОЕХАТЬ <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-geo-alt-fill mb-1" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></a><br>
		<img alt="Марки автомобилей" class="img-fluid mt-3" src="/webhatby/images/all-auto.webp" width="1400" height="107" />
		</div>
		<div class="col-xl-6">
	  <div class="row  g-3 mt-4 justify-content-center">
<?php
/**
 * Блок "Случайные услуги" с оптимизированным выводом и кэшированием
 */

// Получаем страницу услуг (кэшируем запрос)
$servicesPage = $pages->get('/uslugi/');

// Если страница существует и у нее есть дети, выводим 4 случайные услуги
if ($servicesPage->id && $servicesPage->numChildren) {
    // Получаем случайные услуги (можно закэшировать, если часто вызывается)
    $randomServices = $servicesPage->children->getRandom(4);

    foreach ($randomServices as $service) {
        // Кэшируем данные для производительности
        $title = htmlspecialchars($service->title, ENT_QUOTES, 'UTF-8');
        $url = $service->url;
        $firstImage = $service->images->first();

        // Выводим карточку услуги
        ?>
        <div class="col-xl-3 col-lg-4 col-md-6" aria-labelledby="service-title-<?= $service->id ?>">
            <div class="card shadow mb-5 bg-body-tertiary rounded">
                <?php if ($firstImage): ?>
                    <div class="fit">
                        <img src="<?= $firstImage->webpUrl ?>"
                             class="card-img-top img-fluid fit-img"
                             alt="<?= $title ?>"
                             loading="lazy"
                             width="300"
                             height="200">
                    </div>
                <?php else: ?>
                    <!-- Заглушка, если нет изображения (можно стилизовать) -->
                    <div class="fit bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <span class="text-muted">Нет изображения</span>
                    </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column h-100">
                    <h5 class="card-title text-truncate" id="service-title-<?= $service->id ?>"><?= $title ?></h5>
                    <a class="stretched-link mt-auto" href="<?= $url ?>" aria-label="Подробнее об услуге: <?= $title ?>"></a>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // Сообщение, если нет услуг (можно заменить на заглушку)
    echo '<div class="col-12"><div class="alert alert-info">Услуги не найдены.</div></div>';
}
?>
	  </div>
		</div>
		</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-bs-target="#Modal" data-bs-toggle="modal">Предварительная запись</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="Modal" aria-hidden="true" aria-labelledby="ModalleLabel2" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="ModalleLabel2">Предварительная запись</h2>
        <button type="button" class="btn-close xlop" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="row">
      <div class="col-xl-6 mb-3">
         <form class="all-on" action="" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="product" value="Предварительная запись">
          <div class="row">
          <div class="col-md-6">
          <div class="form-floating mb-3">            
            <input type="date" class="form-control" name="quest" id="recipient-date" required>
            <label for="recipient-date">Выбрать дату:</label>
          </div>
          </div>
          <div class="col-md-6">
          <div class="form-floating mb-3">            
            <input type="text" class="form-control" name="name" id="recipient-name" placeholder="Вашe имя" required>
            <label for="recipient-name">Вашe имя:</label>
          </div>
          </div>
          </div>
          <div class="form-floating mb-3">            
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Ваш номер телефона" required>
            <label for="phone">Ваш телефон:</label>
          </div>
          <div class="form-floating mb-3">            
            <input type="text" class="form-control" name="email" id="email" placeholder="name@example.com">
            <label for="email">Ваш e-mail:</label>
          </div>
		  <div class="mb-3 form-floating">
			<select class="form-select" name="num" id="floatingSelect" aria-label="Выбрать вид работ">
				<option selected="">Выбрать</option>
				<?php foreach($pages->get('/uslugi/')->children as $item):?>
				<option value="<?=$item->title?>"><?=$item->title?></option>				
				<?php endforeach;?>
			</select>
			<label for="floatingSelect">Выбор вида работ</label>
		  </div>
          <div class="mb-3 form-floating">            
            <textarea class="form-control" name="message" id="message-text" placeholder="Дополнительная информация, которую вы хотели бы сообщить" ></textarea>
            <label for="message-text lead">Дополнительная информация:</label>
          </div>
          <button type="submit" class="btn btn-primary">Записаться</button>
        </form>
                                           
        </div>
      <div class="col-xl-6">С вами свяжется наш представитель
	  <div class="row  g-3 mt-4 justify-content-center">
	  <?php foreach($pages->get('/uslugi/')->children->getRandom(4) as $uslugi) echo '

	  <div class="col-xl-3 col-lg-4 col-md-6">
		  <div class="card h-100 shadow mb-5 bg-body-tertiary rounded">
			<div class="fit">
			  <img src="'. $uslugi->images->first()->webpUrl.'" class="card-img-top mg-fluid fit-img" alt="'. $uslugi->title .'">
			</div>
			  <div class="card-body align-bottom">
				  <h5 class="card-title">'. $uslugi->title .'</h5>
				  <a class="stretched-link" href="'.$uslugi->url.'"></a>
			  </div>
		  </div>
	  </div>

	  ';?>
	  </div>     
      </div>  
      </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-bs-target="#Modal1" data-bs-toggle="modal">Вернуться назад</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="staticBackdropLabel">Правовая информация</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<?=$pages->get('/')->lawinfo?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="staticBackdropLabel1">Условия оказания услуг</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?=$pages->get('/')->public_offer?> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel2" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="staticBackdropLabel2">Гарантии</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?=$pages->get('/')->warranty?>
        <img alt="Гарантия на работы" class="img-fluid" src="/webhatby/images/warranty.webp" width="825" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel3" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="staticBackdropLabel3">Вопросы и ответы</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<h3 class="title-wbh mb-lg-5 mb-4">Часто задаваемые вопросы</h3>
		<div class="accordion">
		<?php foreach($pages->get("/")->faq as $key => $faq):?>
		  <div class="accordion-item">
			<button id="accordion-button-<?=$faq->id?>" aria-expanded="<?=($faq->show == 1 ? 'true':'false')?>"><span class="accordion-title"><?=$faq->title?></span><span class="icon" aria-hidden="true"></span></button>
			<div class="accordion-content">
			  <p><?=$faq->summary?></p>
			</div>
		  </div>
		  <?php endforeach;?>

		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<div id="modal-bank"></div>
