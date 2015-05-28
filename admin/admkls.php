
<?




# ----- действия с классификатором ----------------------


# включение и выключение видимости
	if(@$_GET['make']=='pokazkls'){
		mysql_query("update tpp_kls set vidimost=-vidimost where (id=".$_GET['rid'].")");
	}

# порядковый номер (двигаем вверх)
	if(@$_GET['make']=='pnklsup'){
		      $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];
          $temp_parent_id=$temp_rs['parent_id'];                       
          if($temp_pn>1){
              $temp_pn=$temp_pn-1;
              mysql_query("update tpp_kls set pn=pn+1 where (parent_id=$temp_parent_id) and (pn=$temp_pn)");
              mysql_query("update tpp_kls set pn=pn-1 where (id=".$_GET['rid'].")");
          }
 	}


# порядковый номер (двигаем вниз)
	if(@$_GET['make']=='pnklsdown'){

		      $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].")");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_pn=$temp_rs['pn'];
          $temp_parent_id=$temp_rs['parent_id'];                                 

          $temp_rez=mysql_query("select max(pn) as maxpn from tpp_kls where (parent_id=$temp_parent_id)");
          $temp_rs=mysql_fetch_array($temp_rez);
          $temp_maxpn=$temp_rs['maxpn'];
                            
          if($temp_pn<$temp_maxpn){
              $temp_pn=$temp_pn+1;
              mysql_query("update tpp_kls set pn=pn-1 where (parent_id=$temp_parent_id) and (pn=$temp_pn)");
              mysql_query("update tpp_kls set pn=pn+1 where (id=".$_GET['rid'].")");
          

          }
 	}

# удаляем элемент классификатора

  if(@$_GET['make']=='delkls'){
        $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].")");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_pn=$temp_rs['pn'];
        $temp_parent_id=$temp_rs['parent_id'];                                         
        mysql_query("update tpp_kls set pn=pn-1 where (parent_id=$temp_parent_id) and (pn>$temp_pn)");    
        mysql_query("delete from tpp_kls where (id=".$_GET['rid'].")");
	}


# добавляем элемент классификатора

  if(@$_GET['make']=='addkls'){      
        
        $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['parent_id'].")");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_lv=$temp_rs['lv'];
        $temp_lv=$temp_lv+1;
        
        $temp_rez=mysql_query("select count(*) as kolvo from tpp_kls where (parent_id=".$_GET['parent_id'].")");
        $temp_rs=mysql_fetch_array($temp_rez);
        $temp_pn=$temp_rs['kolvo'];
        
        $temp_pn=$temp_pn+1;                                                    
        mysql_query("insert into tpp_kls(lv,pn,parent_id, name, vidimost) values($temp_lv, $temp_pn,".$_GET['parent_id'].",'".$_GET['name']."',-1)");
                
	}	
  
# обновляем элемент классификатора  
  
  if(@$_POST['make']=='updatekls'){
        $temp_sql="update tpp_kls set 
              name='".$_POST['name']."', 
              lname='".$_POST['lname']."',
              pattern='".$_POST['pattern']."',
              page='".$_POST['page']."',
              tip=".$_POST['tip'].",
              pos_left=".$_POST['pos_left'].",
              pos_top=".$_POST['pos_top'].",
              icon_width=".$_POST['icon_width'].",
              icon_height=".$_POST['icon_height']."                          
              where  (id=".$_POST['rid'].")";
        mysql_query($temp_sql);          
 
       #  если пришел признак удаления файла иконки
       #  физически файл не удаляем, но в БД иконку обнуляем
        if(@$_POST['delicon']=='del'){
            $temp_sql="update tpp_kls set 
              icon='нет'                       
              where  (id=".$_POST['rid'].")";
            mysql_query($temp_sql);
        }; 
 
       # // если пришел новый файл иконки
        if($_FILES['icon_file']['error']==0){
            $temp_fn=$_FILES['icon_file']['name'];
            $temp_fn=f_rename_filename($temp_fn);
            if(@copy($_FILES['icon_file']['tmp_name'],"../img/markers/$temp_fn")){
                # echo "== ОК ===";
                  $temp_sql="update tpp_kls set 
                             icon='$temp_fn'                       
                             where  (id=".$_POST['rid'].")";
                   mysql_query($temp_sql);                          
            }else{
                # echo "сбой загрузки файла";          
            }
                                              

            
         #   f_rename_filename   
        }; 
 
 
  }  
  
  
# ---------- интерфейс --------------------------------------------------------------------------

  $tempr=$_GET['r'];  
  
