 // ================= наведение / съезжание мыши с объекта - включаем подсветку
 
 function f_event_over(ob){
      $(ob).addClass('event_over');
 };
 function f_event_out(ob){
      $(ob).removeClass('event_over');
 };
 
         
// ================= показ спецификации экземпляря геообъекта(запланировано)
function f_show_geoobject(num){
  
  alert('Показ страницы спецификации еще не работает');

}

// ================= показ содержимого подстраницы при ее выборе 
function f_punkt_show(num){

  $('.punkt_podmenu').removeClass("vibrano");
  $('#punkt_podmenu_'+String(num)).addClass("vibrano");
    
  $.getJSON("_getpage.php?rid="+String(num),
        function(json){                                                                
             $('#d_info_cont h1').html(json.full_name);
             $('#d_info_cont .d_page').html(json.page);
             $('#d_info_cont .d_page_add').html(json.page_add);                             
      });        
}
       
 
 
// ================================ кликнули по конкретному событию ============= 
 function f_event_click(num){          
    
       $.getJSON("_geteventinfo.php?rid="+String(num),
        function(json){                                            
                     
             $('#d_infoevent .h').html(json.name);
             $('#d_infoevent .dataperiod').html(json.period);
             $('#d_infoevent .type_name').html(json.type_name);
             $('#d_infoevent .content').html(json.page);
             
             // назначаем кнопке код объекта kls для показа на карте в атрибуте kls_id
             $('#d_infoevent .ukazatel .btn_na_karte').attr("kls_id", String(json.kls_id));
                                                                   
            if(String(json.kls_id)=="0"){          
                $('#d_infoevent .ukazatel .kls_name').html('нету');
                $('#d_infoevent .ukazatel').hide();            
            }else{            
                $('#d_infoevent .ukazatel .kls_name').html(json.kls_name);
                $('#d_infoevent .ukazatel').show();
            }
            
            $('#d_infoevent').animate({opacity: 1, top:120, left: 220},500);
          
      });        
 };


