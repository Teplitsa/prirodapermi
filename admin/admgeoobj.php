<?

# ----- действия с геообъектами ----------------------
  
# включение и выключение видимости
	if(@$_GET['make']=='pokazgeoob'){
		mysql_query("update tpp_geoobject set vidimost=-vidimost where (id=".$_GET['rid'].")");
	}

# удаляем геообъект

  if(@$_GET['make']=='delgeoob'){           
        mysql_query("delete from tpp_geoobject where (id=".$_GET['rid'].")");
	}

# переключение "требуется защита"
	if(@$_GET['make']=='defgeoob'){
		mysql_query("update tpp_geoobject set need_def=-need_def where (id=".$_GET['rid'].")");
	}

# переключение "требуется мониторинг"
	if(@$_GET['make']=='mongeoob'){
		mysql_query("update tpp_geoobject set need_mon=-need_mon where (id=".$_GET['rid'].")");
	}
  
  
  
  
# добавляем геообъект

  if(@$_GET['make']=='addgeoob'){  
           
       # сначала проверяем сколько точно таких же объектов
         $tmpsql="select count(*) as kolvo from tpp_geoobject where (name='".$_GET['name']."') and (kls_id=".$_GET['kls_id'].") and (event_id=".$_GET['event_id'].") and (coord='".$_GET['coord']."')";
         $temp_rez=mysql_query($tmpsql);
         $temp_rs=mysql_fetch_array($temp_rez);
         $temp_kolvo=$temp_rs['kolvo'];
         
       # если ноль - то вставляем (если уже есть то игнорируем пришедший дубликат)
         if($temp_kolvo==0){
            
            # галочки "требуется защита" и "наблюдение"
            if($_GET['need_def']=="on"){ $tempdef=1;} else {$tempdef=-1;} 
            if($_GET['need_mon']=="on"){ $tempmon=1;} else {$tempmon=-1;}    
                         
            $tmpsql="insert into tpp_geoobject(name, kls_id, event_id, coord, page, kart, ud, 
                      vidimost, user_c, dd_c, user_u, dd_u, kolvo, need_def, need_mon) 
                      values( '".$_GET['name']."',".$_GET['kls_id'].",".$_GET['event_id'].",
                        '".$_GET['coord']."','".$_GET['page']."','".$_GET['kart']."',".$_GET['ud']." ,
                         -1, 'admin', now(), 'admin', now(),".$_GET['kolvo'].",$tempdef, $tempmon)";                                                    
            mysql_query($tmpsql);
         }   
          
                
	}	  
  

# если пришла команда фильтрации / поиска
  $temp_dopusl1="(1=1)";
  $temp_dopusl2="(1=1)";
  $temp_dopusl3="(1=1)";
  $temp_dopusl4="(1=1)";

  if(@$_GET['make']=='filtergeoob'){
      # если пришел код геообъекта
        if($_GET['filter_code']!=""){  $temp_dopusl1="(id=".$_GET['filter_code'].")"; };        
     # если пришел класс геообъекта
        if($_GET['kls_id']!=0){ $temp_dopusl2="(kls_id=".$_GET['kls_id'].")";};        
     # если пришло событие увязки геообъекта
        if($_GET['event_id']!=0){ $temp_dopusl3="(event_id=".$_GET['event_id'].")";};                                                                        
     # если пришел фрагмент названия
        if($_GET['filter_name']){ $temp_dopusl4="(name like '%".$_GET['filter_name']."%')";};
	}
  
# обновляем геообъект  
  
  if(@$_POST['make']=='updategeoob'){
  
            # галочки "требуется защита" и "наблюдение"
            if($_POST['need_def']=="on"){ $tempdef=1;} else {$tempdef=-1;} 
            if($_POST['need_mon']=="on"){ $tempmon=1;} else {$tempmon=-1;}    
  
            $temp_sql="update tpp_geoobject set 
                  name='".$_POST['name']."', 
                  coord='".$_POST['coord']."',
                  ud=".$_POST['ud'].",
                  event_id=".$_POST['event_id'].",
                  kart='".$_POST['kart']."',
                  page='".$_POST['fe_page']."',
                  dd_u=now(),
                  kolvo=".$_POST['kolvo'].",
                  need_def=$tempdef,
                  need_mon=$tempmon                                        
                  where  (id=".$_POST['rid'].")";
            mysql_query($temp_sql);             
  }  
  

  
  
  


# ---------- интерфейс --------------------------------------------------------------------------

  $tempr=getparam("r","1");  
  
  
