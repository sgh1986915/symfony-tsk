acme = function(){
    var locale = '';
    return {
        initLocale: function() {
            if (global.locale) {
                locale = global.locale;
            } else {
                //Set a default locale if the user's one is not managed
                console.error('The locale is missing, default locale will be set (en_US)');
                locale = "en_US";
            }
        },

        getLocale: function(length) {
            if (length == 2) {
                return locale.split('_')[0];
            }
            return locale;
        },

        initDatePicker: function() {
            if($.datepicker.regional[this.getLocale(4)] != undefined ){
                $.datepicker.setDefaults( $.datepicker.regional[getLocale(4)] );
            }else if($.datepicker.regional[this.getLocale(2)] != undefined){
                $.datepicker.setDefaults( $.datepicker.regional[getLocale(2) ] );
            }else{
                $.datepicker.setDefaults( $.datepicker.regional['']);
            }

            $('.AcmeDatePicker').each(function(){
                var id_input=this.id.split('_datepicker')[0];
                var sfInput = $('#'+id_input)[0];
                if(! (sfInput)){
                    console.error('An error has occurred while creating the datepicker');
                }
                $(this).datepicker({
                    'yearRange':$(this).data('yearrange'),
                    'changeMonth':$(this).data('changemonth'),
                    'changeYear':$(this).data('changeyear'),
                    'altField' : '#'+id_input,
                    'altFormat' : 'yy-mm-dd',
                    'minDate' : null,
                    'maxDate': null
                });

                $(this).keyup(function(e) {
                    if(e.keyCode == 8 || e.keyCode == 46) {
                        $.datepicker._clearDate(this);
                        $('#'+id_input)[0].value = '';
                    }
                });
                var dateSf = $.datepicker.parseDate('yy-mm-dd',sfInput.value);

                $(this).datepicker('setDate',dateSf);
                $(this).show();
                $(sfInput).hide();
            });

        }
    }
}()
