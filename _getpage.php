<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  
# ==== выдаем информацию о странице сайта (+вложенные подстраницы) ===============
  
# нужно вернуть формат
#    
#{ "id": "12",
#  "name": "Контакты",
#  "full_name": "Контакты организаторов",
#  "lv": "1",
#  "page": "Текст страницы",
#  "page_add": "Дополнительный текст",
#  "contents": [
#    { "id": "17",
#      "name": "Реквизиты",
#      "pn": "7"
#    },
#    { "id": "18",
#      "name": "Пожертвования",
#      "pn": "8"
#    }
#  ]
# }
          
# пример вызова http://test1.ru/_getpage.php?rid=1          
                                 
  
  $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].") and (vidimost=1)");  
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_page_id=$temp_rs['id'];
  $temp_page_name=$temp_rs['name'];
  $temp_page_full_name=$temp_rs['full_name'];
  $temp_page_lv=$temp_rs['lv'];
  $temp_page_pn=$temp_rs['pn'];

  $temp_page=$temp_rs['page'];
  $temp_page_add=$temp_rs['page_add'];
  

     
  # заменяе кавычку спецсимволы  \n  \r   на ничто (удаляем их)
  $temp_page=preg_replace('/\n|\r/', '', $temp_page); 
  $temp_page_add=preg_replace('/\n|\r/', '', $temp_page_add);
  
  # заменяе кавычку "   на   \"  
  $temp_page=preg_replace('/"/', '\"', $temp_page);
  $temp_page_add=preg_replace('/"/', '\"', $temp_page_add);
        
  
  $rezstr="{      \"id\": \"$temp_page_id\",
                  \"name\": \"$temp_page_name\",                  
                  \"full_name\": \"$temp_page_full_name\",
                  \"lv\": \"$temp_page_lv\",                                                                                                            
                  \"page\": \"$temp_page\",
                  \"page_add\": \"$temp_page_add\",
                  \"contents\": [";      
                  
  #==== организуем выборку всех ниже нащего пункта (pn больше текущего) 
 
  $tempsql="select * from tpp_page WHERE (pn>$temp_page_pn) and (vidimost=1) order by pn asc";           
  $temp_rez=mysql_query($tempsql);        
  
  $ostanov=0; 
  $razd="";

  while($temp_rs=mysql_fetch_array($temp_rez)){
  
      # если уровень больше нашего и обработка не остановлена
      if(($temp_rs['lv']>$temp_page_lv)and($ostanov==0)){
      
            $temp_id=$temp_rs['id'];
            $temp_name=$temp_rs['name'];
            $temp_pn=$temp_rs['pn'];
            
            $rezstr=$rezstr."$razd
                {   \"id\": \"$temp_id\",
                    \"name\": \"$temp_name\",
                    \"pn\": \"$temp_pn\"
                }";
            $razd=",";
      
      }else{
          $ostanov=1;
      }
      

  }
      
#====                        
                  
     
  
  $rezstr=$rezstr."  ] } ";
      
      
    echo $rezstr;  
    
	
?>