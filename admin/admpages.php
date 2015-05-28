
<?




# ----- действия с реестром страниц ----------------------


# включение и выключение видимости
	if(@$_GET['make']=='pokazpage'){
		mysql_query("update tpp_page set vidimost=-vidimost where (id=".$_GET['rid'].")");
	}
  
# переключение уровня вложенности страницы(1 или 2)
	if(@$_GET['make']=='chlevelpage'){
  
    $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    $temp_pn=$temp_rs['lv'];    
    if($temp_pn==1){ $temp_lv=2; } else {$temp_lv=1;}        
		mysql_query("update tpp_page set lv=$temp_lv where (id=".$_GET['rid'].")");
	}


  
  

# порядковый номер (двигаем вверх)
	if(@$_GET['make']=='pnpageup'){
    
		      $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];
                                 
          if($temp_pn>1){
              $temp_pn=$temp_pn-1;
              mysql_query("update tpp_page set pn=pn+1 where (pn=$temp_pn)");
              mysql_query("update tpp_page set pn=pn-1 where (id=".$_GET['rid'].")");
          }
 	}


# порядковый номер (двигаем вниз)
	if(@$_GET['make']=='pnpagedown'){

		      $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];
  
          $temp_rez=mysql_query("select max(pn) as maxpn from tpp_page");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_maxpn=$temp_rs['maxpn'];
                            
          if($temp_pn<$temp_maxpn){
              $temp_pn=$temp_pn+1;
              mysql_query("update tpp_page set pn=pn-1 where (pn=$temp_pn)");
              mysql_query("update tpp_page set pn=pn+1 where (id=".$_GET['rid'].")");          
          }
 	}

# удаляем страницу

  if(@$_GET['make']=='delpage'){
        $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].")");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_pn=$temp_rs['pn'];                                        
        mysql_query("update tpp_page set pn=pn-1 where (pn>$temp_pn)");    
        mysql_query("delete from tpp_page where (id=".$_GET['rid'].")");
	}


# добавляем страницу

  if(@$_GET['make']=='addpage'){      
                
        $temp_rez=mysql_query("select count(*) as kolvo from tpp_page");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_pn=$temp_rs['kolvo'];        
        $temp_pn=$temp_pn+1;                                                    
        mysql_query("insert into tpp_page(lv,pn, name,full_name, page, page_add, vidimost) values(2, $temp_pn,'".$_GET['name']."','".$_GET['full_name']."','','',-1)");
                
	}	
  
# обновляем страницу после коррекции
  
  if(@$_POST['make']=='updatepage'){
        $temp_sql="update tpp_page set 
              name='".$_POST['name']."', 
              full_name='".$_POST['full_name']."',              
              page='".$_POST['page']."',
              page_add='".$_POST['page_add']."'              
              where  (id=".$_POST['rid'].")";
        mysql_query($temp_sql);          
 
 
 
 
  }  
  
  
# ---------- интерфейс --------------------------------------------------------------------------

  $tempr=getparam("r","1");
  