# ---------- форма правки --------------  
  
  if(@$_GET['make']=='editgeoob'){
    # загрузили все поля из БД      
    $temp_rez=mysql_query("select * from tpp_geoobject where (id=".$_GET['rid'].")");
    $temp_rs=mysql_fetch_array($temp_rez);
    
    echo "<div class='admbtn'>";
    echo "<form name='frm_edit' id='frm_edit' method='POST' action='adm.php' enctype='multipart/form-data'>";    
    echo "    <h2>Правка геообъекта</h2>";
    echo "    <b>Технические поля</b><br>";
    echo "    <input type='hidden' name='r' id='r' value='$tempr'><input type='hidden' name='make' id='make' value='updategeoob'>";
    echo "    Код <input type='text' name='rid' id='rid' class='noedit' value='".$temp_rs['id']."' readonly><br>";            
    echo "    Видимость <input type='text' name='name' class='noedit' value='".$temp_rs['vidimost']."' readonly><br>";
    echo " <hr>"; 
    echo "    <b>Поля коррекции</b><br>";
    echo "    Наименование <input type='text' name='name' id='name' value='".$temp_rs['name']."' style='width: 300px;'><br><br>";
    
    echo "  Привязка к событию: <select name='event_id' id='event_id'>";                    
            echo "<option value=0> --- без события --- </option>"; 
       	    $rez1=mysql_query("select id,name from tpp_event order by name asc");	  
	           while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==$temp_rs['event_id']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                                                      
            };          
    echo "    </select><br><br> ";
    echo "    Уровень доверия <input type='text' name='ud' id='ud' value='".$temp_rs['ud']."' style='width:30px;'> &nbsp;&nbsp;";
    
    echo "    Количество<input type='text' name='kolvo' id='kolvo' value='".$temp_rs['kolvo']."' style='width:30px;'>&nbsp; &nbsp; ";
    
    if($temp_rs['need_def']==1){ $tempdef=" checked ";};
    echo "    Требуется:   <input type='checkbox' name='need_def' id='need_def' $tempdef > защита &nbsp; ";
    if($temp_rs['need_mon']==1){ $tempmon=" checked ";};
    echo "                 <input type='checkbox' name='need_mon' id='need_mon' $tempmon > наблюдение &nbsp; <br><br> ";
    
    
    echo "    Координаты <input type='text' name='coord' id='fe_coord' value='".$temp_rs['coord']."' style='width: 400px;'> "; 
    
    echo "  <span id='btn_edit_geoob' style='background-color: #ccccff;'>Уточнить на карте</span><br> 
            <div id='admmap'></div><br><br>";                               
    
    echo "    Карточка:<br> <textarea style='width:100%' name='kart' id='kart' rows='8'>".$temp_rs['kart']."</textarea><br>";
    echo "    Краткое описание:<br> <textarea style='width:100%' name='fe_page' id='fe_page' rows='8'>".$temp_rs['page']."</textarea><br>";
    echo " <hr>";
    echo "    <input type='submit' value='Внести изменения'><br>";   
    echo "</form>";
    echo "</div>";    
    
      
 } 


# ---------- форма фильтрации --------------
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editgeoob'){ echo "style='display: none;' ";};
  echo ">   <h2 id='h2_filter_geoob'>Фильтр / поиск</h2>";
  
  echo "<div class='form' id='d_filter' ";   
  if(@$_GET['make']!='filtergeoob'){ echo "style='display: none;' ";};
    
  echo ">";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='filtergeoob'>";
  echo "    Код: <input type='text' name='filter_code' id='filter_code' value='".@$_GET['filter_code']."' style='width: 40px;'> &nbsp;&nbsp; ";
  echo "    Название: <input type='text' name='filter_name' id='filter_name' value='".@$_GET['filter_name']."' style='width: 80px;'> &nbsp;&nbsp; ";
  
  
  echo "    Класс: <select name='kls_id' id='kls_id'>";                    
          echo "<option value=0> --- без увязки --- </option>";            
        	$rez1=mysql_query("select id,lv,pn,name from tpp_kls where (lv=3) order by name asc");	  
	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==@$_GET['kls_id']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                                                      
          };
      
  echo "              </select> &nbsp;&nbsp;&nbsp; ";
  echo "  Событие: <select name='event_id' id='event_id'>";                    
          echo "<option value=0> --- без увязки --- </option>"; 
       	  $rez1=mysql_query("select id,name from tpp_event order by name asc");	  
	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'];
                  if($rs1['id']==@$_GET['event_id']){
                          echo " selected ";                        
                  }
                  echo ">";
                  echo $rs1['name'];
                  echo "</option>";                                                      
          };          
          
          
                         
  echo "              </select> &nbsp;&nbsp;&nbsp; ";  
        
  echo "    <input type='submit' value='Применить фильтр'><br>";
  echo "</form>";
  echo "</div>";
  echo "</div><br>";

  
