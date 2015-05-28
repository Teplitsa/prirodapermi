<?
  # ==== проверяем доступ администратора ===============
	include("admfunction.php");
	session_start();
	if(!isset($_SESSION['admdostup'])){
		$_SESSION['admdostup']="";
	};
	if(@$_GET['exit']){
		$_SESSION['admdostup']="";
	}
	$tadmdostup=$_SESSION['admdostup'];

	if((@$_POST['login'])and(@$_POST['psw'])){
		f_proverka($_POST['login'],$_POST['psw']);
	}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="ru" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />

    <title>Панель управления</title>

    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script language='JavaScript' src='../js/adm.js'></script>
  
    <script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="../js/tiny_mce/init.js"></script>
    
    <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript" src="../js/admmap.js"></script>
  
    <link rel="stylesheet" type="text/css" href="../css/adm.css" />

</head>


<body>
<? 

if($tadmdostup==1){
	echo "Административный интерфейс проекта \"Природа перми\" &nbsp;&nbsp;&nbsp;&nbsp;"; 
	echo "<a href='adm.php?exit=yes'>Выход</a><br><br>";
	f_admmenu();
	
  if($tr==1){ include("admkls.php"); };
  if($tr==2){ include("admgeoobj.php"); };
  if($tr==3){ include("admevent.php"); };
  if($tr==4){ include("admpages.php"); };  
  if($tr==5){ include("admsprav.php"); };
 
}else{                          
  include("admlogin.php");
}

?>

</body>
</html>