# ---------- форма правки --------------  
  
  if(@$_GET['make']=='editkls'){
    # загрузили все поля из БД      
    $temp_rez=mysql_query("select * from tpp_kls where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    
    echo "<div class='admbtn'>";
    echo "<form name='frm_edit' id='frm_edit' method='POST' action='adm.php' enctype='multipart/form-data'>";    
    echo "    <h2>Правка элемента классификатора</h2>";
    echo "    <b>Технические поля</b><br>";
    echo "    <input type='hidden' name='r' id='r' value='$tempr'><input type='hidden' name='make' id='make' value='updatekls'>";
    echo "    Код <input type='text' name='rid' id='rid' class='noedit' value='".$temp_rs['id']."' readonly><br>";    
    echo "    Уровень <input type='text' class='noedit' value='".$temp_rs['lv']."' readonly><br>";
    echo "    Поряд.номер <input type='text' class='noedit' value='".$temp_rs['pn']."' readonly><br>";
    echo "    Видимость <input type='text' name='name' class='noedit' value='".$temp_rs['vidimost']."' readonly><br>";
    echo " <hr>"; 
    echo "    <b>Поля коррекции</b><br>";
    echo "    Наименование <input type='text' name='name' id='name' value='".$temp_rs['name']."'><br>";
    echo "    Лат.название <input type='text' name='lname' id='lname' value='".$temp_rs['lname']."'><br>";  
    # если уровень = 3 то показываем поля 
    if($temp_rs['lv']>1){
          echo "    Шаблон заполнения: <textarea style='width:100%' name='pattern' id='pattern' rows='6'>".$temp_rs['pattern']."</textarea><br>";
          echo "    Страница описания: <textarea style='width:100%' name='page' id='page' rows='6'>".$temp_rs['page']."</textarea><br>";
    } 
      
    echo " <hr>";
    echo "    <b>Маркер иконки на карте</b><br>"; 
    echo "    Тип элемента 
                  <select name='tip' id='tip'>
                    <option value='0'>точечный объект</option>
                    <option value='1'>ломаная линия</option>
                    <option value='2'>контурный объект</option>
                  </select><br>
                  <script language='JavaScript'> gebi('tip').selectedIndex=".$temp_rs['tip'].";</script>";
    echo "      Изображение <input type='hidden1' name='icon' id='icon' class='noedit' value='".$temp_rs['icon']."'><br>";
          if(($temp_rs['icon']=="нет")or($temp_rs['icon']=="")){
              echo "Графического маркера нет (ожидается: PNG, примерно 30x70 px, прозрачный фон)<br>";
              echo "<input type='file' name='icon_file' id='icon_file'><br>"; 
          }else{
              echo "    Выглядит <img style='border: 1px dotted red;' src='../img/markers/".$temp_rs['icon']."'> <input type='hidden' name='delicon' id='delicon' value='none'>"; 
                        echo "  <span OnClick='gebi(\"delicon\").value=\"del\";this.innerHTML=\"Помечено для удаления\"'><u>Удалить маркер</u></a></span><br>";
          }
    echo "    <br><b>Размер иконки</b> / в пикселях для показа на карте<br>";
    echo "    Ширина: <input type='text' name='icon_width' id='icon_width' value='".$temp_rs['icon_width']."'><br>";
    echo "    Высота: <input type='text' name='icon_height' id='icon_height' value='".$temp_rs['icon_height']."'><br>";
        
    echo "    <br><b>Смещение базовой точки</b> / в пикселях от левого верхнего угла<br>";
    echo "    Отступ слева: <input type='text' name='pos_left' id='pos_left' value='".$temp_rs['pos_left']."'><br>";
    echo "    Отступ сверху: <input type='text' name='pos_top' id='pos_top' value='".$temp_rs['pos_top']."'><br>";
    echo " <hr>";
    echo "    <input type='submit' value='Внести изменения'><br>";   
    echo "</form>";
    echo "</div>";
  }
        
  
# ---------- форма добавления нового --------------
	# если 
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editkls'){ echo "style='display: none;' ";};
  
  echo ">   <h2 id='h2_add_kls'>Добавить элемент</h2>";
  
  echo "<div class='form' id='d_add' style='display: none;'>";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='addkls'>";
  echo "    Наименование: <input type='text' name='name' id='name' value=''><br><br>";
  echo "    Куда добавить: <select name='parent_id' id='parent_id'>";
  
          $rez1=mysql_query("select id,lv,pn,name from tpp_kls where (lv=0)");
          $rs1=mysql_fetch_array($rez1);
          echo "<option value=".$rs1['id']."> --- в корень --- </option>";
          
  
        	$rez1=mysql_query("select id,lv,pn,name from tpp_kls where (lv=1) order by pn asc");	  
	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id']." style='background-color:#C5D3BD;'>";
                  echo $rs1['name'];
                  echo "</option>";
                  $rez2=mysql_query("select id,lv,pn,name from tpp_kls where (lv=2) and (parent_id=".$rs1['id'].") order by pn asc");
                  while($rs2=mysql_fetch_array($rez2)){
                      echo "<option value=".$rs2['id']." style='background-color:#FAF6E4;'>";
                      echo "&nbsp;&nbsp;&nbsp;".$rs2['name'];
                      echo "</option>";
                  }
                  
                  
          };
      
  echo "              </select> <br><br>";  
  echo "    Тип: <select name='tip' id='tip'>
                    <option value='0'>точечный объект</option>
                    <option value='1'>ломаная линия</option>
                    <option value='2'>контурный объект</option>
                  </select><br><br>";    
  echo "    <input type='submit' value='Создать элемент'><br>";
  echo "</form>";
  echo "</div>";
  
  echo "</div>";
  



