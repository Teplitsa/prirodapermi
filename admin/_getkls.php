<?
  # ==== подключились к БД ===============
  
  include("_dbconnect.php");
  
  # ==== получаем информацию о классе ===============
  
  $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].")");
  $temp_rs=mysql_fetch_array($temp_rez);
  
  $temp_id=$temp_rs['id'];
  $temp_name=$temp_rs['name'];  
  $temp_tip=$temp_rs['tip'];
  $temp_icon=$temp_rs['icon'];
  $temp_pattern=$temp_rs['pattern'];  
                                   
//  нужно вернуть в формате";

// {
// 	"id": 7, 
// 	"name": "береза 1", 
// 	"tip": 0,
// 	"pattern": "<p>111</p>"
// }


if(@$_GET['pattern']=='only'){
    # возвращаем шаблон в формате HTML
    echo $temp_pattern; 		
	}
else{
    # полная конструкция
    echo "  
    {
    \"id\": $temp_id, \"name\": \"$temp_name\", \"tip\": $temp_tip, \"icon\": \"$temp_icon\"
    }
    ";
}  

	
?>