
<?

  include("mm_names.php");


# если пришла команда фильтрации / поиска
  $temp_dopusl1="(1=1)";
  $temp_dopusl2="(1=1)";
  $temp_dopusl3="(1=1)";
  $temp_dopusl4="(1=1)";
  $temp_dopusl5="(1=1)";
  
  if(@$_GET['make']=='filterevent'){
      # если пришел код события
        if($_GET['filter_code']!=""){  $temp_dopusl1="(tpp_event.id=".$_GET['filter_code'].")"; };
      # если пришел код типа события
        if($_GET['filter_type_event']!=0){  $temp_dopusl2="(tpp_event.type_id=".$_GET['filter_type_event'].")"; };        
     # если пришел месяц
        if($_GET['filter_mm']!=0){ $temp_dopusl3="((tpp_event.mm_begin=".$_GET['filter_mm'].")or(tpp_event.mm_end=".$_GET['filter_mm']."))";};        
     # если пришел фрагмент названия
        if($_GET['filter_name']){ $temp_dopusl4="((tpp_event.name like '%".$_GET['filter_name']."%')or(tpp_event.anons like '%".$_GET['filter_name']."%'))";};
     # если пришел код класса 
        if($_GET['filter_kls']){ $temp_dopusl5="(tpp_event.kls_id=".$_GET['filter_kls'].")";};
        
	}  


# включение и выключение видимости события
	if(@$_GET['make']=='pokazevent'){
		mysql_query("update tpp_event set vidimost=-vidimost where (id=".$_GET['rid'].")");
	}


# удаляем событие 

  if(@$_GET['make']=='delevent'){
         mysql_query("delete from tpp_event where (id=".$_GET['rid'].")");       
	}


# добавляем событие (если таковые еще нет)

  if(@$_GET['make']=='addevent'){
              
       # сначала проверяем сколько таких же событий
         $tmpsql="select count(*) as kolvo from tpp_event where (name='".$_GET['name']."') and (anons='".$_GET['anons']."')";
         $temp_rez=mysql_query($tmpsql);
         $temp_rs=mysql_fetch_array($temp_rez);
         $temp_kolvo=$temp_rs['kolvo'];
         
       # если ноль - то вставляем (если уже есть, то игнорируем пришедший дубликат)
         if($temp_kolvo==0){                    
            $temp_sql="insert into tpp_event(name,anons,page,type_id,dd_begin,mm_begin,yy_begin,dd_end,mm_end,yy_end,kls_id,vidimost)
                        values('".$_GET['name']."','".$_GET['anons']."','".$_GET['page']."',".$_GET['type_id'].",
                            ".$_GET['dd_begin'].", ".$_GET['mm_begin'].", ".$_GET['yy_begin'].",
                            ".$_GET['dd_end'].", ".$_GET['mm_end'].", ".$_GET['yy_end'].",".$_GET['kls_id'].",-1)";
                                                                                                
            mysql_query($temp_sql);
        }                
	}	
  
# обновляем событие 
  
  if(@$_POST['make']=='updateevent'){
        $temp_sql="update tpp_event set 
              name='".$_POST['name']."',
              anons='".$_POST['anons']."',               
              page='".$_POST['page']."',
              type_id=".$_POST['type_id'].",
              kls_id=".$_POST['kls_id'].",
              dd_begin=".$_POST['dd_begin'].",
              mm_begin=".$_POST['mm_begin'].",
              yy_begin=".$_POST['yy_begin'].",
              dd_end=".$_POST['dd_end'].",
              mm_end=".$_POST['mm_end'].",
              yy_end=".$_POST['yy_end']."                                                     
              where  (id=".$_POST['rid'].")";
        mysql_query($temp_sql);          
  }  
  
  
# ---------- интерфейс --------------------------------------------------------------------------

#  $tempr=$_GET['r'];  
  $tempr=getparam("r","1");
  
  
