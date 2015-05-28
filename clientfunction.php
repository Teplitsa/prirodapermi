<?

include("admin/_dbconnect.php");
  
$tr=getparam("r","1");
$tpm=getparam("r","1");

if(@$_GET['pm']){$tpm=$_GET['pm'];}else{$tpm=0;};



function getparam($pn,$z){
	if(@$_GET[$pn]){ return @$_GET[$pn];}
	if(@$_POST[$pn]){ return @$_POST[$pn];}
	if(!(@$_GET[$pn])&&!(@$_POST[$pn])){ return $z; };


}

function f_upmenu(){
    # строим верхнее меню
    $rez1=mysql_query("select id,lv,pn,name,vidimost from tpp_page where (lv=1) and (vidimost=1) order by pn asc");  
    while($rs1=mysql_fetch_array($rez1)){
        echo "<div class='d_prm' page='".$rs1['id']."' >".$rs1['name']."</div>";   
    }                

};




function f_rmenu(){
    # строим графические вкладки классификатора
    $rez1=mysql_query("select id,lv,pn,name,lname, icon, vidimost from tpp_kls where (lv=1) and (vidimost=1) order by pn asc");  
    while($rs1=mysql_fetch_array($rez1)){
        echo "<div class='d_vkladka' id='d_vkl_".$rs1['id']."' nazvanie='".$rs1['name']."' vkl='".$rs1['id']."'>";
        echo "      <img src='img/markers/".$rs1['icon']."'>";              
        echo "</div>";
  }                
  echo "<div style='clear: both;'></div>";
  echo "<div id='d_vkladka_podpis'></div>";
  
  echo "<div id='d_vkladka_cont'>";
  $rez1=mysql_query("select id,lv,pn,name,lname, icon, vidimost from tpp_kls where (lv=1) and (vidimost=1) order by pn asc");  
  while($rs1=mysql_fetch_array($rez1)){
        echo "  <div class='d_vkladka_cnt' id='d_cnt_".$rs1['id']."'>";
        $rez2=mysql_query("select id,lv,pn,name,lname, icon, vidimost from tpp_kls where (lv=2) and (parent_id=".$rs1['id'].") and (vidimost=1) order by pn asc");
        while($rs2=mysql_fetch_array($rez2)){
              echo "  <div class='d_gr' id='gr_".$rs2['id']."' >".$rs2['name'];
              echo "      <div class='linkpokaz' kod='".$rs2['id']."' title='Показать все объекты'></div>"; 
              echo "  </div>";
              echo "     <div id='d_pod_gr_".$rs2['id']."' style='display: none;'>";
                          $rez3=mysql_query("select id,lv,pn,name,lname, icon, vidimost from tpp_kls where (lv=3) and (parent_id=".$rs2['id'].") and (vidimost=1) order by pn asc");
                          while($rs3=mysql_fetch_array($rez3)){
                              echo " <div class='dend' kod='".$rs3['id']."'>".$rs3['name'];
                              echo "    <div class='linkinfo' kod='".$rs3['id']."' title='Смотреть описание \"".$rs3['name']."\"'></div></div>";                                                  
                          }                    
              
              echo "  </div>";                                          
              
              
              
        }
                      
        echo "  </div>";
  }                
  echo "</div>";
  
  
}







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





?>