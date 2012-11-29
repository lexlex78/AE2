<?php
class test_index extends modul {

   function run (){
       app::$content->center='testt';
       print_r($this);
   }

}