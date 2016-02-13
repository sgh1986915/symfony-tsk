~function (scope) 
{ 
    function initPaymentCalculator(options)
    { 
        function PaymentCalculator(options) 
        {
            this.defaults = {
                principal: 0,
                discount: 0,
                credit: 0,
                prePayment: 0,
                numPayments: 12,
                initialPayments: [],
                paymentFrequeuncy: 'monthly',
                roundingPrecision: 1,
                roundingMethod: 'ceil',
                paymentGravity: 'top',
                maxDiscount: 0
            };

            this.settings = this.extend(this.defaults, options);
            this.openSlots = this.settings.numPayments;
            this.setInitialPayments(this.settings.initialPayments);

            if ((this.settings.discount > this.settings.maxDiscount) && (this.settings.maxDiscount > 0)) {
                throw "Discount must be less than " + this.settings.maxDiscount;
            }
        } 

        PaymentCalculator.prototype.extend = function(obj, extObj) {
            if (arguments.length > 2) {
                for (var a = 1; a < arguments.length; a++) {
                    extend(obj, arguments[a]);
                }
            } else {
                for (var i in extObj) {
                    obj[i] = extObj[i];
                }
            }
            return obj;
        };

        PaymentCalculator.prototype.import = function(obj) {
        };

        PaymentCalculator.prototype.export = function() {
            obj = {};
            obj.principal = this.settings.principal;
            obj.paymentFrequency = this.settings.paymentFrequeuncy;
            obj.payments = this.generatePayments();
            obj.summary = this.generateSummary();
            obj.discount = this.settings.discount;
            obj.credit = this.settings.credit;
            obj.prePayment = this.settings.prePayment;
            return obj;
        };

        PaymentCalculator.prototype.import = function(obj) {
            this.setPrincipal(obj.principal);
            this.setDiscount(isNaN(obj.discount) ? 0 : obj.discount);
            this.setCredit(isNaN(obj.credit) ? 0 : obj.credit);
            this.setInitialPayments(obj.payments);
            this.setNumPayments(obj.payments.length);
            this.setPaymentFrequency(obj.paymentFrequency);
            this.setPrePayment(isNaN(obj.prePayment) ? 0 : obj.prePayment);
        };

        PaymentCalculator.prototype.generateSummary = function() {
            var payments = this.generatePayments(), pjs = {}, paystrings = [];
            // Tally up individual payments
            for (var i=0; i < payments.length; i++) {
                pjs[payments[i]] = (typeof pjs[payments[i]] != 'undefined') ? pjs[payments[i]] + 1 : 1;
            }

            var output = this.settings.numPayments + ' payments totaling $' + this.sumPayments();

            if (this.settings.credit > 0) {
                output += ' ($' + this.settings.principal + ' less a credit of $' + this.settings.credit + ')';
            }

            if (this.settings.discount > 0) {
                output += ' w/ a $' + this.settings.discount + ' discount ';
            }

            for (var key in pjs) {
                paystrings.push(pjs[key] + ' @ $' + key);
            }
            output += ' (' + paystrings.join(', ') + ')';

            if (this.settings.prePayment > 0) {
                output += ' including a $' + this.settings.prePayment + ' pre-payment ';
            }

            return output;
        };

        PaymentCalculator.prototype.setPaymentFrequency = function(paymentFrequency) {
            if (typeof paymentFrequency == 'string') {
                switch(paymentFrequency) {
                    case 'MONTHLY':
                    case 'YEARLY':
                    case 'WEEKLY':
                    case 'monthly':
                    case 'yearly':
                    case 'weekly':
                        this.settings.paymentFrequency = paymentFrequency.toLowerCase();
                    break;
                }
            }
        };

        PaymentCalculator.prototype.setNumPayments = function(numPayments) {
            if (numPayments > 0) {
                this.settings.numPayments = numPayments;
                this.openSlots = numPayments;
            }
        };

        PaymentCalculator.prototype.setPrincipal = function(principal) {
            if (principal > 0) {
                this.settings.principal = principal;
            }
        };

        PaymentCalculator.prototype.setPrePayment = function(prePayment) {
            if (prePayment > 0) {
                this.settings.prePayment = prePayment;
            }
        };

        PaymentCalculator.prototype.setCredit = function(credit) {
            if (credit > 0) {
                this.settings.credit = credit;
            }
        };

        PaymentCalculator.prototype.setDiscount = function(discount) {
            if (discount > 0) {
                if ((discount < this.settings.maxDiscount) || (this.settings.maxDiscount == 0)) {
                    this.settings.discount = discount;
                } else {
                    throw "Discount must be less than " + this.settings.maxDiscount;
                }
            }
        };

        PaymentCalculator.prototype.setPaymentGravity = function(paymentGravity) {
            switch (paymentGravity.toLowerCase()) {
                case 'top':
                case 'bottom':
                    this.settings.paymentGravity = paymentGravity.toLowerCase();
                break;
            }
        };

        PaymentCalculator.prototype.setRoundingPrecision = function(roundingPrecision) {
            switch (roundingPrecision) {
                case 0.001:
                case 0.01:
                case 0.1:
                case 1:
                case 10:
                    this.settings.roundingPrecision = parseFloat(roundingPrecision);
                break;
            }
        };

        PaymentCalculator.prototype.setInitialPayments = function(initialPayments) {
            if (initialPayments instanceof Array) {
                for (var i=0; i < initialPayments.length; i++) {
                    initialPayments[i] = parseInt(initialPayments[i]);
                }
                this.settings.initialPayments = initialPayments;
                if (this.settings.numPayments <= this.settings.initialPayments.length) {
                    this.settings.numPayments = this.settings.initialPayments.length;
                }

                this.openSlots = 0;
                for (var i=0; i < this.settings.numPayments; i++) {
                    if (initialPayments[i] > 0) {
                    } else {
                        this.openSlots++;
                    }
                }

                if (!this.openSlots) {
                    // throw "There are no open slots available for payment calculation."
                }
            }
        };

        PaymentCalculator.prototype.zeroOutInitialPayments = function() {
            var ip = [];
            for (var i=0; i < this.settings.numPayments; i++) {
                ip[i] = 0;
            }
            this.settings.initialPayments = ip;
        };

        PaymentCalculator.prototype.initialPayments = function() {
            return this.settings.initialPayments;
        };

        PaymentCalculator.prototype.sumInitialPayments = function() {
            var total = 0;
            for (var num in this.settings.initialPayments) {
                total += this.settings.initialPayments[num];
            }
            return total;
        };

        PaymentCalculator.prototype.sumPayments = function() {
            var total = 0;
            for (var num in this.payments) {
                total += this.payments[num];
            }
            return this._round(total, 0.01);
        };

        PaymentCalculator.prototype._calculatePaymentAmount = function(method) {
            return this._round( (this.settings.principal - this.settings.prePayment - this.settings.discount - this.settings.credit - this.sumInitialPayments()) / this.openSlots );

        };

        PaymentCalculator.prototype.generatePayments = function() {
            this.payments = [];
            var payAmt = this._calculatePaymentAmount();
            
            for (var i=0; i < this.settings.numPayments; i++) {
                this.payments[i] = (typeof this.settings.initialPayments[i] !== 'undefined') && (this.settings.initialPayments[i] > 0) ? this.settings.initialPayments[i] : payAmt;
            }
            this._validatePayments();
            return this.payments;
        };

        PaymentCalculator.prototype.paymentsJson = function() {
            if (typeof JSON.stringify == 'function') {
                return JSON.stringify(this.generatePayments());
            }
        }

        PaymentCalculator.prototype._validatePayments = function() {
            if (this.sumPayments() > this.settings.principal - this.settings.prePayment - this.settings.discount - this.settings.credit) {
                // console.log('top');
                var adjustedPaymentIndex = (this.settings.paymentGravity == 'top') ? this._getMaxAdjustedPaymentIndex() : this._getMinAdjustedPaymentIndex();
                this.payments[adjustedPaymentIndex] -= this._round(this.sumPayments() - this.settings.principal + parseFloat(this.settings.prePayment) + parseFloat(this.settings.discount) + parseFloat(this.settings.credit), 0.01); // Always round this to nearest penny
            }
            if (this.sumPayments() < this.settings.principal - this.settings.prePayment - this.settings.discount - this.settings.credit) {
                // console.log('bottom');
                var adjustedPaymentIndex = (this.settings.paymentGravity == 'top') ? this._getMinAdjustedPaymentIndex() : this._getMaxAdjustedPaymentIndex();
                this.payments[adjustedPaymentIndex] += this._round(this.settings.principal - this.settings.prePayment - this.settings.discount - this.settings.credit - this.sumPayments(), 0.01); // Always round this to nearest penny
            }
            // verify that all payments are positive integers
            for (var i=0; i < this.settings.numPayments; i++) {
                if (this.payments[i] < 0) {
                    throw "This configuration results in negative payments which are not allowed.  Please make adjustments.";
                }
            }
        };

        PaymentCalculator.prototype._getMinAdjustedPaymentIndex = function() {
            for (var x=0; x < this.payments.length; x++) {
                if ((typeof this.settings.initialPayments[x] === 'undefined') || (this.settings.initialPayments[x] == 0)) {
                    return x;
                }
            }
        };

        PaymentCalculator.prototype._getMaxAdjustedPaymentIndex = function() {
            for (var x=this.payments.length - 1; x > -1; x--) {
                if ((typeof this.settings.initialPayments[x] === 'undefined') || (this.settings.initialPayments[x] == 0)) {
                    return x;
                }
            }
            return this.payments.length - 1;
        };

        PaymentCalculator.prototype._round = function(amount, precision) {
            if (typeof(precision) == 'undefined') {
                precision = this.settings.roundingPrecision;
            }
            return Math.round(amount / precision) * precision;
        };

        // or "var PaymentCalculator = {};" when publishing a module / 
        // initialize the module or function / 
        return PaymentCalculator; 

    }

    if (typeof exports == 'object' && exports) { 
        // CommonJS 
        module.exports = initPaymentCalculator(); 
    } else if (typeof define == 'function' && define.amd) { 
        // AMD 
        define(initPaymentCalculator); 
    } else { 
        // Browser 
        scope.PaymentCalculator = initPaymentCalculator(); 
    } 
}(this);
