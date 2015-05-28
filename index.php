<?
  # ==== подключаем ===============
	include("clientfunction.php");
?>

<!DOCTYPE html> 
<html>
<head>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        
        <META NAME="Author" CONTENT="Студия Жанр, www.ЖАНР.рф" />
        <META NAME="sponsor" CONTENT="Приложение создано при поддержке Теплицы социальных технологий" /> 
         
        <link href="css/main.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
                
        <link href="css/jquery.datepick.css" rel="stylesheet">
        <script type="text/javascript" src="js/jquery.plugin.js"></script>

        <script type="text/javascript" src="js/jquery.datepick.js"></script>
        <script type="text/javascript" src="js/jquery.datepick-ru.js"></script>
        
                
        <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
        <script type="text/javascript">



        
                
ymaps.ready(function(){

        // создаем карту и связываем ее с областью HTML 
          var myMap = new ymaps.Map("map", {
              center: [57.99563333354639, 56.253152290527396],                                                                
              zoom: 13, controls: ['fullscreenControl']
           });
           
           
           var myCollection = new ymaps.GeoObjectCollection();
           
           myMap.geoObjects.add(myCollection);
 
        // При клике на карту все метки коллекции исчезают (удалены) - ок
          myCollection.getMap().events.add('click',function() {
                myCollection.removeAll();
          }); 

          
// ================= функция показа объектов kls по коду =========          
function f_pokaz_kls_on_map(temp_kod){

    $.getJSON("_getgeoobjects.php?rid="+String(temp_kod),
        function(json){                        
        
        temp_icon_file=json.icon_file;
        temp_icon_width=json.icon_width;
        temp_icon_height=json.icon_height;
        temp_icon_pos_left=json.icon_pos_left;
        temp_icon_pos_top=json.icon_pos_top;
        
         for(a in json.contents){
        
            temp_id=json.contents[a].id;
            temp_name=json.contents[a].name;                   
            temp_kart=json.contents[a].kart;
                        
            temp_str=String(json.contents[a].coord);
            
            stradd="";
            if(json.contents[a].kolvo>=0){stradd=stradd+"<hr /><font color=\"#9C9B9A\">Количество: "+String(json.contents[a].kolvo)+" шт.</font>"}            
            if(json.contents[a].need_def==1){stradd=stradd+"<br /><font color=\"#ff0000\">Требуется защита!</font>"}
            if(json.contents[a].need_mon==1){stradd=stradd+"<br /><font color=\"#000077\">Нужно наблюдение!</font>"}            
                        
            ss="myPlacemark = new ymaps.Placemark(["+String(temp_str)+"], {hintContent: '"+String(temp_name)+"', balloonContentHeader: '<font color=\"#E19222\">"+String(temp_name)+"</font>', balloonContent: '<hr />"+String(temp_kart)+" "+String(stradd)+"', balloonContentFooter: '<a href=\"javascript:f_show_geoobject("+String(temp_id)+");\">спецификация</a>'}, { iconLayout: 'default#image', iconImageHref: 'img/markers/"+String(temp_icon_file)+"', iconImageSize: ["+String(temp_icon_width)+","+String(temp_icon_height)+"], iconImageOffset: ["+String(temp_icon_pos_left)+", "+String(temp_icon_pos_top)+"] })";
            eval(ss);  
            
            myCollection.add(myPlacemark);
        }
        
        
        // только если автозум включен, то выполняем автомасштабирование
        if((parseInt($("#d_auto_zoom").attr('autozoom')))==1){
                myMap.setBounds(myMap.geoObjects.getBounds(), {checkZoomRange: true });
        }
        
        
        
        });
}          
      
      


$("#btn_new_object").click(function(){

    // ======================================== Создаем геообъект с типом геометрии "Точка".
    
    if(typeof myGeoObject == "undefined"){
        // первичное создание объекта
    
        aa=myMap.getCenter();
        myGeoObject = new ymaps.GeoObject({
            // Описание геометрии.
            geometry: {
                type: "Point",
                coordinates: aa
            },
            // Свойства.
            properties: {                                
                iconContent: 'Укажите место',
                hintContent: 'Укажите точное местоположение нового объекта'
            }
        }, {
            // Опции.
            // Иконка метки будет растягиваться под размер ее содержимого.
            preset: 'islands#redStretchyIcon',
            // Метку можно перемещать.
            draggable: true
        });
        
        myMap.geoObjects.add(myGeoObject);


//         var log = document.getElementById('log');
        myGeoObject.events.add([
//        'mapchange', 'geometrychange', 'pixelgeometrychange', 'optionschange', 'propertieschange',
//        'balloonopen', 'balloonclose', 'hintopen', 'hintclose',
//         'dragstart', 
          'dragend'
        ], function (e) {
 //       alert(myGeoObject.geometry.coordinates);
 // new_ob_coords
 //alert(myGeoObject.geometry.getCoordinates());
        document.getElementById("new_ob_coords").value=myGeoObject.geometry.getCoordinates();

        //log.innerHTML = '@' + e.get('type') + '<br/>' + log.innerHTML;
        });
    }else{
          // метка установлена ранее на карте - нужно лишь передвинуть в центр карты
        myGeoObject.geometry.setCoordinates(myMap.getCenter());
    
    };
 
});

// ================== кнопка сохранить

$("#save_new_ob").click(function(){

    if(document.getElementById("new_ob_coords").value==""){
        alert('Пока нечего сохранять');
    
    }else{
        alert('Сохраняем');
    
    }
    
    // убираем метку
});
        

// ====================== клик по глазу (показ вложенных элементов группы)  ===============
 $(".d_gr .linkpokaz").click(function(clickEvent ){
    
    //  код объекта для показа на карте приходит в $(this).attr('kod');
    var temp_kod_gr=$(this).attr('kod');

    //зная код группы находим и перебираем все вложенные пункты в соответствующей подгруппе     
    
    $("#d_pod_gr_"+String(temp_kod_gr)).find(".dend").each(function(i,elem){
            temp_kod=$(elem).attr('kod');        
            f_pokaz_kls_on_map(temp_kod);  
    });

    // этот медот останавливает передачу события вверх по DOM (родителям не передаем click)    
    clickEvent.stopPropagation();

});



// ================================================================================================
// показ объектов на карте при выборе элемента из справочника

$(".dend").click(function(){

    //  код объекта для показа на карте приходит в $(this).attr('kod');  
    var temp_kod=$(this).attr('kod');      
    f_pokaz_kls_on_map(temp_kod);
 
});


// ======================= клик по кнопке открытия описания класса (справа)) ===============
$(".dend .linkinfo").click(function(){

    //  код объекта для показа на карте приходит в $(this).attr('kod');
      
    var temp_kod=$(this).attr('kod');
    // alert(temp_kod);          
    
        $.getJSON("_getklsinfo.php?rid="+String(temp_kod),
        function(json){
                                                                                      
             $('#d_infokls .h').html(json.name);
             $('#d_infokls .lname').html(json.lname);             
             $('#d_infokls .content').html(json.page);
                          
             $('#d_infokls .btn_na_karte').attr("kls_id", String(json.kls_id));
             
             $('#d_infokls .klscnt').html(json.metok_na_karte);
             $('#d_infokls .klskolvo').html(json.kolvo_na_karte);
                                                              
                     
      });  
     
     $('#d_infokls').animate({opacity: 1, top:120, left: 220},500);    
     
});



  


// ============ в окне события нажата кнопка "показать на карте" =============
// если код связанного класса kls>0 (достпен через атрибут кнопки) тогда показываем            

$(".btn_na_karte").click(function(){

      temp_kod=$(this).attr('kls_id');
      if(String(temp_kod)!="0"){
            f_pokaz_kls_on_map(temp_kod);
      };
});



// ====== нажатие на кнопку автозумирования ==========================
// чередуем включено или выключено
$("#d_auto_zoom").click(function(){    
    
    temp_value=parseInt($(this).attr('autozoom'));
    temp_value=-temp_value;
    $("#d_auto_zoom").attr('autozoom',String(temp_value));
    
    if(temp_value==1){
        $("#d_auto_zoom #d_zoom_on").show();
        $("#d_auto_zoom #d_zoom_off").hide();
    }else{
        $("#d_auto_zoom #d_zoom_on").hide();
        $("#d_auto_zoom #d_zoom_off").show();
    }
});

           
});




 
 
