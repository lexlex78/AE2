<?php

Class db {

    var $con = 0;
    var $i = 0;
    static $db;

    function connect() {

        // соединяемся с базой данных
        try {
            $connect_str = app::$ini['DB_DRIVER'] . ':host=' . app::$ini['DB_HOST'] . ';dbname=' . app::$ini['DB_NAME'];
            self::$db = new PDO($connect_str, app::$ini['DB_USER'], app::$ini['DB_PASS']);
            self::$db->query('SET NAMES utf8');
            $this->con = 1;
        } catch (Exception $e) {
            app::$eror.=$e->getMessage();
        }
    }

    function sel($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->prepare($sel);
        $a->execute($par);
        $b = $a->errorInfo();
        if ($b)
            app::$eror.=$b[2];
        $this->i++;
        return $a->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function sel_one($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->prepare($sel);
        $a->execute($par);
        $b = $a->errorInfo();
        if ($b)
            app::$eror.=$b[2];
        $this->i++;
        return $a->fetch();
    }

    function exe($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->prepare($sel);
        $a->execute($par);
        $b = $a->errorInfo();
        if ($b)
            app::$eror.=$b[2];
        $this->i++;
    }
    
     function last_id($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->lastInsertId();
        $this->i++;
        $b = $a->errorInfo();
        if ($b){
            app::$eror.=$b[2];
         return false;   
        }
        
        return $a;
    }
    
      function trans($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->beginTransaction();
        $this->i++;
        $b = $a->errorInfo();
        if ($b){
            app::$eror.=$b[2];
         return false;   
        }
        
        return $a;
    }
    
      function commit($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->commit();
        $this->i++;
        $b = $a->errorInfo();
        if ($b){
            app::$eror.=$b[2];
         return false;   
        }
        
        return $a;
    }
    
    function back($sel, $par) {
        if ($this->con == 0)
            $this->connect();
        $a = self::$db->rollBack();
        $this->i++;
        $b = $a->errorInfo();
        if ($b){
            app::$eror.=$b[2];
         return false;   
        }
        
        return $a;
    }
   

}