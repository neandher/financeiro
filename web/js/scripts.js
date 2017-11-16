function datepicker() {
    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        language: 'pt-BR',
        clearBtn: true,
        autoclose: true
    });
}

function tooltip() {
    $('.js-tooltip').tooltip();
}

function showTabError() {

    var $tab_content = $(".tab-content");

    $tab_content.find("div.tab-pane:has(div.has-error)").each(function (index, tab) {

        var id = $(tab).attr("id");
        $('a[href="#' + id + '"]').tab('show');

        $(tab).find('div.has-error').each(function (_index, _field) {
            $('html, body').animate({
                scrollTop: $(_field).offset().top
            }, 2000);
            return false;
        });

        return false;
    });
}

$('#bill_billCategory').change(function () {

    var billPlan = $('#bill_billPlan');

    if ($(this).val().length > 0) {

        billPlan.html($("<option></option>").attr("value", 0).text('carregando...'));

        $.getJSON(Routing.generate("bill_plan_list_form_json", {"bill_category_id": $(this).val()}), function (data) {

            billPlan.children().remove();

            billPlan.html($("<option></option>"));

            $.each(data, function (key, value) {
                billPlan.append($("<option></option>")
                    .attr("value", value.id)
                    .text(value.billPlanCategory.description + ' - ' + value.description));
            });
        })
    }
    else {
        billPlan.html($("<option></option>"));
    }
});

$('.cashFlowAjaxBills').click(function () {

    $('#showAjaxBillsModal').modal();

    var $modalBody = $('.showAjaxBillsModalBody');

    $modalBody.html('<tr><td colspan="5">loading...</td><tr>');

    $.getJSON(Routing.generate("bill_list_ajax"),
        {
            billCategory: $(this).data('billCategory'),
            billPlan: $(this).data('billPlan'),
            billStatus: $(this).data('billStatus'),
            billMonth: $(this).data('billMonth'),
            billYear: $(this).data('billYear'),
            billBank: $(this).data('billBank')
        },
        function (data) {

            $modalBody.html('');

            $.each(data, function (key, value) {

                $.each(value.billInstallments, function (_key, _value) {

                    var dueDateAt = moment(_value.dueDateAt.date).format('DD/MM/YYYY');
                    var paymentDateAt = _value.paymentDateAt != null ? moment(_value.paymentDateAt.date).format('DD/MM/YYYY') : '-';
                    var amountPaid = _value.amountPaid != null ? _value.amountPaid : '-';
                    var amount = _value.amount;

                    $modalBody.append('<tr><td>' + value.description + '</td><td>' + dueDateAt + '</td><td>' + amount + '</td><td>' + paymentDateAt + '</td><td>' + amountPaid + '</td></tr>');
                });
            });
        })
});

function installmentsInit() {

    var $collectionHolder;

    var $addInstallmentLink = $('#btn_add_installment');
    var $newLinkPanel = $('#panel_add_installment');
    var $generateInstallments = $('#installments_generate');

    $collectionHolder = $('div#installments');

    $collectionHolder.find('div.panel-body').each(function () {
        addInstallmentFormDeleteLink($(this));
    });

    var index = $collectionHolder.find('div.panel-default').length;

    $collectionHolder.data('index', index);

    if (index == 0) {
        addInstallmentForm($collectionHolder, $newLinkPanel);
    }

    $addInstallmentLink.on('click', function (e) {
        e.preventDefault();
        addInstallmentForm($collectionHolder, $newLinkPanel);
    });

    $generateInstallments.on('click', function (e) {
        e.preventDefault();
        installmentsGenerate($collectionHolder, $newLinkPanel);
    });
}

function addInstallmentForm($collectionHolder, $newLinkPanel) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    var new_index = index + 1;

    $collectionHolder.data('index', new_index);

    var $newFormPanelBody = $('<div class="panel-body"></div>').append(newForm);
    var $newFormPanel = $('<div class="panel panel-default"></div>').append($newFormPanelBody);
    $newLinkPanel.before($newFormPanel);

    datepicker();

    addInstallmentFormDeleteLink($newFormPanelBody, $newFormPanel);

    return new_index;
}