$(document).ready(function() {

// ============= активируем новый календарь ======================
// http://forum.vingrad.ru/topic-272078.html
// http://easywebscripts.net/jquery/datepicker.php
// http://slyweb.ru/jquerydoc/datepicker-options.php
// http://jquery.page2page.ru/index.php5/%D0%9A%D0%B0%D0%BB%D0%B5%D0%BD%D0%B4%D0%B0%D1%80%D1%8C_UI
// http://forum.jquery.com/topic/datepicker-formatdate-can-t-handle-predefined-standard-date-formats





$(function() {
	$('#inlineDatepicker').datepick({onSelect: showDate});  
});




 function showDate(date) {

  // дата придет в таком жутком формате
 	//     Fri Mar 27 2015 03:00:16 GMT+0500 (Уральское время (зима))
  // фиксируем ее в объекте типа дата
  
  var now = new Date(date);
  
  // и расчленяем ее на составляющие
 	
	$('#calendar_all').val($.datepick.formatDate("dd.mm.yyyy", now));
	$('#calendar_d').val($.datepick.formatDate("dd", now));
	$('#calendar_m').val($.datepick.formatDate("mm", now));
	$('#calendar_y').val($.datepick.formatDate("yyyy", now));

  temp_c_all=$.datepick.formatDate("dd.mm.yyyy", now);
  temp_c_d=$.datepick.formatDate("dd", now);
  temp_c_m=$.datepick.formatDate("mm", now);
  temp_c_y=$.datepick.formatDate("yyyy", now);
  
  temp_str="_getevents.php?calendar_d="+String(temp_c_d)+"&calendar_m="+String(temp_c_m)+"&calendar_y="+String(temp_c_y);
  // alert(temp_str);  

  $.getJSON(temp_str,
        function(json){                        
        
          $('#d_event_list').hide(200);
          temp_ev_na_datu=json.na_datu;
          temp_ev_kolvo=json.kolvo;
          
          str_res="на "+String(temp_c_all) +" событий ";
          if(temp_ev_kolvo==0){str_res=str_res+"нет";}else{str_res=str_res+String(temp_ev_kolvo);}                    
    
          for(a in json.contents){
        
            // читаем в цикле данные о событиях 

            temp_id=json.contents[a].id;
            temp_name=json.contents[a].name;
            temp_anons=json.contents[a].anons;
            temp_period=json.contents[a].period;
                                                       
            str_res=str_res+"<div class='fevent' OnMouseOver='f_event_over(this);' OnMouseOut='f_event_out(this);' OnClick='f_event_click("+String(temp_id)+");'>";
            str_res=str_res+    String(temp_name)+"<br><span class='dataperiod'>"+String(temp_period)+"</span><br>"+String(temp_anons);
            str_res=str_res+"</div>";                        
            
          }
          $('#d_event_list').html(str_res);
          $('#d_event_list').show(200);
  });



 }


// при первой загрузке приложения включаем показ списка событий за текущую дату
  var now = new Date();
  showDate(now);




page=0;
//$('div.d_prm').first().addClass("amenu");



// =====!!!!====== выбор пункта верхнего меню =================
// код id страницы находится в атрибуте page текущего объекта
$('div.d_prm').click(function(){
  
  var temp_page=parseInt($(this).attr("page"));
  
  $('div.d_prm').removeClass("amenu");
  $(this).addClass("amenu");
  
  // загружаем содержимое страницы 
  
  $.getJSON("_getpage.php?rid="+String(temp_page),
        function(json){                                            
                     
             $('#d_info_cont h1').html(json.full_name);
             $('#d_info_cont .d_page').html(json.page);
             $('#d_info_cont .d_page_add').html(json.page_add);
             
             str_res="";
             
             for(a in json.contents){      
                  
                  // читаем в цикле данные о подстраницах 
                  temp_id=json.contents[a].id;
                  temp_name=json.contents[a].name;
                                                       
                                                       
                // OnMouseOver='f_event_over(this);' OnMouseOut='f_event_out(this);'                                                        
                  str_res=str_res+"<span class='punkt_podmenu' id='punkt_podmenu_"+String(temp_id)+"' OnClick='f_punkt_show("+String(temp_id)+");'>";
                  str_res=str_res+    String(temp_name);
                  str_res=str_res+"</span> &nbsp;&nbsp;";                                    
          }
          
         // alert(str_res);
            $('#d_info_cont .d_podmenu').html(str_res);
            
             f_info_on();
          
      });      
        
        

  

 
});


// первичный выбор / активация вкладки правого меню (при загрузке страницы)

              $('div.d_vkladka').first().addClass("amenu");
              $('#d_vkladka_podpis').html($('div.d_vkladka').first().attr("nazvanie"));
              
              vkl=$('div.d_vkladka').first().attr("vkl");              
              
              $('.d_vkladka_cnt').hide();  
              $('#d_cnt_'+String(vkl)).addClass("acontent");
              $('#d_cnt_'+String(vkl)).show();


                // =========== выбор правого меню
                $('div.d_vkladka').click(function(){                
                  
                  vkl=parseInt($(this).attr("vkl"));                                    
                  $('div.d_vkladka').removeClass("amenu");
                  $(this).addClass("amenu");              
                  $('#d_vkladka_podpis').html($(this).attr("nazvanie"));
                  
                  $('.d_vkladka_cnt').hide();  
                  $('#d_cnt_'+String(vkl)).addClass("acontent");
                  $('#d_cnt_'+String(vkl)).show();  

                
                });
                
                $(".d_gr").hover(
                    function() { $(this).addClass('mover');},
                    function() { $(this).removeClass('mover');      
                });

                $(".d_gr").click(function(){
                  //alert(this.id);
                  if($('#d_pod_'+String(this.id)).css('display')=="none"){
                        $('#d_pod_'+String(this.id)).show(100);
                  }else{
                        $('#d_pod_'+String(this.id)).hide(100);
                    }          
                });





$('#span_open').click(function(){
  f_info_on();
});


// ==== режим подсветки элементов и пунктов
$(".dend").hover(
      function() { $(this).addClass('mover');},
      function() { $(this).removeClass('mover');      
});



$("#d_info_close").click(function(){
    f_info_off();
});





$("#d_infoevent_close").click(function(){
    f_infoevent_off();
});


$("#d_infokls_close").click(function(){
    f_infokls_off();
});





// ==== режим подсветки кнопки на карте
$(".btn_na_karte").hover(
      function() { $(this).css('border','1px solid red');},
      function() { $(this).css('border','1px solid transparent');      
});




// ==== информационный блок

function f_info_on(){
      $('#d_infopage').animate({top:70, left: 350},500);
}

function f_info_off(){
        $('#d_infopage').animate({top:-1000},500);
        $('div.d_prm').removeClass("amenu"); 	      
}

// ==== управление окном описания класса ==================================================================

function f_infokls_on(){
      $('#d_infokls').animate({top:70, left: 350},500);
}

function f_infokls_off(){
        $('#d_infokls').animate({left:-1000},500);         	      
}





// ==== управление окном описания события =========================================================
function f_infoevent_on(){
      $('#d_infoevent').animate({opacity: 1, top:120, left: 220},1000);
}

function f_infoevent_off(){
        $('#d_infoevent').animate({opacity: 1, top:120, left: -700},1000); 	      
}



	 	
    





    



});





// =====================================================

