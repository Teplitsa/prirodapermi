<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  include("admin/mm_names.php");
  
  
# ==== получаем информацию о конкретном классе (по rid) ===============
 
# запрос вида http://test1.ru/_getklsinfo.php?rid=8 
  
# нужно вернуть формат 
#  
#{ "kls_id": "8",
#  "name": "русское название",
#  "lname": "лат.название",
#  "tip": "0",
#  "page": "большой текст",
#  "metok_na_karte": "2",
#  "kolvo_na_karte": "7"
# }

#  { "kls_id": "8", "name": "Осина", "lname": "Populus tremula", "tip": "0", "page": "", "metok_na_karte": "4", "kolvo_na_karte": "124" }


  $rid=$_GET['rid'];                                 
   
  $tempsql="select * from tpp_kls where (id=$rid) and (vidimost=1)"; 
  

# echo "<hr>".$tempsql."<hr>";  

  
  $temp_rez=mysql_query($tempsql);
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_id=$temp_rs['id'];
  $temp_name=$temp_rs['name'];
  $temp_lname=$temp_rs['lname'];
  $temp_tip=$temp_rs['tip'];
  
  
  $temp_page=$temp_rs['page'];
 
  # заменяе кавычку спецсимволы  \n  \r   на ничто (удаляем их)
  $temp_page=preg_replace('/\n|\r/', '', $temp_page); 
  
  # заменяе кавычку "   на   \"  
  $temp_page=preg_replace('/"/', '\"', $temp_page);
    
    
  $rezstr="{      \"kls_id\": \"$temp_id\",
                  \"name\": \"$temp_name\",
                  \"lname\": \"$temp_lname\",                  
                  \"tip\": \"$temp_tip\",                                         
                  \"page\": \"$temp_page\",
                  ";
                                     
  # в случае если есть такие объекты на учете считаем их статистику
  
  $tempsql="select count(*) as cnt, sum(kolvo) as vsego from tpp_geoobject where (kls_id=$rid) and (vidimost=1)";
  $temp_rez=mysql_query($tempsql);
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_kolvo=$temp_rs['cnt'];    
  $temp_vsego=$temp_rs['vsego'];

  $rezstr=$rezstr." \"metok_na_karte\": \"$temp_kolvo\",
                    \"kolvo_na_karte\": \"$temp_vsego\"    
   } ";
  
        
  echo $rezstr;
          	
?>