# ---------- форма правки --------------  
  
  if(@$_GET['make']=='editevent'){
    # загрузили все поля из БД      
    $temp_rez=mysql_query("select * from tpp_event where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    
    echo "<div class='admbtn'>";
    echo "<form name='frm_edit' id='frm_edit' method='POST' action='adm.php'>";    
    echo "    <h2>Правка события</h2>";
    echo "    <b>Технические поля</b><br>";
    echo "    <input type='hidden' name='r' id='r' value='$tempr'><input type='hidden' name='make' id='make' value='updateevent'>";
    echo "    Код <input type='text' name='rid' id='rid' class='noedit' value='".$temp_rs['id']."' readonly><br>";                
    echo " <hr>"; 
    echo "    <b>Поля коррекции</b><br>";
    echo "    Наименование <input type='text' name='name' id='name' value='".$temp_rs['name']."'><br><br>";     
    echo "    Анонс <input type='text' name='anons' id='anons' value='".$temp_rs['anons']."' style='width: 400px;'><br><br>";
    
    echo "    Тип события: <select name='type_id' id='type_id'>";                                              
        	     $rez1=mysql_query("select id,name,pn from tpp_type_event order by pn asc");	  
	             while($rs1=mysql_fetch_array($rez1)){
                    echo "<option value=".$rs1['id'];
                        if($rs1['id']==$temp_rs['type_id']){ echo " selected ";}
                    echo ">";                      
                    echo $rs1['name'];
                    echo "</option>";                                                      
                };
      
  echo "      </select> &nbsp;&nbsp; ";
  
  echo "    Связанный класс: <select name='kls_id' id='kls_id'>";                                              
            echo "<option value=0> --- без класса --- </option>";
            $rez1=mysql_query("select id,lv,name from tpp_kls where (lv=3) order by name asc");	  
  	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==$temp_rs['kls_id']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                
            };              
  echo "      </select><br><br>";
  
    
    echo "    Период с <input type='text' name='dd_begin' id='dd_begin' value='".$temp_rs['dd_begin']."' style='width: 30px;'> ";
    echo "          <select name='mm_begin' id='mm_begin'>";
                        for($temp_i=1;$temp_i<=12;$temp_i++){
                            echo "<option value=$temp_i>".$masmesr[$temp_i]."</option>";      
                        }
    echo "          </select><script language='JavaScript'> gebi('mm_begin').selectedIndex=".$temp_rs['mm_begin']."-1;</script>";    
    echo "          <input type='text' name='yy_begin' id='yy_begin' value='".$temp_rs['yy_begin']."' style='width: 50px;'> &nbsp;&nbsp;";
    
    echo "    по &nbsp; <input type='text' name='dd_end' id='dd_end' value='".$temp_rs['dd_end']."' style='width: 30px;'> ";
    echo "          <select name='mm_end' id='mm_end'>";
                        for($temp_i=1;$temp_i<=12;$temp_i++){
                            echo "<option value=$temp_i>".$masmesr[$temp_i]."</option>";      
                        }
    echo "          </select><script language='JavaScript'> gebi('mm_end').selectedIndex=".$temp_rs['mm_end']."-1;</script>";    
    
    echo "      <input type='text' name='yy_end' id='yy_end' value='".$temp_rs['yy_end']."' style='width: 50px;'> <br><br>";
    
    
        
    echo "    Страница описания:<br> <textarea style='width:100%' name='page' id='pageevent' rows='6'>".$temp_rs['page']."</textarea><br>";
    
    echo "    <input type='submit' value='Внести изменения'><br>";   
    echo "</form>";
    echo "</div>";
  }
        

# ---------- форма фильтрации --------------
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editevent'){ echo "style='display: none;' ";};
  echo ">   <h2 id='h2_filter_event'>Фильтр / поиск</h2>";
  
  echo "<div class='form' id='d_filter' ";   
  if(@$_GET['make']!='filterevent'){ echo "style='display: none;' ";};
    
  echo ">";
  echo "<form name='frm_filter' id='frm_filter' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='filterevent'>";
  echo "    Код события: <input type='text' name='filter_code' id='filter_code' value='".@$_GET['filter_code']."' style='width: 40px;'> &nbsp;&nbsp;&nbsp; ";
  
    
  echo "    Тип события: <select name='filter_type_event' id='filter_type_event'>";                    
          echo "<option value=0> --- любой --- </option>";            
        	$rez1=mysql_query("select id,name,pn from tpp_type_event order by pn asc");	  
	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==@$_GET['filter_type_event']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                                                      
          };
      
  echo "              </select> &nbsp;&nbsp;&nbsp; ";
  echo "   Месяц: <select name='filter_mm' id='filter_mm'>";                    
  echo "            <option value=0> --- любой --- </option>";
                    for($temp_i=1;$temp_i<=12;$temp_i++){
                      echo "<option value=$temp_i ";
                      if($temp_i==@$_GET['filter_mm']){
                          echo " selected ";                        
                      };
                      echo ">".$masmes[$temp_i]."</option>";      
                    }
  echo "            </select> <br><br>";
  
  echo "    Текст: <input type='text' name='filter_name' id='filter_name' value='".@$_GET['filter_name']."' style='width: 180px;'> &nbsp;&nbsp; ";
            
  echo "    Связанно с классом: <select name='filter_kls' id='filter_kls'>";
            echo "<option value=0> --- любой --- </option>";
            $rez1=mysql_query("select id,lv,name from tpp_kls where (lv=3) order by name asc");	  
  	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==@$_GET['filter_kls']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                
            };      
  echo "              </select> <br><br>";
      
        
  echo "    <input type='submit' value='Применить фильтр'><br>";
  echo "</form>";
  echo "</div>";
  echo "</div><br>";        
        
  