# -----------------------------------------------------------------------------------------------
# -------- просмотр действующего классификатора -----------
# -----------------------------------------------------------------------------------------------



	echo "<h2>Работа с классификатором</h2>";
  

  
    
  echo "<table  border=0 cellpadding=6 cellspacing=0>";
  echo "
      <tr bgcolor='#ffffff'>
        <td>Классификатор</td>  
        <td>Геообъекты</td>
        <td>ID</td>
        <td>Уровень</td>
        <td>№</td>
      </tr>";
  
  # --- выборка верхнего уровня ---
	$rez1=mysql_query("select id,lv,pn,name,lname, icon, pattern,vidimost from tpp_kls where (lv=1) order by pn asc");	  
	while($rs1=mysql_fetch_array($rez1)){    
		echo "<tr class='tr1'>";
    echo "<td style='padding-left: 6px;  width: 400px;'>".$rs1['name']."</td>";
    echo "<td>&nbsp;</td>";
    
		echo "<td>".$rs1['id']."</td>";
    echo "<td>".$rs1['lv']."</td>";
	  echo "<td>".$rs1['pn']."</td>";
    echo "<td><a href='?make=pnklsup&r=$tempr&rid=".$rs1['id']."' class='astrelka'>&uarr;</a></td>";
    echo "<td><a href='?make=pnklsdown&r=$tempr&rid=".$rs1['id']."' class='astrelka'>&darr;</a></td>";
    
    
    echo "<td><a href='?make=pokazkls&r=$tempr&rid=".$rs1['id']."'>";
         if($rs1['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыт";}
    echo "$tmppokaz</a></td>";
        
		echo "<td><a href='?make=editkls&r=$tempr&rid=".$rs1['id']."'>править</a></td>";			
		echo "<td><a href='?make=delkls&r=$tempr&rid=".$rs1['id']."'>X</a></td>";
        
    	
		echo "</tr>";
    
       
    
    
    
    // если есть дочерние элементы 2 уровня - показываем их
    $rez2=mysql_query("select id,lv,pn,name,lname, icon, pattern,vidimost from tpp_kls where (lv=2) and (parent_id=".$rs1['id'].") order by pn asc");
    while($rs2=mysql_fetch_array($rez2)){
        echo "<tr class='tr2'>";
        echo "<td style='padding-left: 30px;'>".$rs2['name']."</td>";
        echo "<td>&nbsp;</td>";
		    echo "<td>".$rs2['id']."</td>";
        echo "<td>".$rs2['lv']."</td>";	
        echo "<td>".$rs2['pn']."</td>";
        echo "<td><a href='?make=pnklsup&r=$tempr&rid=".$rs2['id']."' class='astrelka'>&uarr;</a></td>";
        echo "<td><a href='?make=pnklsdown&r=$tempr&rid=".$rs2['id']."' class='astrelka'>&darr;</a></td>";
        
        echo "<td><a href='?make=pokazkls&r=$tempr&rid=".$rs2['id']."'>";
                if($rs2['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыт";}
        echo "$tmppokaz</a></td>";
	
      	echo "<td><a href='?make=editkls&r=$tempr&rid=".$rs2['id']."'>править</a></td>";		
		    echo "<td><a href='?make=delkls&r=$tempr&rid=".$rs2['id']."'>X</a></td>";
        
		    echo "</tr>";


        // если есть дочерние элементы 3 уровня - показываем их
        $rez3=mysql_query("select id,lv,pn,name,lname, icon, pattern, vidimost from tpp_kls where (lv=3) and (parent_id=".$rs2['id'].") order by pn asc");
        while($rs3=mysql_fetch_array($rez3)){
            echo "<tr>";
            echo "<td style='padding-left: 60px;'>".$rs3['name']."</td>";
            echo "<td><a href='?make=filtergeoob&r=2&kls_id=".$rs3['id']."' class='astrelka'>&rarr;</a></td>";
    		    echo "<td>".$rs3['id']."</td>";
            echo "<td>".$rs3['lv']."</td>";	
            echo "<td>".$rs3['pn']."</td>";
            echo "<td><a href='?make=pnklsup&r=$tempr&rid=".$rs3['id']."' class='astrelka'>&uarr;</a></td>";
            echo "<td><a href='?make=pnklsdown&r=$tempr&rid=".$rs3['id']."' class='astrelka'>&darr;</a></td>";
            
            echo "<td><a href='?make=pokazkls&r=$tempr&rid=".$rs3['id']."'>";
                if($rs3['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыт";}
            echo "$tmppokaz</a></td>";

          	echo "<td><a href='?make=editkls&r=$tempr&rid=".$rs3['id']."'>править</a></td>";	
    		    echo "<td><a href='?make=delkls&r=$tempr&rid=".$rs3['id']."'>X</a></td>";	            
            
    		    echo "</tr>";
        };
                
    };          
    
	};
	echo "</table>";








?>