$('input[name="qty"]').on('change', function () {
    var qty = $('input[name="qty"]').val();
    var uP = $('input[name="unit_price"]').val();
    $('input[name="payment_total"]').val(qty * uP);
});
$('input[name="unit_price"]').on('change', function () {
    var qty = $('input[name="qty"]').val();
    var uP = $('input[name="unit_price"]').val();
    $('input[name="payment_total"]').val(qty * uP);
});

if ($('input[name="DynamicField"').length) {
    var neareastDiv = $('input[name="DynamicField"').closest('div');
    var btn = $('<input type="button" id="addPallets" name="addPallets" class="btn btn-warning addPallets" value="add Pallets">');
    $('input[name="DynamicField"').closest('div').append(btn);
    $('body').on('click', '.addPallets', function () {
        fetch('http://voyager.local/public/api/products')
            .then(response => {
                return response.json();
            })
            .then(json => {
                var products = json.data;
                $('input[name="DynamicField"').closest('div').append(callModal(products));
                $('#myModalScan').modal();
            });
    });
    console.log('wat');
}
function callModal(products){
    var modal =
        '<div class="modal fade divBody" id="myModalScan" role="dialog" data-replace="true" style="display: none;" aria-hidden="false">' +
        '    <div class="modal-dialog modal-lg">' +
        '        <div class="modal-content" id="prodUnitsContent">' +
        '            <div class="modal-header">' +
        '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>' +
        '                <h4 class="modal-title">Please add inbound line information:</h4>' +
        '            </div>' +
        '            <div class="modal-body">' +
        '                <div class="row">' +
        '                    <div class="form-group">' +
        '                        <div class="col-sm-6">' +
        '                            <label>Select a Model</label>' +
        '                            <select name="Model" id="selectModel" class="form-control inboundReceivingLine select2" required tabindex="9">' +
        '                                <option value="0" selected="selected">[- Please select a value -]</option>';
    $.each(products, function (i, v) {
        modal += ' <option value="' + v.id + '" palletsize="'+v.palletsize+'" cartonsize="'+v.cartonsize+'">' + v.model + '</option>';
    });
    modal += '                            </select>' +
        '                            <div class="help-block with-errors"></div>' +
        '                        </div>' +
        '                        <div class="form-group col-lg-6">' +
        '                            <label for="ReceiptQty">Receipt Quantity</label>' +
        '                            <div class="input-group">' +
        '                                <input type="text" name="ReceiptQty" value="0" class="form-control text-uppercase inboundReceivingLine" id="quantity" placeholder="Quantity" data-title="Please enter a valid Number" required  tabindex="11">' +
        '                            </div>' +
        '                            <div class="help-block with-errors"></div>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '                <div class="row">' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="SkidCount">Pallets <span class="label label-default stdPack">${Product_HashMap[]}</span><span class="calc"></span></label>' +
        '                                <div class="input-group">' +
        '                                    <input type="text" name="SkidCount" value="1" class="form-control text-uppercase qtyInput inboundReceivingLine" id="skid" placeholder="Number of Pallets" data-title="Please enter a valid Number" required  tabindex="12">' +
        '                                </div>' +
        '                            </div>' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="CartonCount">Cartons <span class="label label-default stdPack">${Product_HashMap[]}</span><span class="calc"></span></label>' +
        '                                <div class="input-group">' +
        '                                    <input type="text" name="CartonCount" value="0" class="form-control text-uppercase qtyInput inboundReceivingLine" id="carton" placeholder="Number of Cartons" data-title="Please enter a valid Number" required  tabindex="13">' +
        '                                </div>' +
        '                            </div>' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="Area">Location</label>' +
        '                                <div class="input-group">' +
        '                                    <input type="text" name="location" value="" class="form-control text-uppercase inboundReceivingLine" id="location" placeholder="location" data-title="Please enter a location" required tabindex="14">' +
        '                                </div>' +
        '                            </div>' +
        '                </div>' +
        '            <div class="row">' +
        '                <div class="form-group col-lg-12">' +
        '                    <label for="Comments">Comments</label>' +
        '                    <textarea id="comments" class="form-control inboundReceivingLine" name="Comments" rows="5" placeholder="Comments" tabindex="15"></textarea>' +
        '                </div>' +
        '            </div>' +
        '            </div>' +
        '            <div class="modal-footer">' +
        '                <button id="close" type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="icon fa fa-close"></i> Cancel</button>' +
        '                <button id="add" type="button" class="btn btn-info pull-right"><i class="icon fa fa-cubes"></i> Add pallet</button>' +
        '            </div>' +
        '        </div>' +
        '        <!-- /.modal-content -->' +
        '    </div>' +
        '</div>';
    modal = $(modal);
    return modal;
}
