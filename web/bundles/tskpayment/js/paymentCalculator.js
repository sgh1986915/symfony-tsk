$(document).ready(function() {
    $('.btnPaymentCalculator').each(function() {
        // add a random number via javascript to distinguish dialog divs
        var randomnumber=Math.floor(Math.random()*100000000)
        attachPaymentCalculatorDialog(this, this.href + '?rnd=' + randomnumber, randomnumber);
    });

});
    // I think I finally have a glimpse into the power of a closure!
    // div ends up being a closure here.  Taking this code and dumping
    // it straight into the above .each() will NOT work, must be in a
    // separate function to create the closure
    function attachPaymentCalculatorDialog(div, url, rnd)
    {
        $.ajax({
                url: url,
                cache: false,
                success: function(data) {
            $(div).next('.paycal').html(data);

            // Store summary div for this instance
            $.data(div, 'summary_div', $(div).siblings('.paycal_txt').children('a'));
            
            // Store textarea for this instance
            $.data(div, 'textarea', $(div).siblings('textarea'));

            try {
                var paydata = $(div).data('textarea').val();
                if (paydata) {
                    var pcData  = JSON.parse(paydata);
                    // Draw Payment Calculator Summary
                    $(div).data('summary_div').html(pcData.summary);
                }
            } catch (err) {
                console.log(err);
            }
            
            // Setup DOM data handlers
            $(div).next('.paycal').data('summary_div', $(div).siblings('.paycal_txt').children('a'));
            $(div).next('.paycal').data('textarea', $(div).siblings('textarea'));

            // Store dialog for this instance
            $.data(div, 'dialogx', 
                $(div).next('.paycal').dialog({
                    height: 'auto',
                    autoOpen: false,
                    width: 580,
                    modal: true,
                    resizable: true,
                    title: 'Payment Calculator',
                    zIndex: 9998,
                    open: function(event, ui) {
                        if (typeof pcData != 'undefined') {
                            paymentCalculator = new PaymentCalculator();
                            paymentCalculator.import(pcData);
                            payments = paymentCalculator.generatePayments();
                            drawPayments(payments, '#' + rnd + ' #payments');
                        }
                    }
                }));

            $(div).on('click', function(e) {
                $.data(this, 'dialogx').dialog('open');
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            try {
                var paydata = $(div).data('textarea').val();
                if (paydata) {
                    var pcData  = JSON.parse(paydata);
                    // Draw Payment Calculator Summary
                    $(div).data('summary_div').html(pcData.summary);
                    $(div).data('summary_div').on('click', function(e) {
                        // console.log($(this).parents('div').find('.btnPaymentCalculator').click());
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                }
            } catch (err) {
                console.log(err);
            }
        }});
    }
