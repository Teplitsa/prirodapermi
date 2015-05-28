<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  
# ==== получаем информацию о множестве объектов класса (приходит код класса) ===============
  
# нужно вернуть формат
#  
#  
#{ "kls_id": "12",
#  "kls_name": "Береза",
#  "icon_file": "bereza.png",
#  "icon_width": "30",
#  "icon_height": "60",
#  "icon_pos_left": "15",
#  "icon_pos_top": "59",
#  "kolvo": "2",
#  "contents": [
#    { "id": "34",
#      "name": "Малая береза",
#      "coord": "57.99132500044739, 56.17719464471429"
#    },
#    { "id": "36",
#      "name": "Большая береза",
#      "coord": "57.99132500044739, 56.17719464471429"
#    }
#  ]
# }
                                 
  
  
  
  
  $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].") and (vidimost=1)");  
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_kls_id=$temp_rs['id'];
  $temp_kls_name=$temp_rs['name'];
  $temp_kls_icon=$temp_rs['icon'];
  $temp_icon_width=$temp_rs['icon_width'];
  $temp_icon_height=$temp_rs['icon_height'];
  $temp_pos_left=$temp_rs['pos_left'];
  $temp_pos_top=$temp_rs['pos_top'];
  
  
  $temp_rez=mysql_query("select count(*) as kolvo from tpp_geoobject where (kls_id=$temp_kls_id) and (vidimost=1)");  
  $temp_rs=mysql_fetch_array($temp_rez);
  $temp_kolvo=$temp_rs['kolvo'];
  
  $rezstr="{      \"kls_id\": \"$temp_kls_id\",
                  \"kls_name\": \"$temp_kls_name\",                  
                  \"icon_file\": \"$temp_kls_icon\",
                  \"icon_width\": \"$temp_icon_width\",
                  \"icon_height\": \"$temp_icon_height\",
                  \"icon_pos_left\": \"$temp_pos_left\",
                  \"icon_pos_top\": \"$temp_pos_top\",                                                                        
                  \"kolvo\": \"$temp_kolvo\",
                  \"contents\": [";      
  $tempi=0;     
  $temp_rez=mysql_query("select * from tpp_geoobject where (kls_id=$temp_kls_id) and (vidimost=1)");
  
  while($temp_rs=mysql_fetch_array($temp_rez)){
      $tempi=$tempi+1;
      
      $temp_id=$temp_rs['id'];
      $temp_name=$temp_rs['name'];
      $temp_coord=$temp_rs['coord'];
      $temp_kolvo_elem=$temp_rs['kolvo'];
      $temp_need_def=$temp_rs['need_def'];
      $temp_need_mon=$temp_rs['need_mon'];
      
      $temp_kart=$temp_rs['kart'];
           # заменяе кавычку спецсимволы  \n  \r   на ничто (удаляем их)
          $temp_kart=preg_replace('/\n|\r/', '', $temp_kart); 
  
          # заменяе кавычку "   на   \"  
          $temp_kart=preg_replace('/"/', '\"', $temp_kart);      
      
      $rezstr=$rezstr."
                {   \"id\": \"$temp_id\",
                    \"name\": \"$temp_name\",
                    \"coord\": \"$temp_coord\",
                    \"kolvo\": \"$temp_kolvo_elem\",
                    \"need_def\": \"$temp_need_def\",                    
                    \"need_mon\": \"$temp_need_mon\",
                    \"kart\": \"$temp_kart\"
                }";
      # если номер текущего элемента еще не достиг общего количества, то ставим запятую
      if ($tempi<$temp_kolvo){
          $rezstr=$rezstr.",";
      }
  }   
  
  $rezstr=$rezstr."  ] } ";
      
      
    echo $rezstr;  
    
	
?>