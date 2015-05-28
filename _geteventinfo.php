<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  include("admin/mm_names.php");
  
  
# ==== получаем информацию о конкретном событии (по rid) ===============
 
# запрос вида _geteventinfo.php?rid=27 
  
  
  
# нужно вернуть формат 
#  
#{ "id": "3",
#  "name": "Прилет грачей",
#  "anons": "Грачи скоро совсем прилетают",
#  "type_id": "1",
#  "type_name": "События природы",
#  "kls_id": "12"  или "0",
#  "kls_name": "Грач",
#  "page": "большой текст",
#  "period": "10.12.2015"
# }

# { "id": "3", "name": "Прилет грачей", "anons": "Птицы прилетают", "type_id": "1", "type_name": "События природы", "period": "5 - 25 марта" }


  $rid=$_GET['rid'];                                 
  
                                 
  $tempusl=" (tpp_event.id= $rid) and (tpp_type_event.id=tpp_event.type_id) "; 
   
  $tempsql="select tpp_event.id, tpp_event.name, tpp_event.anons, tpp_event.page, tpp_event.type_id, tpp_type_event.name as type_name,tpp_event.kls_id,
                tpp_event.dd_begin, tpp_event.mm_begin, tpp_event.yy_begin, tpp_event.dd_end, tpp_event.mm_end, tpp_event.yy_end
               from tpp_event, tpp_type_event WHERE ($tempusl) and (tpp_event.vidimost=1)";
 
 
# echo "<hr>".$tempsql."<hr>";  

  
  $temp_rez=mysql_query($tempsql);
  $temp_rs=mysql_fetch_array($temp_rez);
  

  $temp_id=$temp_rs['id'];
  $temp_name=$temp_rs['name'];
  $temp_anons=$temp_rs['anons'];
  $temp_type_id=$temp_rs['type_id'];
  $temp_type_name=$temp_rs['type_name'];  
  $temp_page=$temp_rs['page'];
 
     
  # заменяе кавычку спецсимволы  \n  \r   на ничто (удаляем их)
  $temp_page=preg_replace('/\n|\r/', '', $temp_page); 
  
  # заменяе кавычку "   на   \"  
  $temp_page=preg_replace('/"/', '\"', $temp_page);
    

  $rezstr="{      \"id\": \"$temp_id\",
                  \"name\": \"$temp_name\",
                  \"anons\": \"$temp_anons\",
                  \"type_id\": \"$temp_type_id\",    
                  \"type_name\": \"$temp_type_name\",                   
                  \"page\": \"$temp_page\",
                  ";
                                    
 
 
     # анализируем формат выдачи спецификации периода:  
     # 27 августа 2015,    4 - 12 августа,   2 марта - 3 апреля,  27 декабря - 4 января 2016
     
     # вариант 1 - если даты полностью совпали:  "27 августа"  или "27 августа 2015"
     if(($temp_rs['dd_begin']==$temp_rs['dd_end'])&&($temp_rs['mm_begin']==$temp_rs['mm_end'])&&($temp_rs['yy_begin']==$temp_rs['yy_end'])){
        $temp_period=$temp_rs['dd_begin']." ".$masmesr[$temp_rs['mm_begin']];
        if($temp_rs['yy_begin']>0){
           $temp_period=$temp_period." ".$temp_rs['yy_begin'];
        };
     }else{
     # вариант 2 - если даты не совпали:  "1 - 7 августа"  или "10 июля - 3 августа" или "10 июля 2015 - 3 августа 2015"
     
          $temp_period=$temp_rs['dd_begin'];
          if($temp_rs['mm_begin']!=$temp_rs['mm_end']){
                $temp_period=$temp_period." ".$masmesr[$temp_rs['mm_begin']];
                if($temp_rs['yy_begin']>0){
                      $temp_period=$temp_period." ".$temp_rs['yy_begin'];
                }                          
          }
          $temp_period=$temp_period." - ".$temp_rs['dd_end']." ".$masmesr[$temp_rs['mm_end']];
          if($temp_rs['yy_end']>0){
                      $temp_period=$temp_period." ".$temp_rs['yy_end'];
          }
     }       

  $rezstr=$rezstr." \"period\": \"$temp_period\",  ";
  
  # в случае если есть связанный объект (неноль) то подзапросом выбираем этот объект
  $temp_kls_id=$temp_rs['kls_id'];
  
  if($temp_kls_id>0){
      $temp_rez=mysql_query("SELECT * FROM tpp_kls WHERE (id=$temp_kls_id)");
      $temp_rs=mysql_fetch_array($temp_rez);
      $temp_kls_name=$temp_rs['name'];
  }else{
      $temp_kls_name="";  
  }
  
  $rezstr=$rezstr." \"kls_id\": \"$temp_kls_id\",
                    \"kls_name\": \"$temp_kls_name\"    
   } ";
  

  
            
  echo $rezstr;
  
        	
?>