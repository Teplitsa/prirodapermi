<?

include("_dbconnect.php");
  
$tr=getparam("r","1");
$tpm=getparam("r","1");

if(@$_GET['pm']){$tpm=$_GET['pm'];}else{$tpm=0;};

$masmenu = array ("","Классификатор","Геообъекты","События","Страницы","Справочники");


function getparam($pn,$z){
	if(@$_GET[$pn]){ return @$_GET[$pn];}
	if(@$_POST[$pn]){ return @$_POST[$pn];}
	if(!(@$_GET[$pn])&&!(@$_POST[$pn])){ return $z; };


}


function f_admmenu(){
	global $tr;
	global $masmenu;

	echo "<table border=0 cellpadding=10 cellspacing=0><tr>";
	for($i=1;$i<count($masmenu);$i++){
		echo "<td ";
		if($tr==$i){ echo " class='amenu' ";}else{echo " class='nmenu' ";};
		echo ">";
		echo "<a href='?r=$i'>".$masmenu[$i]."</a>";
	  echo "</td>";
	}
	echo "</tr></table><br>";

};

function f_sqlrezult($psql){
	$rez=mysql_query($psql);
	while($rs=mysql_fetch_array($rez)){
		$tempvar=$rs["sqlresult"];
	};
	return $tempvar;

};


function f_proverka($l,$p){
global $tadmdostup;
	if(f_sqlrezult("select count(*) as sqlresult from tpp_users where (login='$l')and(psw='$p')")>0){
		$tadmdostup=1;
	}else{
		$tadmdostup=0;
	};
	$_SESSION['admdostup']=$tadmdostup;
};

function f_if($p1,$p2,$r1,$r2){
	if($p1==$p2){return $r1;}
	else{ return $r2;}
}


# функция генерации безопасного наименования файлов
function f_rename_filename($fn){
	$tempfn=strtolower($fn);
	$strrus="|а|б|в|г|д|е|ё|ж|з|и|й|к|л|м|н|о|п|р|с|т|у|ф|х|ц|ч|ш|щ|ъ|ы|ь|э|ю|я| ";
	$streng="|a|b|v|g|d|e|e|j|z|i|iy|k|l|m|n|o|p|r|s|t|u|f|h|c|ch|sh|sh||i||ie|yu|ya|_";
	$masrus=explode("|",$strrus);
	$maseng=explode("|",$streng);
	for ($i=0;$i<strlen($tempfn);$i++){
		$tempch=$tempfn[$i];
		# если текущий символ есть в массиве проблемных символов
		if(in_array($tempch,$masrus)){
			for($j=1;$j<count($masrus);$j++){
				if($masrus[$j]==$tempch){
					$tempch=$maseng[$j];
				}
			}
		}
	 	$temprez=$temprez.$tempch;
	}
	return $temprez;
}





?>