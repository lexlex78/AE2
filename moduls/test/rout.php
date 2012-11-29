<?
// на входе
//        self::$r_req -  запрос
//        self::$r_file_ext - раширения файла
//        self::$r_file - имя файла
//        self::$r_str - строка запрсса
//        self::$r - масив пути
//        $modul_name - имя модуля

if (self::$r[0]=='test' && count(self::$r)==1){
  self::$m=$modul_name;
  self::$f='index';
}

//  на выходе self::$m  модуль
//            self::$f  файл