</script>
                   

</head>

<body>

<div id="d_up_menu">

  <!-- 
    <div style="width: 250px; float: left; padding: 14px;">
        <div style="width: 200px; float: left; font-size: 12pt; ">Счетчик экологической активности:</div>
        <div style="width: 30px; float: left;"><span class="ballov">21</span></div>
  
  </div>
  
  -->
  
  <div style="width: 220px; float: left; padding: 14px;">
        <div style="width: 220px; float: left; font-size: 12pt; ">Экологический проект <span class="ballov">Природа Перми</span></div>        
  
  </div>
  
  <?  f_upmenu(); ?>

  
  <div style="float: left; width: 30px;">&nbsp;</div>
  <div id="d_auto_zoom" autozoom="1"><div id="d_zoom_on" title="Автомасштаб включен"></div><div id="d_zoom_off" title="Автомасштаб выключен"></div></div>
  
   
    
  
</div>

<div id="d_logo"><img src="img/logo.png" /></div>

<!-- ======================================================================================================================================== -->

<div id="d_priroda">
<?  f_rmenu(); ?>
              


    
</div>
<div id="d_aktiv">
    <img src="img/new_object.png" style="cursor: pointer;" id="btn_new_object">
    <div>      
        <input type="text" name="new_ob_coords" id="new_ob_coords" value="Пусто"> <br>  
        <input type="button" value="Сохранить" id="save_new_ob">
                  
    </div>
