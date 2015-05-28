ymaps.ready(function(){


        // создаем карту и связываем ее с областью HTML 
          var myMap = new ymaps.Map("admmap", {
           center: [57.99563333354639, 56.253152290527396],                                                                
           zoom: 13, controls: ['zoomControl', 'searchControl', 'typeSelector',  'fullscreenControl']
           });
           
           
           var myCollection = new ymaps.GeoObjectCollection();           
            myMap.geoObjects.add(myCollection);
  
  
  
// =================== правка геообъекта (администратор) =====================  
$("#btn_edit_geoob").click(function(){
  if(typeof myGeoObjectOld == "undefined"){
        // первичное создание объекта
    
        aa=myMap.getCenter();
        
        // берем данные из поля координат и заносим в массив aa
        tmp_old=$("#fe_coord").val();
        eval("aa=["+String(tmp_old)+"]");
                
        myGeoObjectOld = new ymaps.GeoObject({
            // Описание геометрии.
            geometry: {
                type: "Point",
                coordinates:   aa
            },
            // Свойства.
            properties: {                                
                iconContent: 'Уточните место',
                hintContent: 'Уточните местоположение'
            }
        }, {
            // Опции.
            // Иконка метки будет растягиваться под размер ее содержимого.
            preset: 'islands#redStretchyIcon',
            // Метку можно перемещать.
            draggable: true
        });        
        myMap.geoObjects.add(myGeoObjectOld);

        myGeoObjectOld.events.add([
           'dragend'
        ], function (e) {
              document.getElementById("fe_coord").value=myGeoObjectOld.geometry.getCoordinates();
        });
    }else{
          // метка установлена ранее на карте - нужно лишь передвинуть в центр карты
        myGeoObjectOld.geometry.setCoordinates(myGeoObjectOld.geometry.getCoordinates());
    
    };
    myMap.setBounds(myMap.geoObjects.getBounds(), {checkZoomRange: true });
});


// =================== расположение нового геообъекта (администратор) =====================  
$("#btn_new_object").click(function(){
  if(typeof myGeoObject == "undefined"){
        // первичное создание объекта
        aa=myMap.getCenter();
        myGeoObject = new ymaps.GeoObject({
            // Описание геометрии.
            geometry: {
                type: "Point",
                coordinates:   aa
            },
            // Свойства.
            properties: {                                
                iconContent: 'Укажите место',
                hintContent: 'Укажите местоположение'
            }
        }, {
            // Опции.
            // Иконка метки будет растягиваться под размер ее содержимого.
            preset: 'islands#redStretchyIcon',
            // Метку можно перемещать.
            draggable: true
        });        
        myMap.geoObjects.add(myGeoObject);

        myGeoObject.events.add([
           'dragend'
        ], function (e) {
              document.getElementById("coord").value=myGeoObject.geometry.getCoordinates();
        });
    }else{
          // метка установлена ранее на карте - нужно лишь передвинуть в центр карты
        myGeoObject.geometry.setCoordinates(myGeoObject.geometry.getCoordinates());
    
    };
  
});




  




// ==================== JSON ============================================

function KlsReload(num){

    $.getJSON("_getkls.php?rid="+String(num),
        function(json){
                        
        alert(json.name);
        // .category.length
        
//          $.each(data.items, function(i,item){
//            $("<img/>").attr("src", item.media.m).appendTo("#images");
//            if ( i == 3 ) return false;
//          });
        
        });
}; 

// ===== если в форме добваления нов геооб выбрали другой класс ================

$("#fadd_kls_id").change(function(){
  
    var maskls = ['точечный объект', 'ломаная линия', 'контурный объект'];
       
    $.getJSON("_getkls.php?rid="+String($("#fadd_kls_id").val()),
        function(json){    
            // пришли  json.id, json.name, json.tip, json.icon, json.pattern
                                                      
            $("#fadd_name").val(String(json.name));
            $("#fadd_tip").val(String(json.tip));
            tempinfo="";            
            if((json.icon!="")&&(json.icon!="нет")){
                tempinfo=tempinfo+"<img src='../img/markers/"+json.icon+"'> &nbsp;";
            };
            tempinfo=tempinfo+maskls[json.tip]+"<br><br>";                                    
            $("#dinfokls").html(tempinfo);
            
            // подгружаем отдельно html для заполнения pattern 
            $("#fadd_tmp_pat").load("_getkls.php?rid="+String($("#fadd_kls_id").val())+"&pattern=only",function() {
                  tinyMCE.get('fadd_pattern').setContent($("#fadd_tmp_pat").html());
              }
            );
        
    });


});


    

            
          
});       

