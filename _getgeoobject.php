<?
  # ==== подключились к БД ===============
  
  include("admin/_dbconnect.php");
  
  # ==== получаем информацию о конкретном геообъекте ===============
  
  $temp_rez=mysql_query("select * from tpp_geoobject where (id=".$_GET['rid'].")");
  $temp_rs=mysql_fetch_array($temp_rez);
      
  $temp_id=$temp_rs['id'];
  $temp_name=$temp_rs['name'];  
  $temp_coord=$temp_rs['coord'];
  $temp_kls_id=$temp_rs['kls_id'];
    
  $temp_rez=mysql_query("select * from tpp_kls where (id=$temp_kls_id)");
  $temp_rs=mysql_fetch_array($temp_rez);
  $temp_icon=$temp_rs['icon'];
  
  
  
    
                                   
//  нужно вернуть в формате";

// {
// 	"id": 7, 
// 	"name": "береза 1", 
// 	"tip": 0,
// 	"pattern": "<p>111</p>"
// }




    echo "  
    {
    \"id\": \"$temp_id\", \"name\": \"$temp_name\", \"coord\": \"$temp_coord\", \"icon\": \"$temp_icon\"
    }
    ";
	        
?>