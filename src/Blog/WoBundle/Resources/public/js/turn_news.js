function turn(url){
    $.post(url, function(data){
                  $(".left-news .news-content").remove();
                  $(".left-news").append(data); 
    });
}

function like(url,id){
        $.post(url, function(data){
            $('.'+id+'dislike').css('color','');
            $('.'+id+'like').css('color','red');
    });
}

function dislike(url,id){
        $.post(url, function(data){
            $('.'+id+'like').css('color','');
            $('.'+id+'dislike').css('color','black');
    });
}

