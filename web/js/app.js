$(document).ready(function() {
    $('a.browse_menu').click(function() {
        var letter = ($(this).html()) ? $(this).html() : 'A';
        $.get('http://dpi.obverse.com/browse/characters?startsWith='+letter+'&byType=digitalcomics&limit=500', function(data) {
            var template = '{{#data.results}}<li>{{id}} {{title}}</li>{{/data.results}}';
            var html = Mustache.to_html(template, data);
            $('#character-listing').html(html);
        });
    });
    $('a.browse_menu[href="#A"]').click();
});
