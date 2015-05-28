
<?

# ----- действия со справочниками ----------------------


# порядковый номер типа события (двигаем вверх)
	if(@$_GET['make']=='pnteventup'){
		      $temp_rez=mysql_query("select * from tpp_type_event where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];                                
          if($temp_pn>1){
              $temp_pn=$temp_pn-1;
              mysql_query("update tpp_type_event set pn=pn+1 where (pn=$temp_pn)");
              mysql_query("update tpp_type_event set pn=pn-1 where (id=".$_GET['rid'].")");
          }
 	}


# порядковый номер типа события (двигаем вниз)
	if(@$_GET['make']=='pnteventdown'){

		      $temp_rez=mysql_query("select * from tpp_type_event where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];
                            
          $temp_rez=mysql_query("select max(pn) as maxpn from tpp_type_event");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_maxpn=$temp_rs['maxpn'];
                            
          if($temp_pn<$temp_maxpn){
              $temp_pn=$temp_pn+1;
              mysql_query("update tpp_type_event set pn=pn-1 where (pn=$temp_pn)");
              mysql_query("update tpp_type_event set pn=pn+1 where (id=".$_GET['rid'].")");          
          }
 	}

# удаляем тип события (если такиъ событий в реестре нет)

  if(@$_GET['make']=='deltevent'){
        $temp_rez=mysql_query("select count(*) as kolvo from tpp_event where (type_id=".$_GET['rid'].")");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_kolvo=$temp_rs['kolvo'];
        if($temp_kolvo==0){
            mysql_query("delete from tpp_type_event where (id=".$_GET['rid'].")");
        }        
	}


# добавляем тип события

  if(@$_GET['make']=='addtevent'){      
        
        $temp_rez=mysql_query("select max(pn) as maxpn from tpp_type_event");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_maxpn=$temp_rs['maxpn'];
        $temp_maxpn=$temp_maxpn+1;                                                                                        
        mysql_query("insert into tpp_type_event(name,page,pn) values('".$_GET['name']."','".$_GET['page']."',$temp_maxpn)");
                
	}	
  
# обновляем тип события 
  
  if(@$_GET['make']=='updatetevent'){
        $temp_sql="update tpp_type_event set 
              name='".$_GET['name']."',               
              page='".$_GET['page']."'                          
              where  (id=".$_GET['rid'].")";
        mysql_query($temp_sql);          
  }  
  
  
# ---------- интерфейс --------------------------------------------------------------------------

  $tempr=$_GET['r'];  
  
# ---------- форма правки --------------  
  
  if(@$_GET['make']=='edittevent'){
    # загрузили все поля из БД      
    $temp_rez=mysql_query("select * from tpp_type_event where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    
    echo "<div class='admbtn'>";
    echo "<form name='frm_edit' id='frm_edit' method='GET' action='adm.php'>";    
    echo "    <h2>Правка типа события</h2>";
    echo "    <b>Технические поля</b><br>";
    echo "    <input type='hidden' name='r' id='r' value='$tempr'><input type='hidden' name='make' id='make' value='updatetevent'>";
    echo "    Код <input type='text' name='rid' id='rid' class='noedit' value='".$temp_rs['id']."' readonly><br>";        
    echo "    Поряд.номер <input type='text' class='noedit' value='".$temp_rs['pn']."' readonly><br>";    
    echo " <hr>"; 
    echo "    <b>Поля коррекции</b><br>";
    echo "    Наименование <input type='text' name='name' id='name' value='".$temp_rs['name']."'><br><br>";     
    echo "    Страница описания:<br> <textarea style='width:100%' name='page' id='pagetevent' rows='6'>".$temp_rs['page']."</textarea><br>";
    
    echo "    <input type='submit' value='Внести изменения'><br>";   
    echo "</form>";
    echo "</div>";
  }
        
  
# ---------- форма добавления нового --------------
	# если 
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='edittevent'){ echo "style='display: none;' ";};
  
  echo ">   <h2 id='h2_add_tevent'>Добавить тип события</h2>";
  
  echo "<div class='form' id='d_add' style='display: none;'>";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='addtevent'>";
  echo "    Наименование: <input type='text' name='name' id='name' value=''><br><br>";
  echo "    Страница описания: <textarea style='width:100%' name='page' id='page' rows='8'></textarea><br>";
    
  echo "    <input type='submit' value='Создать элемент'><br>";
  echo "</form>";
  echo "</div>";
  
  echo "</div>";
  



# -----------------------------------------------------------------------------------------------
# -------- просмотр действующего классификатора -----------
# -----------------------------------------------------------------------------------------------



	echo "<h2>Работа со справочниками</h2>";
  

  echo "<h3>Типы событий</h2>";
  
  

  $tempsql="
        SELECT tpp_type_event.id, MAX( tpp_type_event.name) AS rname, 
        MAX( tpp_type_event.pn) AS rpn, COUNT( tpp_event.id ) AS kolvo
        FROM tpp_type_event
            LEFT JOIN tpp_event ON ( tpp_type_event.id = tpp_event.type_id ) 
            group by tpp_type_event.id order by rpn asc";
            
  $rez=mysql_query($tempsql);
    
	echo "<table width=600 cellspacing=0 cellpadding=8 class='tableadmin'>";
  echo "<tr>
            <th>Код</th>
            <th>Название типа</th>            
            <th>Событий</th>
            <th colspan=3>Действия</th>
        </tr>";
	while($rs=mysql_fetch_array($rez)){
		echo "<tr>";
		echo "<td>".$rs['id']."</td>";	
    echo "<td nowrap>".$rs['rname']."</td>";    
    echo "<td align='center'>".$rs['kolvo']."</td>";    
    echo "<td nowrap>
              <a href='?make=pnteventup&r=$tempr&rid=".$rs['id']."' class='astrelka'>&uarr;</a> &nbsp;
              <a href='?make=pnteventdown&r=$tempr&rid=".$rs['id']."' class='astrelka'>&darr;</a>
          </td>";
    
 		echo "<td><a href='?make=edittevent&r=$tempr&rid=".$rs['id']."'>править</a></td>";			
		echo "<td>";
          if($rs['kolvo']==0){
                echo "<a href='?make=deltevent&r=$tempr&rid=".$rs['id']."'>X</a></td>";
          }else{
                echo "удалить нельзя";
          };
    echo "</td>";            
   
		echo "</tr>";
	};
	echo "</table>";
  




?>