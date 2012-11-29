<?php
class index_index extends modul {
    
   function cache() {
   return array(
    'key'=>$_GET[id],   
    'content'=>'center',
    'time'=>10,
 //   'tip'=>2,   
   );    
   } 

   function run (){
       
   
//  if ((app::$content->center=$this->cash_get('index'))==false){    
       
   $rend='test_kesha';
   
//   print_r(app::$db->sel_one('SELECT * FROM test1 where id=:a;',array('a'=>1)));
//   
//   print_r( app::$db->lastid());

   $this->x=$rend;
   $this->render('test_view','center');
   
//   $this->cache_set('index', 20, app::$content->center);
//   }
   
   
         
   
  
   }

}