# ---------- форма добавления нового --------------
	# если 
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editevent'){ echo "style='display: none;' ";};
  
  echo ">   <h2 id='h2_add_event'>Добавить событие</h2>";
  
  echo "<div class='form' id='d_add' style='display: none;'>";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='addevent'>";

  echo "    Тип события: <select name='type_id' id='type_id'>";
                  $rez1=mysql_query("select id,name,pn from tpp_type_event order by pn asc");
                  while($rs1=mysql_fetch_array($rez1)){
                      echo "<option value=".$rs1['id'].">";
                      echo $rs1['name'];
                      echo "</option>";                                           
                  }; 
  echo "    </select><br><br>";      
                                                            
  echo "    Наименование: <input type='text' name='name' id='name' value=''> &nbsp;&nbsp;";
  echo "    Связанный класс: <select name='kls_id' id='fadd_kls_id'>";
            echo "<option value=0> --- без класса --- </option>";
            $rez1=mysql_query("select id,lv,name from tpp_kls where (lv=3) order by name asc");	  
  	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'].">";
                  echo $rs1['name'];
                  echo "</option>";                                 
            };      
  echo "              </select> <br><br>";
  
  
  echo "    Анонс: <input type='text' name='anons' id='anons' value='' style='width: 400px'><br><br>";
  
  echo "    В период с <input type='text' name='dd_begin' id='dd_begin' value='1' style='width: 30px'> ";
  echo "            <select name='mm_begin' id='mm_begin'>";
                    for($temp_i=1;$temp_i<=12;$temp_i++){
                      echo "<option value=$temp_i>".$masmesr[$temp_i]."</option>";      
                    }
  echo "            </select>";
  echo "            <input type='text' name='yy_begin' id='yy_begin' value='0' style='width: 50px'> ";

  echo "    по <input type='text' name='dd_end' id='dd_end' value='1' style='width: 30px'> ";
  echo "            <select name='mm_end' id='mm_end'>";
                    for($temp_i=1;$temp_i<=12;$temp_i++){
                      echo "<option value=$temp_i>".$masmesr[$temp_i]."</option>";      
                    }
  echo "            </select>";
  echo "            <input type='text' name='yy_end' id='yy_end' value='0' style='width: 50px'> (0-ежегодно)<br><br>";
  

  
  
  
  
  echo "    Страница описания: <textarea style='width:100%' name='page' id='page' rows='8'></textarea><br>";
    
  echo "    <input type='submit' value='Создать событие'><br>";
  echo "</form>";
  echo "</div>";
  
  echo "</div>";
  



# -----------------------------------------------------------------------------------------------
# -------- просмотр действующего календаря  -----------
# -----------------------------------------------------------------------------------------------



	echo "<h2>Календарь событий</h2>";
  
  $tempsql="
        SELECT tpp_event.id, tpp_event.name, tpp_event.anons, tpp_event.kls_id,
                tpp_event.dd_begin,tpp_event.mm_begin, tpp_event.yy_begin,
                tpp_event.dd_end,tpp_event.mm_end, tpp_event.yy_end,
                tpp_event.vidimost, tpp_event.type_id, tpp_type_event.name as tname                               
        FROM tpp_event, tpp_type_event 
        WHERE (tpp_event.type_id= tpp_type_event.id) and $temp_dopusl1 and $temp_dopusl2 and $temp_dopusl3 and $temp_dopusl4 and $temp_dopusl5 
        order by mm_begin, dd_begin asc";
        
         
            
  $rez=mysql_query($tempsql);
    
	echo "<table width=600 cellspacing=0 cellpadding=8 class='tableadmin'>";
  echo "<tr>
            <th>Код</th>
            <th>Событие</th>
            <th>Начало</th>            
            <th>Окончание</th>
            <th>Тип</th>
            <th>Класс</th>
            <th colspan=3>Действия</th>
        </tr>";
	while($rs=mysql_fetch_array($rez)){
		echo "<tr>";
		echo "<td>".$rs['id']."</td>";	
    echo "<td nowrap>".$rs['name']."</td>";
         
    echo "<td nowrap>".$rs['dd_begin']." ".$masmesr[$rs['mm_begin']]." ";
    if($rs['yy_begin']==0){
        echo "<br><span style='font-size:10pt; color: #999999;'>ежегодно</span>";    
    }else{
        echo $rs['yy_begin'];    
    }
    echo "</td>";

    echo "<td nowrap>".$rs['dd_end']." ".$masmesr[$rs['mm_end']]." ";
    if($rs['yy_end']==0){
        echo "<br><span style='font-size:10pt; color: #999999;'>ежегодно</span>";    
    }else{
        echo $rs['yy_end'];    
    }
    echo "</td>";
          
    echo "<td nowrap>".$rs['tname']."</td>";
    echo "<td align='center'>";
        if($rs['kls_id']!=0){
        
            echo "<a href='adm.php?make=editkls&r=1&rid=".$rs['kls_id']."' class='astrelka'>&rarr;</a>";
        }else{
            echo "&nbsp;";
        };
    echo "</td>";
    
    echo "<td><a href='?make=pokazevent&r=$tempr&rid=".$rs['id']."'>";
            if($rs['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыт";}
    echo "$tmppokaz</a></td>";
    
 		echo "<td><a href='?make=editevent&r=$tempr&rid=".$rs['id']."'>править</a></td>";			
		echo "<td><a href='?make=delevent&r=$tempr&rid=".$rs['id']."'>X</a></td>";
          
                   
		echo "</tr>";
	};
	echo "</table>";
  




?>