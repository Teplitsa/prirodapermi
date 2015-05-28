<?

#====== заполните параметры подключения ===============

$srv="localhost";
$db ="db_pp";
$log="root";
$pwd="";


$cn=@mysql_connect($srv,$log,$pwd);
if(!$cn){
	die("Нет соединения с СУБД !");
}else{
	if(!(@mysql_select_db($db,$cn))){
		die("Нет подключения к базе данных !");
	}else{
		@mysql_query("SET NAMES 'utf8'");
	};
};

?>