<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  include("admin/mm_names.php");
  
  
# ==== получаем информацию о конкретном экземпляре геообъекта (по rid) ===============
 
# запрос вида view-source:http://test1.ru/_getgeoobjectinfo.php?rid=18
  
# нужно вернуть формат 
#  
#{
#  "id": "8",
#  "kls_id": "12",
#  "kls_name": "Береза",
#  "name": "Береза у реки",
#  "kolvo": "1",
#  "ud": "0",
#  "need_def": "-1",
#  "need_mon": "1",
#  "kart": "карточка геообъекта",
#  "page": "большой текст"
# }

  $rid=$_GET['rid'];                                 
   
  $tempsql="select tpp_geoobject.id, tpp_geoobject.kls_id, tpp_kls.name as kls_name, tpp_geoobject.name, tpp_geoobject.kolvo, tpp_geoobject.ud,
                  tpp_geoobject.need_def, tpp_geoobject.need_mon, tpp_geoobject.kart, tpp_geoobject.page
            from tpp_geoobject, tpp_kls    
            where (tpp_geoobject.id=$rid) and (tpp_geoobject.kls_id=tpp_kls.id) and (tpp_geoobject.vidimost=1)"; 

# echo "<hr>".$tempsql."<hr>";  
  
  $temp_rez=mysql_query($tempsql);
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_id=$temp_rs['id'];
  $temp_kls_id=$temp_rs['kls_id'];
  $temp_kls_name=$temp_rs['kls_name'];
  $temp_name=$temp_rs['name'];
  $temp_kolvo=$temp_rs['kolvo'];
  $temp_ud=$temp_rs['ud'];
  $temp_need_def=$temp_rs['need_def'];
  $temp_need_mon=$temp_rs['need_mon'];
  
  $temp_kart=$temp_rs['kart'];  
  $temp_page=$temp_rs['page'];
 
  # заменяе кавычку спецсимволы  \n  \r   на ничто (удаляем их)
  $temp_kart=preg_replace('/\n|\r/', '', $temp_kart);
  $temp_page=preg_replace('/\n|\r/', '', $temp_page); 
  
  
  # заменяе кавычку "   на   \"  
  $temp_kart=preg_replace('/"/', '\"', $temp_kart);
  $temp_page=preg_replace('/"/', '\"', $temp_page);
    
    
  $rezstr="{      \"id\": \"$temp_id\",     
                  \"kls_id\": \"$temp_kls_id\",
                  \"kls_name\": \"$temp_kls_name\",
                  \"name\": \"$temp_name\",
                  \"kolvo\": \"$temp_kolvo\",
                  \"ud\": \"$temp_ud\",
                  \"need_def\": \"$temp_need_def\",
                  \"need_mon\": \"$temp_need_mon\",                  
                  \"kart\": \"$temp_kart\",
                  \"page\": \"$temp_page\"   
   } ";
  
        
  echo $rezstr;
          	
?>