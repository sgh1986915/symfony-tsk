$(document).ready(function() {
    // Read DOM to see if we have any data to pre-load

    $('#btnGenPayments').click(function() {
        // Empty payments div
        $('#payments').empty();

        var initialPayments = [];
        var strategyValue = $.isNumeric($('#strategy_value').val()) ? $('#strategy_value').val() : 0;
        if ($('#payment_strategy').val() == 'dp') {
            initialPayments[0] = strategyValue;
        } else if ($('#payment_strategy').val() == 'mp') {
            for (var i=0; i < $('#num_payments').val(); i++) {
                initialPayments.push(strategyValue);
            }
            initialPayments[0] = 0;
        }

        try {
            // compute and draw payments
            paymentCalculator = new PaymentCalculator({'principal': $('#principal').val(), 'numPayments': $('#num_payments').val(), 'initialPayments': initialPayments});
            payments = paymentCalculator.generatePayments();
            drawPayments(payments, '#payments');
        } catch (err) {
            alert(err);
        }
    
       return false;
    });

    $('#payment_strategy, #principal, #num_payments, #discount').change(function() {
        $('#btnGenPayments').click();
    });
    $('#btnClear').click(function() {
        // Empty payments div
        $('#payments').empty();
        return false;
    });

});

function drawPayments(payments, selector)
{
    for (i=0; i < payments.length; i++) {
        var output = Mustache.render($('#paymentWidget').html(), {'idx': i+1, 'payment': payments[i]});
        $(selector).append(output);
    }

    $(selector).append('<p><b>Total</b> ' + paymentCalculator.sumPayments() + '</p>');
    $(selector).append('<p><button name="btn" id="btnSave">Save Payments</button></p>')

    $('#btnSave').on('click', function(e) {
        console.log('Save clicked');
        e.preventDefault();
        e.stopPropagation();
        // Export data to DOM
        $('#paydata').data('pc', paymentCalculator.export());
        $('#paycal').dialog('close');
    });
}
