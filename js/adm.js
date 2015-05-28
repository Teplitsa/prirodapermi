function gebi(a){
    return document.getElementById(a);
}

$(document).ready(function() {

  
  





// включаем и выключаем видимость формы добавления нового элемента классификатора
$("#h2_add_kls").click(function(){    
        if(gebi('d_add').style.display=='none'){gebi('d_add').style.display='block';}else {gebi('d_add').style.display='none';}  
});


// включаем и выключаем видимость формы фильтра/поиска геообъектов
$("#h2_filter_geoob").click(function(){    
        if(gebi('d_filter').style.display=='none'){gebi('d_filter').style.display='block';}else {gebi('d_filter').style.display='none';}  
}); 	

// включаем и выключаем видимость формы добавления геообъекта
$("#h2_add_geoob").click(function(){    
        if(gebi('d_add').style.display=='none'){gebi('d_add').style.display='block';}else {gebi('d_add').style.display='none';}  
}); 	


// включаем и выключаем видимость формы добавления типа события
$("#h2_add_tevent").click(function(){    
        if(gebi('d_add').style.display=='none'){gebi('d_add').style.display='block';}else {gebi('d_add').style.display='none';}  
}); 	




// включаем и выключаем видимость формы фильтра событий
$("#h2_filter_event").click(function(){    
        if(gebi('d_filter').style.display=='none'){gebi('d_filter').style.display='block';}else {gebi('d_filter').style.display='none';}  
}); 	


// включаем и выключаем видимость формы добавления события
$("#h2_add_event").click(function(){    
        if(gebi('d_add').style.display=='none'){gebi('d_add').style.display='block';}else {gebi('d_add').style.display='none';}  
}); 	



});
