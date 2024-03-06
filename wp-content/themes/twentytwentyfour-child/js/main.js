window.onload = function () {
    jQuery(".b-submit input[type=submit]").click(function(e) {
        var date = jQuery('input[name=date]').val();
        var arr = date.split('-');
        var category = jQuery('input[name=category]').attr('data-term-slug');
        
        var url = '/wp-json/webrock-namespace/v1/posts?';
        if (date.length) {
            url += "date="+arr[2]+"."+arr[1]+"."+arr[0]+"&";
        }
        
        if (typeof category !== "undefined") {
            if (category.length) {
                url += "category="+category;    
            }
        }
        
        jQuery.ajax({
            type: "GET",
            url: url,
            //data: form.serialize(), // serializes the form's elements.
            success: function(data)
            {
                console.log(data);
                var count = data.length;
                jQuery('.b-page__grid').empty();
                if (count > 3) {
                    jQuery('.b-readmore').removeClass('hidden');
                    jQuery('.b-pagination').removeClass('hidden');
                } else {
                    jQuery('.b-readmore').addClass('hidden');
                    jQuery('.b-pagination').addClass('hidden');
                }
                if (count > 0) {
                    jQuery.each(data, function(index,value){
                        if (index < 3) {
                            jQuery('<div class="b-article" data-post-id="'+value.id+'"><div class="b-article__content"><div class="b-content"><img class="b-content__img" src="'+value.image+'"><div class="b-content__date">'+value.date+'</div><div class="b-content__category">'+value.category+'</div><div class="b-content__title">'+value.title+'</div><div class="b-content__desc">'+value.content+'</div><div class="b-content__author"><img src="https://webrockstudio.ru/wp-content/themes/twentytwentyfour-child/images/author.svg"><span>'+value.author+'</span></div></div></div></div>').appendTo('.b-page__grid');    
                        }
                    });    
                } else {
                    jQuery('<p>Ничего не найдено. Попробуйте изменить параметры фильтра.</p>').appendTo('.b-page__grid');  
                }
            }
        });
        e.preventDefault();
    });
    
    
    
    jQuery("input[name=category]").click(function(e) {
        jQuery(".b-categorylist ul.has-parent").each(function( index ) {
            if (!jQuery(this).find(".has-children").length) {
                jQuery(this).removeClass('has-parent');
            }
        });
        
        if (jQuery('.b-categorylist').hasClass('hidden')) {
            jQuery('.b-categorylist').removeClass('hidden');
        } else {
            jQuery('.b-categorylist').addClass('hidden');
        }
    });
    
    jQuery(".b-categorylist li").click(function(e) {
        var term_id = jQuery(this).attr('data-term-id');
        var term_slug = jQuery(this).attr('data-term-slug');
        var term_name = jQuery(this).attr('data-term-name');
        jQuery('input[name=category]').val(term_name);
        jQuery('input[name=category]').attr('data-term-id', term_id);
        jQuery('input[name=category]').attr('data-term-slug', term_slug);
        jQuery('.b-categorylist').addClass('hidden');
    });
    
    
    
    
    
    
}