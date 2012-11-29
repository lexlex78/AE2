<?php
function debag (){
if (app::$ini['DEBAG']==1){
    error_reporting(E_ALL);
    global $timer;
// клас замера времени работы скрипта
require 'timer.php';
$timer = new timer();
$timer->start_timer();

}
else error_reporting(0);
}
function debag_end (){
if (app::$ini['DEBAG']==1){
    global $timer;
echo '<div style="position: fixed;z-index: 99999;
    padding: 6px; bottom:10px;left: 10px;  opacity: 0.7;
    color: #0066FF; background: #fff;
    border: #ccc 1px solid;" >Режим разработки<br>';
echo round($timer->end_timer(),6);
echo  ' сек <br>';
echo memory_get_usage();
echo  ' байт <br>';
$tt='нет';if (app::$db->con==1)$tt='да';
echo 'БД подключение - '.$tt.'<br>';
echo 'БД запросов - '.app::$db->i.'<br>';
echo '<div style="color: #f00;">'.app::$eror.'</div>';
echo "</div>";

}    
}
register_shutdown_function (debag_end);
