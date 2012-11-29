<?
// ini-файл ///
return array(
//mysql connection params
    'DB_DRIVER' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '123',
    'DB_NAME' => 'test',
    'DB_PR' => 'site_',
/// режим отладки 1 -да 0 - нет
    'DEBAG' => '1',
/// URL и путь сайта    
    'site_url' => 'http://' . $_SERVER['HTTP_HOST'],
    'site_path' => $_SERVER[DOCUMENT_ROOT] . '/',
// языки используемые на сайте 1 й по умолчанию
    'language' => array('ru', 'ua'),
// тип кеширования 0 - нет, 1 - файл , 2 - мемкеш
    'cache' => 2,
// время кеширования    
    'cache_time' => 20,
// параметры подключения к мемкушу
    'memcache' => array('host'=>'localhost','port'=>'11211'),    
// шаблон по умолчанию     
    'layout' => 'layout_main',
// meta даннные по умолчанию
    'meta_t' => 'test',
    'meta_d' => '',
    'meta_k' => '',
/// подключаемые модули  
    'moduls' => array('index', 'test')
);