</div>

<div id="map"></div>
 
<div id="d_event">
 <h3>Календарь природы</h3>
    <div id="d_calend">
        
          <div id="inlineDatepicker"></div>        
          <input type='hidden' name='calendar_all' id='calendar_all' size=20>          
          <input type='hidden' name='calendar_d' id='calendar_d' size=5>
          <input type='hidden' name='calendar_m' id='calendar_m' size=5>
          <input type='hidden' name='calendar_y' id='calendar_y' size=5>        
    </div>
 
    <h3>Фенологические события</h3>    
    <div id='d_event_list'>    
    </div>    
</div>



<div id="d_infopage">
        <div id="d_info_close">Закрыть</div>
        <div id="d_info_cont">              
            <div class="d_podmenu">пункты подменю</div>                
            <h1 style="margin-bottom: 12px;">заголовок</h1>                        
            <div class="d_page">страница</div>
            <div class="d_page_add">страница2</div>            
        </div>
        
</div>

<div id="d_infoevent">
        <div id="d_infoevent_close">Закрыть</div>
       <div class="h">Заголовок</div>
       <div style="clear: both;"></div>
       <span class="dataperiod">период</span>
       <span class="type_name">тип события</span>
       <div class="content">информация</div>
       
       <div class="ukazatel">
          <table border=0 width=100% height=100%>
          <tr>
              <td>Природный объект: <span class="kls_name">название</span></td>
              <td>
                  <img src="img/btn_na_karte.png" class="btn_na_karte"  kls_id="0" />
              </td>
          </tr>
          </table>                              
      </div>
</div>

<div id="d_infokls">
        <div id="d_infokls_close">Закрыть</div>
       <div class="h">Заголовок</div>
       <div style="clear: both;"></div>
       Латинское наименование: <span class="lname">лат.наименование</span>       
       <div class="content">информация</div>
       
        <table border=0 width=100% height=100%>
          <tr>
              <td>
                Меток на карте: <span class="klscnt">меток</span><br>
                Количество объектов:<span class="klskolvo">штук</div>              
              </td>
              <td>
                <img src="img/btn_na_karte.png" class="btn_na_karte"  kls_id="0" />           
              </td>
          </tr>
          </table>        
</div>




</body>
</html>
