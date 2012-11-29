<?

// core Anastasia Engine v2.0 //
session_start();
/////////////debag /////////////
require 'debag.php';

function __autoload($class_name) {
    $a = explode('_', $class_name);
    if (file_exists(app::$ini['site_path'] . 'moduls/' . $a[0] . '/' . $a[1] . '.php')) {
        include app::$ini['site_path'] . 'moduls/' . $a[0] . '/' . $a[1] . '.php';
    }
}

class core_root {

// ядро
    //рендер вюхи  
    function render($v, $var) {
        $p = get_class($this);
        $p = explode('_', $p);
        $a = app::$ini['site_path'] . 'moduls/' . $p[0] . '/view/' . $v . '.php';
        ob_start();
        include $a;
        if (isset($var))
            app::$content->$var = ob_get_clean();
        else
            return ob_get_clean();
    }

    /// Кеширование
    // читаем кешь есле есть возврашаем запись есле нет false
// параметры $k - ключь

    function cash_get($k, $tip) {
        if (empty($tip))
            $tip = app::$ini['cache'];
        $ret = false;

//файл
        if ($tip == 1) {
            $file_cash = app::$ini['site_path'] . 'cache/' . $k . '.cash';
            $time_sec = time();
            $time_file = filemtime($file_cash);
            if ($time_file) {

                if ($time_file > $time_sec) {
                    $rHandle = fopen($file_cash, 'r');
                    $ret = fread($rHandle, filesize($file_cash));
                    fclose($rHandle);
                }
                else
                    unlink($file_cash);
            }
        }
        if ($tip == 2) {

            if (!app::$ini['memcache_con'])
                app::memcache();
            $ret = app::$memcache->get($k);
        }
        return $ret;
    }

// пишем кешь 
// параметры $k - ключь, $t - время в секундах харанения $d - данные
    function cache_set($k, $t, $d, $tip) {
        echo "set";

        if (empty($tip))
            $tip = app::$ini['cache'];

           
        if ($tip == 1) {
            $file_cash = app::$ini['site_path'] . 'cache/' . $k . '.cash';
            $time_sec = time() + $t;
            $rHandle = fopen($file_cash, 'w');
            fwrite($rHandle, $d);
            fclose($rHandle);
            chmod($file_cash, 0777);
            touch($file_cash, $time_sec);
        }
        if ($tip == 2) {
            if (!app::$ini['memcache_con'])
                app::memcache();
            app::$memcache->set($k, $d, $t);
        }
    }

// редирект
    function redirect($url = '') {
        if (headers_sent())
            print "<script>location='$url';</script>";
        else
            header('location: ' . $url);
    }

}

//------------- язык сайта
class p {

    static $p;

    static function load() {

// прописываем потдержываемые языки
        $site_language = app::$ini[language][0];
        $yaz = array_slice(app::$ini[language], 1);

        if (in_array(app::$r[0], $yaz)) {
            $site_language = app::$r[0];
            $router = array_slice(app::$r, 1);
            app::$ini['site_url'] = app::$ini['site_url'] . '/' . $site_language;
            app::$ini['router_str'] = implode('/', $router); //строка пути
        }

        app::$site_language = $site_language;
// $site_language - язык сайта
// подключаем файл перевода    
        include (app::$ini[site_path] . '/language/' . $site_language . '.php');
        self::$p = $p;
        unset($p);
    }

}

// подключаем расширения
require './ext/ext.php';

class modul extends core {

    function cache() {
        
    }

// модуль   
    function __construct() {

        // авто кеширование 
      
        $c = $this->cache();
        if (isset($c['content'])) {
            $key=get_class($this).app::$site_language.$c['key'];
            $time=60;
            if (isset(app::$ini['cache_time']))$time=app::$ini['cache_time'];
            if (isset($c['time']))$time=$c['time'];
            
            if ((app::$content->$c['content'] = $this->cash_get($key,$c['tip'])) == false) {
                $this->run();
                $this->cache_set($key, $time, app::$content->$c['content'],$c['tip']);
            }
        }
        else
            $this->run();
    }

}

class content {

    function render() {
        $a = app::$ini['site_path'] . 'layout/' . app::$ini['layout'] . '.php';
        include $a;
    }

}

require 'db.php';

class app {

    static $ini,
            $r_req,
            $r_file_ext,
            $r_file,
            $r_str,
            $r,
            $m,
            $f,
            $content,
            $db,
            $eror,
            $memcache,
            $site_language

    ;

    static function rout() {

        $router = trim($_SERVER['REQUEST_URI'], '/');
        self::$r_req = $router;
//----------------- разбиваем полученный урл ----------------- 
        $router = explode('?', $router); //отделяем динамику
        $router = trim($router[0], '/');
        $router = explode('/', $router); //разбиваем по слешам

        $last_route = count($router) - 1;
        if (stripos($router[$last_route], '.')) { // если в конце пути указан файл
            $last_route = array_pop($router);
            $route_file = explode('.', $last_route);
            self::$r_file_ext = $route_file[1]; //расширение файла
            self::$r_file = $route_file[0]; //имя файла
        }

        self::$r_str = implode('/', $router); //строка пути
        self::$r = $router;
    }

// подключение мемкеша    
    static function memcache() {
        self::$memcache = new Memcached();
        self::$memcache->addServer(self::$ini['memcache']['host'], self::$ini['memcache']['port']);
        self::$ini['memcache_con'] = 1;
    }

    function run() {
// старт приложения ////////////////
// подключаем ini-файл
        self::$ini = require 'ini.php';
// создаем обект контент
        self::$content = new content;
//режим отладки
        debag();

// БД
        self::$db = new db;
// роутер
        self::rout();
//  подключаем языки      
        p::load();


//подключение всех модулей по умолчанию
//           формирование ini массива
        foreach (self::$ini['moduls'] as $v) {
            if (file_exists(self::$ini['site_path'] . 'moduls/' . $v . '/ini.php')) {
                self::$ini['mod_ini'][$v] = include self::$ini['site_path'] . 'moduls/' . $v . '/ini.php';
                if (include self::$ini['site_path'] . 'moduls/' . $v . '/' . $i['def_file'] . '.php') {
                    $a = $v . '_' . self::$ini['mod_ini'][$v]['run'];
                    $$a = new $a;
                }
            }
        }


// собираем правила 
        foreach (self::$ini['moduls'] as $v) {
            if (file_exists(self::$ini['site_path'] . 'moduls/' . $v . '/rout.php')) {
                $modul_name = $v;
                require self::$ini['site_path'] . 'moduls/' . $v . '/rout.php';
            }
        }


// подключение модуля по запросу       
        if (!self::$f)
            self::$f = self::$m;
        if (self::$m && self::$f) {
            $a = self::$m . '_' . self::$f;
            $$a = new $a;
        } else {
            $_GET['error'] = '404';
        }
// вывод 404
        if ($_GET['error'] == '404') {
            echo '404';
        }

// вывод всего приложения        
        self::$content->render();
    }

}
