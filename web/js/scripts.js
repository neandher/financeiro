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

    var $addInstallmentLink = $('<a href="#" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span> Adicionar Parcela</a>');
    var $newLinkLi = $('<div class="panel-footer"></div>').append($addInstallmentLink);

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

function addInstallmentForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a installment" link li
    var $newFormLi = $('<div class="panel-body"></div>').append(newForm);
    $newLinkLi.before($newFormLi);

    datepicker();

    addInstallmentFormDeleteLink($newFormLi);
}

function addInstallmentFormDeleteLink($installmentFormLi) {
    var $removeFormA = $('<div class="form-group col-md-6"><label class="control-label">&nbsp;</label><a href="#" class="btn btn-danger form-control"><span class="glyphicon glyphicon-remove"></span> Remover Parcela</a></div>');
    $installmentFormLi.append($removeFormA);

    $removeFormA.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the installment form
        $installmentFormLi.remove();
    });
}
