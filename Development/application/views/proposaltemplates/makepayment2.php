<?php
if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}
?>
<?php $this->load->view('proposaltemplates/includes/head'); ?>
<div class="note no-margin row">
    <div class="col-sm-6">
        <strong>
            <?php
            if (isset($preview) && $preview == "preview") { ?>
                <a class=""
                   href="<?php echo $proposal->status == "decline" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
                   onclick="self.close()">
                    <i class="fa fa-angle-left mright10"></i>
                    Preview mode
                </a>
            <?php } else {
                if (isset($token)) {
                    if (is_staff_logged_in()) {
                        $href = admin_url('projects/dashboard/' . $rel_id);
                    } else {
                        $href = "javascript:void(0)";
                    }
                } else {
                    $href = admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link;
                }
                ?>
                <a class="" href="<?php echo $href; ?>">
                    <?php
                    if (isset($token) && is_staff_logged_in()) {
                        echo "Project Dashboard";
                    } else {
                        echo $proposal->name;
                    } ?>
                </a>
            <?php } ?>
        </strong>
    </div>
    <div class="col-sm-6">
        <?php
        $download = "javascript:void(0)";
        $print = "javascript:void(0)";
        $email = "javascript:void(0)";
        if (is_staff_logged_in() && ((isset($_GET['pid']) && $_GET['pid'] > 0) || (isset($_GET['pid']) && $_GET['pid'] > 0))) {
            $email = admin_url('proposaltemplates/sentproposal/' . $proposal->templateid . $rel_link);
            if (isset($token)) {
                $email = "javascript:void(0)";
            }
            $download = "javascript:void(0)";
            $print = "javascript:void(0)";

        }
        if ($proposal->isclosed || $proposal->isarchieve) {
            $email = "javascript:void(0)";
        }
        ?>
        <div class="headicons text-right">
            <ul class="topiconmenu">
                <li class="inline-block mleft10">
                    <a href="<?php echo $download ?>">
                        <i class="fa fa-download"></i>
                    </a>
                </li>
                <li class="inline-block mleft10">
                    <a href="<?php echo $print ?>">
                        <i class="fa fa-print"></i>
                    </a>
                </li>
                <li class="inline-block mleft10">
                    <a href="<?php echo $email ?>">
                        <i class="fa fa-envelope-o"></i>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>