function addInstallmentFormDeleteLink($newFormPanelBody, $newFormPanel) {
    var $removeForm = $('<div class="col-md-6"><div class="form-group"><a href="#" class="btn red btn-outline form-control"><span class="fa fa-remove"></span> Remover Parcela</a></div></div>');
    $newFormPanelBody.append($removeForm);

    if ($newFormPanel == null) {
        $newFormPanel = $newFormPanelBody.parent('.panel');
    }

    var $collectionHolder = $('div#installments');

    $removeForm.on('click', function (e) {
        e.preventDefault();
        $newFormPanel.remove();

        var index = $collectionHolder.find('div.panel-default').length;

        $collectionHolder.removeData('index');
        $collectionHolder.data('index', index);
    });
}

function installmentsGenerate($collectionHolder, $newLinkPanel) {

    var $number = $('#bill_generate_installments_number');
    var $dueDateAt = $('#bill_generate_installments_dueDateAt');
    var $amount = $('#bill_generate_installments_amount');

    var newDueDateAt = null;
    var dueDateAtVal;

    var index = $collectionHolder.find('div.panel-default').length;

    $collectionHolder.removeData('index');
    $collectionHolder.data('index', index);

    if ($number.val() > 0) {
        for (i = 0; i < $number.val(); i++) {

            var current_index = (parseInt(addInstallmentForm($collectionHolder, $newLinkPanel)) - 1);

            if (newDueDateAt == null) {
                dueDateAtVal = $dueDateAt.val();
            }
            else {
                dueDateAtVal = newDueDateAt;
            }

            var dueDateAtSplit = dueDateAtVal.split('-');

            if (i > 0) {
                newDueDateAt = moment([dueDateAtSplit[2], parseInt(dueDateAtSplit[1]) - 1, dueDateAtSplit[0]]).add(1, 'months').format('DD-MM-YYYY');
            }
            else {
                newDueDateAt = moment([dueDateAtSplit[2], parseInt(dueDateAtSplit[1]) - 1, dueDateAtSplit[0]]).format('DD-MM-YYYY');
            }

            $('#bill_billInstallments_' + current_index + '_amount').val($amount.val());
            $('#bill_billInstallments_' + current_index + '_dueDateAt').val(newDueDateAt);
        }
    }
}

function filesInit() {

    var $collectionHolder;

    var $addFileLink = $('#btn_add_file');
    var $newLinkPanel = $('#panel_add_file');

    $collectionHolder = $('div#prototype_files');

    $collectionHolder.find('div.panel-body').each(function () {
        addFileFormDeleteLink($(this));
    });

    var index = $collectionHolder.find('div.panel-default').length;

    $collectionHolder.data('index', index);

    $addFileLink.on('click', function (e) {
        e.preventDefault();
        addFileForm($collectionHolder, $newLinkPanel);
    });
}

function addFileForm($collectionHolder, $newLinkPanel) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    var new_index = index + 1;

    $collectionHolder.data('index', new_index);

    var $newFormPanelBody = $('<div class="panel-body"></div>').append(newForm);
    var $newFormPanel = $('<div class="panel panel-default"></div>').append($newFormPanelBody);
    $newLinkPanel.before($newFormPanel);

    addFileFormDeleteLink($newFormPanelBody, $newFormPanel);

    return new_index;
}

function addFileFormDeleteLink($newFormPanelBody, $newFormPanel) {
    var $removeForm = $('<div class="col-md-2"><div class="form-group"><label class="control-label">&nbsp;</label><a href="#" class="btn red btn-outline form-control"><span class="fa fa-remove"></span></a></div></div>');
    $newFormPanelBody.append($removeForm);

    if ($newFormPanel == null) {
        $newFormPanel = $newFormPanelBody.parent('.panel');
    }

    var $collectionHolder = $('div#prototype_files');

    $removeForm.on('click', function (e) {
        e.preventDefault();
        $newFormPanel.remove();

        var index = $collectionHolder.find('div.panel-default').length;

        $collectionHolder.removeData('index');
        $collectionHolder.data('index', index);
    });
}