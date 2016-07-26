function datepicker() {
    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        language: 'pt-BR',
        clearBtn: true
    });
}

function showTabError() {

    var $tab_content = $(".tab-content");

    $tab_content.find("div.tab-pane:has(div.has-error)").each(function (index, tab) {
        var id = $(tab).attr("id");
        $('a[href="#' + id + '"]').tab('show');
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

function installmentsInit() {

    var $collectionHolder;

    var $addInstallmentLink = $('<a href="#" class="btn default"><span class="fa fa-plus"></span> Adicionar Parcela</a>');
    var $newLinkLi = $('<div class="panel-footer"></div>').append('&nbsp;').append($addInstallmentLink);

    // Get the ul that holds the collection of installments
    $collectionHolder = $('div#installments');

    // add a delete link to all of the existing installment form li elements
    $collectionHolder.find('div.panel-body').each(function () {
        addInstallmentFormDeleteLink($(this));
    });

    // add the "add a installment" anchor and li to the installments ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addInstallmentLink.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new installment form (see next code block)
        addInstallmentForm($collectionHolder, $newLinkLi);
    });
}

var newDueDateAt = null;

function addInstallmentForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var totalCollectionHolder = $collectionHolder.find('div.panel-body').length;

    var htmlGenerateInstallments = '<div class="form-group col-md-12"><hr></div>';

    var $addMultiInstallmentLink = $('<a href="#" class="btn default col-md-2">Gerar Multiplas Parcelas</a>');

    if (totalCollectionHolder == 0) {

        $newLinkLi.append($addMultiInstallmentLink);

        htmlGenerateInstallments = '<div class="form-group col-md-6"><label class="control-label" for="qtd_installment">Quantidade Parcelas</label>';
        htmlGenerateInstallments += '<input name="qtd_installment" id="qtd_installment" class="form-control"></div>';
        htmlGenerateInstallments += '<div class="form-group col-md-6">&nbsp;</div>';
        htmlGenerateInstallments += '<div class="form-group col-md-12"><hr></div>';
    }

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    newForm = htmlGenerateInstallments + newForm;

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a installment" link li
    var $newFormLi = $('<div class="panel-body"></div>').append(newForm);
    $newLinkLi.before($newFormLi);

    if (totalCollectionHolder == 0) {
        var $amount = $('#bill_billInstallments_' + index + '_amount');
        var $dueDateAt = $('#bill_billInstallments_' + index + '_dueDateAt');
    }

    datepicker();

    addInstallmentFormDeleteLink($newFormLi, $collectionHolder, $addMultiInstallmentLink);

    $addMultiInstallmentLink.on('click', function (e) {

        e.preventDefault();

        var qtd_installment = $('#qtd_installment').val();

        var dueDatAtVal;

        if (qtd_installment > 0) {
            for (i = 1; i < qtd_installment; i++) {

                var current_index = addInstallmentForm($collectionHolder, $newLinkLi);

                if (newDueDateAt == null) {
                    dueDatAtVal = $dueDateAt.val();
                }
                else {
                    dueDatAtVal = newDueDateAt;
                }

                var dueDateAtSplit = dueDatAtVal.split('-');

                newDueDateAt = moment([dueDateAtSplit[2], parseInt(dueDateAtSplit[1]) - 1, dueDateAtSplit[0]]).add(1, 'months').format('DD-MM-YYYY');

                $('#bill_billInstallments_' + current_index + '_amount').val($amount.val());
                $('#bill_billInstallments_' + current_index + '_dueDateAt').val(newDueDateAt);
            }
        }
    });

    return index;
}

function addInstallmentFormDeleteLink($installmentFormLi, $collectionHolder, $addMultiInstallmentLink) {

    var $removeFormA = $('<div class="form-group col-md-6"><label class="control-label">&nbsp;</label><a href="#" class="btn btn-danger"><span class="fa fa-remove"></span> Remover Parcela</a></div>');
    $installmentFormLi.append($removeFormA);

    $removeFormA.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the installment form
        $installmentFormLi.remove();

        var totalCollectionHolder = $collectionHolder.find('div.panel-body').length;

        if (totalCollectionHolder == 0) {
            $addMultiInstallmentLink.remove();
        }

        var dueDateAtSplit = newDueDateAt.split('-');

        newDueDateAt = moment([dueDateAtSplit[2], parseInt(dueDateAtSplit[1]) - 1, dueDateAtSplit[0]]).subtract(1, 'months').format('DD-MM-YYYY');
    });
}