# ---------- форма добавления нового --------------
  
  echo "<div class='admbtn' ";
  if(@$_GET['make']=='editgeoob'){ echo "style='display: none;' ";};
  
  echo ">   <h2 id='h2_add_geoob'>Добавить геообъект</h2>";
  
  echo "<div class='form' id='d_add' style='display: none;'>";
  echo "<form name='frm_add' id='frm_add' method='get'>";
  echo "    <input type='hidden' name='r' id='r' value='$tempr'>";
  echo "    <input type='hidden' name='make' id='make' value='addgeoob'>";
  echo "    <input type='hidden' name='tip' id='fadd_tip' value=''>";
  echo "    Наименование: <input type='text' name='name' id='fadd_name' value=''> &nbsp;&nbsp;";
  echo "    Класс: <select name='kls_id' id='fadd_kls_id'>";
  
            echo "<option value=0> --- без класса --- </option>";
            $rez1=mysql_query("select id,lv,name from tpp_kls where (lv=3) order by name asc");	  
  	        while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'].">";
                  echo $rs1['name'];
                  echo "</option>";                                 
            };      
  echo "              </select> &nbsp;&nbsp;";
  
  echo "    Событие: <select name='event_id' id='event_id'>";
                        echo "<option value=0> --- нет связи --- </option>";   
      	     $rez1=mysql_query("select id,name from tpp_event order by name asc");	  
	           while($rs1=mysql_fetch_array($rez1)){
                  echo "<option value=".$rs1['id'].">";                  
                  echo $rs1['name'];
                  echo "</option>";                                                      
          };                                  
                        
                        
                                                   
  echo "             </select> <br><br>";
  echo "  <div id='dinfokls' style='color:#555555;'></div>";
  
  echo "  <div id='fadd_tmp_pat' style='display:none; color:red'></div>";
  
  echo "    Точечные координаты: <input style='width: 400px;' type='text' name='coord' id='coord' value=''> ";
  
  echo "    <span id='btn_new_object' style='background-color: #ffcccc;'>Указать на карте</span><br><br> 
            <div id='admmap'></div><br><br>";
  
  echo "    Карточка объекта (формуляр от класса): <textarea style='width:100%' name='kart' id='fadd_pattern' rows='4'></textarea><br><br>";
  
  echo "    Краткое описание: <textarea style='width:100%' name='page' id='page' rows='8'></textarea><br>";
  echo "    Уровень доверия: <input type='text' name='ud' id='ud' value='0'> &nbsp;&nbsp;";
  
  echo "    Количество: <input type='text' name='kolvo' id='kolvo' value='1' style='width:30px;'> &nbsp;&nbsp;&nbsp; ";
  
  echo "    Требуется: <input type='checkbox' name='need_def' id='need_def'> защита &nbsp;&nbsp; ";
  echo "    <input type='checkbox' name='need_mon' id='need_mon'> наблюдение <br><br> ";
    
  echo "    <input type='submit' value='Создать элемент'><br>";
  echo "</form>";
  echo "</div>";
  
  echo "</div>";
  


    
# -----------------------------------------------------------------------------------------------
# -------- просмотр действующего реестра объектов -----------
# -----------------------------------------------------------------------------------------------


	echo "<h2>Реестр геообъектов</h2>";
	$rez=mysql_query("select id,kls_id,name,coord,ud,dd_c,user_c,dd_u,user_u,event_id,vidimost,kolvo,need_def,need_mon from tpp_geoobject where $temp_dopusl1 and $temp_dopusl2 and $temp_dopusl3 and $temp_dopusl4 order by id desc");
	echo "<table cellspacing=0 cellpadding=8 class='tableadmin'>";
  echo "<tr>
            <th>Код</th><th>Тип</th>
            <th>Название</th><th>Событие</th><th>Координаты</th>
            <th>Уровень доверия</th><th>Дата обновления</th>
            <th>Внимание</th>
            <th colspan=3>Действия</th>
            
        </tr>";
	while($rs=mysql_fetch_array($rez)){
		echo "<tr>";
		echo "<td>".$rs['id']."</td>";	
    echo "<td>".$rs['kls_id']."</td>";
    echo "<td nowrap>".$rs['name']."</td>";
    echo "<td align='center'>";
        if($rs['event_id']!=0){
            echo "<a href='adm.php?r=3&make=filterevent&filter_code=".$rs['event_id']."&filter_name=&filter_type_event=0&filter_mm=0' class='astrelka'>&rarr;</a>";
        }else{
            echo "&nbsp;";
        };
    echo "</td>";
    echo "<td>".$rs['coord']."</td>";
    echo "<td>".$rs['ud']."</td>";
    echo "<td>".$rs['dd_u']."</td>";
    
    echo "<td>";
            if($rs['kolvo']!="1"){  echo $rs['kolvo']." "; }
            if($rs['need_def']=="1"){   $tempdef="<font color='red'>защита</font><br>"; } else{      $tempdef=""; }
            echo "<a href='?make=defgeoob&r=$tempr&rid=".$rs['id']."' class='astrelka'>$tempdef</a> ";
            if($rs['need_mon']=="1"){   $tempmon="<font color='blue'>мониторинг</font><br>"; } else{  $tempmon=""; }
            echo "<a href='?make=mongeoob&r=$tempr&rid=".$rs['id']."' class='astrelka'>$tempmon</a>";
        
        
      echo "</td>";
    
    
    echo "<td><a href='?make=pokazgeoob&r=$tempr&rid=".$rs['id']."'>";
         if($rs['vidimost']==1){ $tmppokaz="показывается";}else{$tmppokaz="скрыт";}
    echo "$tmppokaz</a></td>";        
		echo "<td><a href='?make=editgeoob&r=$tempr&rid=".$rs['id']."'>править</a></td>";			
		echo "<td><a href='?make=delgeoob&r=$tempr&rid=".$rs['id']."'>X</a></td>";
   
		echo "</tr>";
	};
	echo "</table>";





?>