# ---------- форма правки --------------  
  
  if(@$_GET['make']=='editpage'){
    # загрузили все поля из БД      
    $temp_rez=mysql_query("select * from tpp_page where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    
    echo "<div class='admbtn'>";
    echo "<form name='frm_edit' id='frm_edit' method='POST' action='adm.php' enctype='multipart/form-data'>";    
    echo "    <h2>Правка страницы сайта</h2>";
    echo "    <b>Технические поля</b><br>";
    echo "    <input type='hidden' name='r' id='r' value='$tempr'><input type='hidden' name='make' id='make' value='updatepage'>";
    echo "    Код <input type='text' name='rid' id='rid' class='noedit' value='".$temp_rs['id']."' readonly><br>";    
    echo "    Уровень <input type='text' class='noedit' value='".$temp_rs['lv']."' readonly><br>";
    echo "    Поряд.номер <input type='text' class='noedit' value='".$temp_rs['pn']."' readonly><br>";
    echo "    Видимость <input type='text' name='name' class='noedit' value='".$temp_rs['vidimost']."' readonly><br>";
    echo " <hr>"; 
    echo "    <b>Поля коррекции</b><br>";
    echo "    Наименование: <input type='text' name='name' id='name' value='".$temp_rs['name']."' style='width: 200px;'><br>";
    echo "    Заголовок: <input type='text' name='full_name' id='full_name' value='".$temp_rs['full_name']."' style='width: 300px;'><br>";  
   
    echo "    Содержимое: <textarea style='width:100%' name='page' id='page' rows='10'>".$temp_rs['page']."</textarea><br>";
    
    echo "    Доп.контент: <textarea style='width:100%' name='page_add' id='page_add' rows='10'>".$temp_rs['page_add']."</textarea><br>";      
    echo " <hr>";        
    echo "    <input type='submit' value='Внести изменения'><br>";   
    echo "</form>";
    echo "</div>";
  }
        
  
# ---------- форма добавления нового --------------
	# если 
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editpage'){ echo "style='display: none;' ";};
  
  echo ">   <h2 id='h2_add_kls'>Добавить страницу</h2>";
  
  echo "<div class='form' id='d_add' style='display: none;'>";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='addpage'>";
  echo "    Название: <input type='text' name='name' id='name' value=''> &nbsp;&nbsp;&nbsp;";
  echo "    Заголовок: <input type='text' name='full_name' id='full_name' value=''><br><br>";
  
  echo "    <input type='submit' value='Добавить'><br>";
  echo "</form>";
  echo "</div>";
  
  echo "</div>";
  



# -----------------------------------------------------------------------------------------------
# -------- просмотр реестра страниц -------------------------------------------------------------
# -----------------------------------------------------------------------------------------------



	echo "<h2>Страницы сайта</h2>";
  
    
  echo "<table  border=0 cellpadding=6 cellspacing=0>";
  echo "
      <tr bgcolor='#ffffff'>
        
        <th> № </td>
        <th align='left'>Страница</td>  
        <th align='left'>Заголовок</td>
        <th></td>
        <th colspan=5>Действия</th>
      </tr>";
  
  # --- выборка ---
	$rez1=mysql_query("select id,lv,pn,name,full_name,vidimost from tpp_page order by pn asc");	  
	while($rs1=mysql_fetch_array($rez1)){    
		
    if($rs1['lv']=="1"){
        echo "<tr class='tr1'>";
        echo "<td>".$rs1['pn']."</td>";
        echo "<td style='padding-left: 6px;  width: 200px;'>".$rs1['name']."</td>";
    }else{
        echo "<tr class='tr2'>";
        echo "<td>".$rs1['pn']."</td>";
        echo "<td style='padding-left: 26px;  width: 200px;'>".$rs1['name']."</td>";
    }
       
    echo "<td style='width: 300px;'>".$rs1['full_name']."</td>";        
    echo "<td><a href='?make=chlevelpage&r=$tempr&rid=".$rs1['id']."'>";
          if($rs1['lv']==1){
              $temp_lv_info="Раздел";
          }else{
              $temp_lv_info="Подстраница";          
          }
    echo "$temp_lv_info</a></td>";
	  
    echo "<td><a href='?make=pnpageup&r=$tempr&rid=".$rs1['id']."' class='astrelka'>&uarr;</a></td>";
    echo "<td><a href='?make=pnpagedown&r=$tempr&rid=".$rs1['id']."' class='astrelka'>&darr;</a></td>";
    
    
    echo "<td><a href='?make=pokazpage&r=$tempr&rid=".$rs1['id']."'>";
         if($rs1['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыто";}
    echo "$tmppokaz</a></td>";
        
		echo "<td><a href='?make=editpage&r=$tempr&rid=".$rs1['id']."'>править</a></td>";			
		echo "<td><a href='?make=delpage&r=$tempr&rid=".$rs1['id']."'>X</a></td>";
            	
		echo "</tr>";
    
       
    
	};
	echo "</table>";








?>