<div id="wrapper">
    <div class="content viewproposal proposal-template">
        <div class="row">
            <div class="col-md-12 widget-holder">
                <h1 class="pageTitleH1"><i class="fa fa-file-text-o "></i><?php echo ucfirst($title); ?></h1>
                <?php $this->load->view('proposaltemplates/proposal_bullets'); ?>
                <div class="widget-bg">
                    <div class="widget-body clearfix">
                        <div class="makepayment">
                            <?php
                            /**
                             * Created by PhpStorm.
                             * User: masud
                             * Date: 31-08-2018
                             * Time: 02:31 PM
                             */
                            foreach ($invoices as $key => $inv) {
                                if ($inv->status != 2) {
                                    $invoice = $invoices[$key];
                                    break;
                                }
                            }
                            if (isset($invoice) && !empty($invoice)) {

                                if ($invoice->project_id > 0) {
                                    $lid = $invoice->project_id;
                                    $type = "pid";
                                } elseif ($invoice->leadid > 0) {
                                    $lid = $invoice->leadid;
                                    $type = "lid";
                                }
                                $CI =& get_instance();
                                $CI->load->model('payments_model');
                                $CI->load->model('payment_modes_model');
                                $payments = $CI->payments_model->get_invoice_payments($invoice->id);
                                $payment_modes = $CI->payment_modes_model->get('', array(
                                    'expenses_only !=' => 1
                                ));
                                $this->load->view('proposaltemplates/psl_section_head', array('title' => "payment"));
                                ?>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#stripe">Stripe</a></li>
                                    <li><a data-toggle="tab" href="#check">Check</a></li>
                                    <li><a data-toggle="tab" href="#cash">Cash</a></li>
                                </ul>

                                <div class="tab-content">
                                    <?php $this->load->view('proposaltemplates/paymentmodes/stripe', array('invoice' => $invoice, 'lid' => $lid, 'type' => $type)); ?>
                                    <?php $this->load->view('proposaltemplates/paymentmodes/check', array('invoice' => $invoice, 'lid' => $lid, 'type' => $type)); ?>
                                    <?php $this->load->view('proposaltemplates/paymentmodes/cash', array('invoice' => $invoice, 'lid' => $lid, 'type' => $type)); ?>
                                </div>


                            <?php } ?>

                            <div class="proposal_actions text-center mtop35">
                                <div class="inline-block">
                                    <a class="btn btn-info"
                                       href="<?php echo $proposal->status == "draft" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
                                       onclick="self.close()">
                                        <i class="fa fa-reply" aria-hidden="true"></i>
                                        <?php echo _l('exit_proposal'); ?>
                                    </a>
                                </div>
                                <div class="inline-block">
                                    <a class="btn btn-primary proposal_step slickNext " data-tab="invoice"
                                       href="#<?php //echo site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash) ?>">
                                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                                        <?php echo _l('invoice'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /*============   ================*/ ?>

<?php $this->load->view('proposaltemplates/includes/scripts'); ?>
<script type="text/javascript">
    var stripePublishableKey = "<?php echo $stripePublishableKey;?>";
    var stripe = Stripe(stripePublishableKey);
    (function () {
        'use strict';

        var elements = stripe.elements({
            fonts: [
                {
                    cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
                },
            ],
            // Stripe's examples are localized to specific languages, but if
            // you wish to have Elements automatically detect your user's locale,
            // use `locale: 'auto'` instead.
            locale: window.__exampleLocale
        });

        // Floating labels
        /*var inputs = document.querySelectorAll('.cell.example.example2 .input');
        Array.prototype.forEach.call(inputs, function (input) {
            input.addEventListener('focus', function () {
                input.classList.add('focused');
            });
            input.addEventListener('blur', function () {
                input.classList.remove('focused');
            });
            input.addEventListener('keyup', function () {
                if (input.value.length === 0) {
                    input.classList.add('empty');
                } else {
                    input.classList.remove('empty');
                }
            });
        });*/

        var elementStyles = {
            base: {
                color: '#32325D',
                fontWeight: 500,
                fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
                fontSize: '16px',
                fontSmoothing: 'antialiased',

                '::placeholder': {
                    color: '#CFD7DF',
                },
                ':-webkit-autofill': {
                    color: '#e39f48',
                },
            },
            invalid: {
                color: '#E25950',

                '::placeholder': {
                    color: '#FFCCA5',
                },
            },
        };

        var elementClasses = {
            focus: 'focused',
            empty: 'empty',
            invalid: 'invalid',
        };

        var cardNumber = elements.create('cardNumber', {
            style: elementStyles,
            classes: elementClasses,
        });
        cardNumber.mount('#example2-card-number');

        var cardExpiry = elements.create('cardExpiry', {
            style: elementStyles,
            classes: elementClasses,
        });
        cardExpiry.mount('#example2-card-expiry');

        var cardCvc = elements.create('cardCvc', {
            style: elementStyles,
            classes: elementClasses,
        });
        cardCvc.mount('#example2-card-cvc');

        registerElements([cardNumber, cardExpiry, cardCvc], 'example2');
    })();


    function stripeHandler(token, elemnt) {
        ``
        // Insert the token ID into the form so it gets submitted to the server
        if (elemnt == "token") {
            var name = "stripeToken";
            token = token.id;
        } else if (elemnt == 'type') {
            var name = "stripeType";
            token = token.type;
        }
        var form = document.getElementById('record_payment_form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', name);
        hiddenInput.setAttribute('value', token);
        form.appendChild(hiddenInput);
    }

    function registerElements(elements, exampleName) {
        var formClass = '.' + exampleName;
        var example = document.querySelector(formClass);
        var form = example.querySelector('form');
        var resetButton = example.querySelector('a.reset');
        var error = form.querySelector('.error');
        var errorMessage = error.querySelector('.errormessage');

        function enableInputs() {
            Array.prototype.forEach.call(
                form.querySelectorAll(
                    "input[type='text'], input[type='email'], input[type='tel']"
                ),
                function (input) {
                    input.removeAttribute('disabled');
                }
            );
        }

        function disableInputs() {
            Array.prototype.forEach.call(
                form.querySelectorAll(
                    "input[type='text'], input[type='email'], input[type='tel']"
                ),
                function (input) {
                    input.setAttribute('disabled', 'true');
                }
            );
        }

        function triggerBrowserValidation() {
            // The only way to trigger HTML5 form validation UI is to fake a user submit
            // event.
            var submit = document.createElement('input');
            submit.type = 'submit';
            submit.style.display = 'none';
            form.appendChild(submit);
            submit.click();
            submit.remove();
        }

        // Listen for errors from each Element, and show error messages in the UI.
        var savedErrors = {};
        elements.forEach(function (element, idx) {
            element.on('change', function (event) {
                if (event.error) {
                    error.classList.add('visible');
                    savedErrors[idx] = event.error.message;
                    errorMessage.innerText = event.error.message;
                } else {
                    savedErrors[idx] = null;

                    // Loop over the saved errors and find the first one, if any.
                    var nextError = Object.keys(savedErrors)
                        .sort()
                        .reduce(function (maybeFoundError, key) {
                            return maybeFoundError || savedErrors[key];
                        }, null);

                    if (nextError) {
                        // Now that they've fixed the current error, show another one.
                        errorMessage.innerText = nextError;
                    } else {
                        // The user fixed the last error; no more errors.
                        error.classList.remove('visible');
                    }
                }
            });
        });

        // Listen on the form's 'submit' handler...
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Trigger HTML5 validation UI on the form if any of the inputs fail
            // validation.
            var plainInputsValid = true;
            Array.prototype.forEach.call(form.querySelectorAll('input'), function (
                input
            ) {
                if (input.checkValidity && !input.checkValidity()) {
                    plainInputsValid = false;
                    return;
                }
            });
            if (!plainInputsValid) {
                triggerBrowserValidation();
                return;
            }

            // Show a loading screen...
            //example.classList.add('submitting');

            // Disable all inputs.
            //disableInputs();

            // Gather additional customer data we may have collected in our form.
            var name = form.querySelector('#' + exampleName + '-name');
            var additionalData = {
                name: name ? name.value : undefined,
            };

            // Use Stripe.js to create a token. We only need to pass in one Element
            // from the Element group in order to create a token. We can also pass
            // in the additional customer data we collected in our form.
            stripe.createToken(elements[0], additionalData).then(function (result) {
                // Stop loading!
                example.classList.remove('submitting');

                if (result.token) {
                    // If we received a token, show the token ID.
                    /*example.querySelector('.token').innerText = result.token.id;
                    example.classList.add('submitted');*/
                    stripeHandler(result.token, 'token');
                    stripeHandler(result.token, 'type');
                    form.submit();
                } else {
                    // Otherwise, un-disable inputs.
                    enableInputs();
                }
            });
        });
    }
</script>
<script>
    var validator = $("#proposal-form").validate({
        rules: {name: {required: true}},
    });
    $(function () {
        _validate_form($('.proposal-form'), {name: 'required'});
        _validate_form($('#decline_form'), {reason: 'required'});
        /*$('.proposalsections').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            infinite: false,
            draggable: false,
            adaptiveHeight: true,
            asNavFor: '.proposal_bullets',
            centerMode: false,
        });*/
        $('.proposal_bullets').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            /*asNavFor: '.proposalsections',*/
            dots: false,
            centerMode: false,
            focusOnSelect: true,
            useTransform: false
        });
    });

    function closeCurrentTab() {
        var conf = confirm("Are you sure, you want to Exit Proposal?");
        if (conf == true) {
            window.close();
        }
    }
</script>
<style>
    .example .error {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: center;
        justify-content: center;
        position: absolute;
        width: 100%;
        bottom: -10px;
        margin-top: 20px;
        left: 0;
        padding: 0 15px;
        font-size: 13px !important;
        opacity: 0;
        transform: translateY(10px);
        transition-property: opacity, transform;
        transition-duration: 0.35s;
        transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .example .error.visible {
        opacity: 1;
        transform: none;
    }
</style>