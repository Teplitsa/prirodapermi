<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  
# ==== получаем информацию о перечне событий (подходящих по дате) ===============
 
# запрос вида _getevents.php?calendar_d=27&calendar_m=3&calendar_y=2015 
  
# нужно вернуть формат 
#  
#{ "na_datu": "12.03.2015",
#  "kolvo": "2",
#  "contents": [
#    { "id": "12",
#      "name": "Прилет грачей",
#      "anons": "грачи прилетают с юга"
#    },
#    { "id": "19",
#      "name": "Цветение вишни",
#      "anons": "Распускаются бутоны вишни"
#    }
#  ]
# }


$masmesr[0]="Род.падеж";      
$masmesr[1]="января";      $masmesr[2]="февраля";     $masmesr[3]="марта";        
$masmesr[4]="апреля";      $masmesr[5]="мая";         $masmesr[6]="июня";        
$masmesr[7]="июля";        $masmesr[8]="августа";     $masmesr[9]="сентября";    
$masmesr[10]="октября";    $masmesr[11]="ноября";     $masmesr[12]="декабря";
                                 
                                 
  $dd_p=$_GET['calendar_d'];                                 
  $mm_p=$_GET['calendar_m'];
  $yy_p=$_GET['calendar_y'];
  $na_datu=$dd_p.".".$mm_p.".".$yy_p;
                                 
  $tempusl="    
    (
        ($dd_p+31*$mm_p >= dd_begin+31*mm_begin) and 
        ($dd_p+31*$mm_p <= dd_end+31*mm_end) and 
        (mm_begin<=mm_end) and ((yy_begin=0) or ($yy_p=yy_begin))
    ) 
    or
    (
      (not (
            ($dd_p+31*$mm_p < dd_begin+31*mm_begin) and 
            ($dd_p+31*$mm_p > dd_end+31*mm_end)
          )) 
      and (mm_begin>mm_end) and ((yy_begin=0) or ($yy_p=yy_begin))
    )"; 
 
  
  $tempsql="select count(*) as kolvo from tpp_event WHERE ($tempusl) and (vidimost=1)";
  
# echo "<hr>".$tempsql."<hr>";  
  
  $temp_rez=mysql_query($tempsql);
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_kolvo=$temp_rs['kolvo'];

  $rezstr="{      \"na_datu\": \"$na_datu\",
                  \"kolvo\": \"$temp_kolvo\",
                  \"contents\": [";                  
  
  $tempsql="select * from tpp_event WHERE ($tempusl) and (vidimost=1) order by mm_begin asc, dd_begin asc";  
         
  $temp_rez=mysql_query($tempsql);

  $tempi=0;       

  while($temp_rs=mysql_fetch_array($temp_rez)){
      $tempi=$tempi+1;

      $temp_id=$temp_rs['id'];
      $temp_name=$temp_rs['name'];
      $temp_anons=$temp_rs['anons'];


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
     
                             
      $rezstr=$rezstr."
                {   \"id\": \"$temp_id\",
                    \"name\": \"$temp_name\",
                    \"anons\": \"$temp_anons\",
                    \"period\": \"$temp_period\"                    
                }";

      # если номер текущего элемента еще не достиг общего количества, то ставим запятую
      if ($tempi<$temp_kolvo){
          $rezstr=$rezstr.",";
      }
  }   
  
  $rezstr=$rezstr."  ] } ";
            
  echo $rezstr;      	
?>