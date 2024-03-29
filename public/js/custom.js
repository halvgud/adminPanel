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

if ($('input[name="contract"').length) {
    $('input[name="quantity"]').on('change', function () {
        var qty = $('input[name="quantity"]').val();
        var uP = $('input[name="unit_cost"]').val();
        $('input[name="total_contract_cost"]').val(qty * uP);
    });
    $('input[name="unit_cost"]').on('change', function () {
        var qty = $('input[name="quantity"]').val();
        var uP = $('input[name="unit_cost"]').val();
        $('input[name="total_contract_cost"]').val(qty * uP);
    });

}
var modal = "";
//inboundReceiving
if ($('input[name="DynamicField"').length) {
    var nearestDiv = $('input[name="DynamicField"').closest('div');
    var type = $('#standardData').length;
    if (type) {
        type = 'edit';
    }
    console.log(type);
    $(nearestDiv).removeClass('hidden');
    var btn = $('<input type="button" id="addPallets" name="addPallets" class="btn btn-warning addPallets" value="add Pallets">');
    $('input[name="DynamicField"').closest('div').append(btn);
    $('body').on('click', '.addPallets', function () {
        appendModal(function(){},'DynamicField');
    });
    var InboundLines = {};
    $(document).on('change', '.selectModel', function () {
        var palletsize = $('option:selected', this).attr('palletsize');
        var cartonsize = $('option:selected', this).attr('cartonsize');
        $('.stdPackPallet').text(palletsize);
        $('.unitsincarton').val(cartonsize);
        $('.cartonsinpallet').val(palletsize);
        $('.stdPackCarton').text(cartonsize);
    });

    var i = 0;
    var lineId = "";
    var editLineId = "";
    var sumRecQty = 0;
    var sumQty = 0;
    $(document).on('click', '.add', function () {
        if ($("#selectModel").val() === "0") {
            toastr.warning("Select a model");
        } else if ($("#palletsscc").val() === "") {
            toastr.warning("pallet number it's required");
        } else if ($("#cartonsinpallet").val() === "") {
            toastr.warning("cartons in pallet value it's required");
        } else if ($("#unitsincarton").val() === "") {
            toastr.warning("units in carton value it's required");
        } else if ($("#location").val() === "") {
            toastr.warning("location value it's required");
        } else {
            if (editLineId.length == 0) {
                var d = new Date();
                lineId = d.getFullYear() + d.getTime() + i;
            } else {
                lineId = editLineId;
                editLineId = "";
                //$('#' + lineId + '').remove();
            }
            $('#myModalScan').modal('hide');
            var inboundLinesArray = $(".inboundLines").not('div');
            var pallet = {};
            inboundLinesArray.each(function () {
                var name = $(this).attr('name');
                var value;
                if ($(this).hasClass("select2") && name !== "UOM") {
                    value = $(this).find("option:selected").text();
                    pallet['product_id'] = $(this).val();
                    pallet['model'] = value;
                } else {
                    value = $(this).val().trim();
                    pallet[name] = value;
                }
                if (name === "unitsincarton") {
                    sumRecQty = parseInt(sumRecQty) + parseInt(value);
                }
                if (name === "cartonsinpallet") {
                    sumQty = parseInt(sumQty) + parseInt(value);
                }
            });
            InboundLines[lineId] = pallet;
            $thead = $('.headers > tr:first');
            $tbody = $('#standardData');
            $tfoot = $('#footers > tr:first').removeClass("hide");
            var trows = [];
            $('#standardData > tr').each(function () {
                var id = $(this).attr('id');
                trows.push(id);
            });
            var rowCount = $('.headers tr').length;
            if ($thead[0].children.length == 0) {
                $thead.append('<th></th>');
                $thead.append('<th></th>');
                $.each(InboundLines, function (lineId, groupInfo) {
                    $.each(groupInfo, function (name, value) {
                        if (name != "product_id") {
                            $thead.append('<th title=' + name + '>' + name + '</th>');
                        }
                    });
                });
            }
            $tfoot.find('#sumQty').empty().text(sumQty);
            $tfoot.find('#sumRecQty').empty().text(sumRecQty);
            $.each(InboundLines, function (lineId, groupInfo) {
                //If it does not exist, creat a new one, otherwise update selected row
                if (trows.indexOf(lineId) === -1) {
                    var td = '';
                    td = '<tr id=\"' + lineId + '\"><td><button id=\"delete\" type=\"button\" class=\"btn btn-danger delete\" title=\"Delete\"><i class=\"fa fa-close\">Delete</i></button></td>';
                    td = td + '<td><button id=\"edit\" type=\"button\" class=\"btn btn-info edit\" title=\"Edit\"><i class=\"fa fa-edit\"></i>Edit</button></td>';
                    $.each(groupInfo, function (name, value) {
                        if (name === "Quantity") {
                            td = td + '<td id=\"addQuantity\">' + value + '</td>';
                        } else if (name === "ReceiptQty") {
                            td = td + '<td id=\"addReceiptQty\">' + value + '</td>';
                        } else if (name == 'model') {
                            td = td + '<td title="' + name + '" product_id="' + groupInfo['product_id'] + '">' + value + '</td>';
                        } else if (name == 'product_id') { } else {
                            td = td + '<td title="' + name + '">' + value + '</td>';
                        }
                    });
                    td = td + '</tr>';
                    $tbody.append(td);
                } else {
                    $trow = $('#' + lineId + '');
                    $trow.empty();
                    var td = '<td><button id=\"delete\" type=\"button\" class=\"btn btn-danger delete\" title=\"Delete\"><i class=\"fa fa-close\"></i>Delete</button></td>';
                    td = td + '<td><button id=\"edit\" type=\"button\" class=\"btn btn-info edit\" title=\"Edit\"><i class=\"fa fa-edit\"></i>Edit </button></td>';
                    $.each(groupInfo, function (name, value) {
                        if (name === "Quantity") {
                            td = td + '<td id=\"addQuantity\">' + value + '</td>';
                        } else if (name === "ReceiptQty") {
                            td = td + '<td id=\"addReceiptQty\">' + value + '</td>';
                        } else if (name == 'model') {
                            td = td + '<td title="' + name + '" product_id="' + groupInfo['product_id'] + '">' + value + '</td>';
                        } else if (name == 'product_id') { } else {
                            td = td + '<td title="' + name + '">' + value + '</td>';
                        }
                    });
                    $trow.append(td);
                }
            });
        }
    });
    $(document).on("hidden.bs.modal", function () {
        $('#locationBin').empty();
        var inputs = $(this).find('.inboundLines');
        inputs.each(function () {
            $(this).val('');
        });
    });
    $(document).on('click', '.edit', function () {
        var id = $(this).closest('tr').attr('id');
        var palletInfo = InboundLines[id];
        var myRows = {};
        var headers = $("th").map(function () {
            return $(this).attr("title");
        }).get();
        var index2;
        var obj = {};
        var $rows = $(this).closest('tr').each(function (index) {
            $cells = $(this).find("td");
            index2 = $(this).attr('id');
            myRows[index2] = {};
            $cells.each(function (cellIndex) {
                var title = $(this).attr('title');
                if (headers.includes(title)) {
                    if (title == 'id') {
                        obj[title] = index2;
                    } else if (title == 'model') {
                        obj[title] = $(this).html();
                        obj['product_id'] = $(this).attr('product_id');
                    } else {
                        obj[title] = $(this).html();
                    }

                }
            });
            //myRows[index2] = obj;
        });
        appendModal(function () {
            console.log('callback');
            for (var name in obj) {
                $("input[name='" + name + "']").val(obj[name]);
                $("textarea[name='" + name + "']").val(obj[name]);
                console.log(name);
                if (name =='product_id'){
                    $("select[name='" + 'selectModel' + "']").val(obj[name]).trigger('change');
                }
                
            }
            editLineId = id;
        },'DynamicField','edit');
    });
    $(document).on('click', '.delete', function () {
        var id = $(this).closest('tr').attr('id');
        delete InboundLines[id];
        $(this).closest('tr').remove();
    });
    $(document).submit(function myFormSubmitCallback(event) {
        var myRows = {};
        var headers = $("th").map(function () {
            return $(this).attr("title");
        }).get();
        var $rows = $("tbody tr").each(function (index) {
            $cells = $(this).find("td");
            var index2 = $(this).attr('id');
            myRows[index2] = {};
            var obj = {};
            $cells.each(function (cellIndex) {
                var title = $(this).attr('title');
                if (headers.includes(title)) {
                    if (title == 'id') {
                        obj[title] = index2;
                    } else if (title == 'model') {
                        obj[title] = $(this).html();
                        obj['product_id'] = $(this).attr('product_id');
                    } else {
                        obj[title] = $(this).html();
                    }

                }
            });
            myRows[index2] = obj;
        });

        var myObj = {};
        myObj.myrows = myRows;
        var infoJSON = {};
        infoJSON['InboundLinesInfo'] = myObj.myrows;
        console.log(infoJSON);
        console.log(!jQuery.isEmptyObject(infoJSON['InboundLinesInfo']));
        if (!jQuery.isEmptyObject(infoJSON['InboundLinesInfo'])) {
            $('input[name="DynamicField"]').val(JSON.stringify(infoJSON));
        } else {
            event.preventDefault();
            toastr.warning("Pallet line it's required");
        }

    });
}
function appendModal(callback,name,type) {
    Swal.showLoading();
    //fetch('https://dtmwarehouselogistics.com/public/api/products') 
    fetch('http://voyager.local/public/api/products')  //http://voyager.local/public/api/products
        .then(response => {
            return response.json();
        })
        .then(json => {
            var products = json.data;
            
            if (modal.length == 0) {
                modal = callModal(products);
                $('input[name="' + name + '"]').closest('div').append(modal);
                // $(document).on('load','.select2',function(){
                console.log('entro');
                $('.selectModel').select2({ placeholder: 'Select an option',width:350});
    //});
            }
            $('#myModalScan').modal({
                keyboard: false
            });
            callback();
            console.log(json);
            Swal.close();
        });
};
//outboundReceiving////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('body').on('DOMNodeInserted', 'select', function () {
    $(this).select2();
});







if ($('input[name="DynamicField2"').length) {
    var nearestDiv = $('input[name="DynamicField2"').closest('div');
    $(nearestDiv).removeClass('hidden');
    var btn = $('<input type="button" id="addPallets" name="addPallets" class="btn btn-warning addPallets" value="add Pallets">');
    $('input[name="DynamicField2"').closest('div').append(btn);
    $('body').on('click', '.addPallets', function () {
        appendModal(function () { }, 'DynamicField2');
        //});
    });
    var InboundLines = {};
    $(document).on('change', '.selectModel', function () {
        Swal.showLoading();
        var palletsize = $('option:selected', this).attr('palletsize');
        var cartonsize = $('option:selected', this).attr('cartonsize');
        $('.stdPackPallet').text(palletsize);
        $('.unitsincarton').val(cartonsize);
        $('.cartonsinpallet').val(palletsize);
        $('.stdPackCarton').text(cartonsize);
        $('.palletsscc').empty();
        $('#location').val('');
        fetch('http://voyager.local/public/api/products/' + $(this).val() + '/pallets')  //http://voyager.local/public/api/products
            .then(response => {
                console.log('entro')
                return response.json();
            })
            .then(json => {
                var pallets = json.data;
                console.log(pallets);
                $.each(pallets, function (i, v) {
                    var option = new Option(v.palletsscc, v.palletsscc, true, true);
                    option.setAttribute('location',v.location);
                    $('.palletsscc').select2().append(option).trigger('change');
                });
            Swal.close();
            });
    });
    $(document).on('change', '.palletsscc', function () {
        $('#location').val($(this).find('option:selected').attr('location'));
    });
    var i = 0;
    var lineId = "";
    var editLineId = "";
    var sumRecQty = 0;
    var sumQty = 0;
    $(document).on('click', '.add', function () {
        if ($("#selectModel").val() === "0") {
            toastr.warning("Select a model");
        } else if ($("#palletsscc").val() === "") {
            toastr.warning("pallet number it's required");
        } else if ($("#cartonsinpallet").val() === "") {
            toastr.warning("cartons in pallet value it's required");
        } else if ($("#unitsincarton").val() === "") {
            toastr.warning("units in carton value it's required");
        } else if ($("#location").val() === "") {
            toastr.warning("location value it's required");
        } else {
            if (editLineId.length == 0) {
                var d = new Date();
                lineId = d.getFullYear() + d.getTime() + i;
            } else {
                lineId = editLineId;
                editLineId = "";
                //$('#' + lineId + '').remove();
            }
            $('#myModalScan').modal('hide');
            var inboundLinesArray = $(".inboundLines").not('div');
            var pallet = {};
            inboundLinesArray.each(function () {
                var name = $(this).attr('name');
                var value;
                if ($(this).hasClass("select2") && name !== "UOM") {
                    value = $(this).find("option:selected").text();
                    pallet['product_id'] = $(this).val();
                    pallet['model'] = value;
                } else {
                    value = $(this).val().trim();
                    pallet[name] = value;
                }
                if (name === "unitsincarton") {
                    sumRecQty = parseInt(sumRecQty) + parseInt(value);
                }
                if (name === "cartonsinpallet") {
                    sumQty = parseInt(sumQty) + parseInt(value);
                }
            });
            InboundLines[lineId] = pallet;
            $thead = $('.headers > tr:first');
            $tbody = $('#standardData');
            $tfoot = $('#footers > tr:first').removeClass("hide");
            var trows = [];
            $('#standardData > tr').each(function () {
                var id = $(this).attr('id');
                trows.push(id);
            });
            if ($thead[0].children.length == 0) {
                $thead.append('<th></th>');
                $thead.append('<th></th>');
                $.each(InboundLines, function (lineId, groupInfo) {
                    $.each(groupInfo, function (name, value) {
                        if (name != "product_id") {
                            $thead.append('<th title=' + name + '>' + name + '</th>');
                        }
                    });
                });
            }
            $tfoot.find('#sumQty').empty().text(sumQty);
            $tfoot.find('#sumRecQty').empty().text(sumRecQty);
            $.each(InboundLines, function (lineId, groupInfo) {
                //If it does not exist, creat a new one, otherwise update selected row
                if (trows.indexOf(lineId) === -1) {
                    var td = '';
                    td = '<tr id=\"' + lineId + '\"><td><button id=\"delete\" type=\"button\" class=\"btn btn-danger delete\" title=\"Delete\"><i class=\"fa fa-close\">Delete</i></button></td>';
                    td = td + '<td><button id=\"edit\" type=\"button\" class=\"btn btn-info edit\" title=\"Edit\"><i class=\"fa fa-edit\"></i>Edit</button></td>';
                    $.each(groupInfo, function (name, value) {
                        if (name === "Quantity") {
                            td = td + '<td id=\"addQuantity\">' + value + '</td>';
                        } else if (name === "ReceiptQty") {
                            td = td + '<td id=\"addReceiptQty\">' + value + '</td>';
                        } else if (name == 'model') {
                            td = td + '<td title="' + name + '" product_id="' + groupInfo['product_id'] + '">' + value + '</td>';
                        } else if (name == 'product_id') { } else {
                            td = td + '<td title="' + name + '">' + value + '</td>';
                        }
                    });
                    td = td + '</tr>';
                    $tbody.append(td);
                } else {
                    $trow = $('#' + lineId + '');
                    $trow.empty();
                    var td = '<td><button id=\"delete\" type=\"button\" class=\"btn btn-danger delete\" title=\"Delete\"><i class=\"fa fa-close\"></i>Delete</button></td>';
                    td = td + '<td><button id=\"edit\" type=\"button\" class=\"btn btn-info edit\" title=\"Edit\"><i class=\"fa fa-edit\"></i>Edit </button></td>';
                    $.each(groupInfo, function (name, value) {
                        if (name === "Quantity") {
                            td = td + '<td id=\"addQuantity\">' + value + '</td>';
                        } else if (name === "ReceiptQty") {
                            td = td + '<td id=\"addReceiptQty\">' + value + '</td>';
                        } else if (name == 'model') {
                            td = td + '<td title="' + name + '" product_id="' + groupInfo['product_id'] + '">' + value + '</td>';
                        } else if (name == 'product_id') { } else {
                            td = td + '<td title="' + name + '">' + value + '</td>';
                        }
                    });
                    $trow.append(td);
                }
            });
        }
    });
    $(document).on("hidden.bs.modal", function () {
        $('#locationBin').empty();
        var inputs = $(this).find('.inboundLines');
        inputs.each(function () {
            $(this).val('');
        });
    });
    $(document).on('click', '.edit', function () {
        var id = $(this).closest('tr').attr('id');
        var palletInfo = InboundLines[id];
        var myRows = {};
        var headers = $("th").map(function () {
            return $(this).attr("title");
        }).get();
        var index2;
        var obj = {};
        var $rows = $(this).closest('tr').each(function (index) {
            $cells = $(this).find("td");
            index2 = $(this).attr('id');
            myRows[index2] = {};
            $cells.each(function (cellIndex) {
                var title = $(this).attr('title');
                if (headers.includes(title)) {
                    if (title == 'id') {
                        obj[title] = index2;
                    } else if (title == 'model') {
                        obj[title] = $(this).html();
                        obj['product_id'] = $(this).attr('product_id');
                    } else {
                        obj[title] = $(this).html();
                    }

                }
            });
            //myRows[index2] = obj;
        });
        appendModal(function () {
            console.log('callback');
            for (var name in obj) {
                $("input[name='" + name + "']").val(obj[name]);
                $("textarea[name='" + name + "']").val(obj[name]);
                console.log(name);
                if (name == 'product_id') {
                    $("select[name='" + 'selectModel' + "']").val(obj[name]).trigger('change');
                }

            }
            editLineId = id;
        }, 'DynamicField2', 'edit');
    });
    $(document).on('click', '.delete', function () {
        var id = $(this).closest('tr').attr('id');
        delete InboundLines[id];
        $(this).closest('tr').remove();
    });
    $(document).submit(function myFormSubmitCallback(event) {
        var myRows = {};
        var headers = $("th").map(function () {
            return $(this).attr("title");
        }).get();
        var $rows = $("tbody tr").each(function (index) {
            $cells = $(this).find("td");
            var index2 = $(this).attr('id');
            myRows[index2] = {};
            var obj = {};
            $cells.each(function (cellIndex) {
                var title = $(this).attr('title');
                if (headers.includes(title)) {
                    if (title == 'id') {
                        obj[title] = index2;
                    } else if (title == 'model') {
                        obj[title] = $(this).html();
                        obj['product_id'] = $(this).attr('product_id');
                    } else {
                        obj[title] = $(this).html();
                    }

                }
            });
            myRows[index2] = obj;
        });

        var myObj = {};
        myObj.myrows = myRows;
        var infoJSON = {};
        infoJSON['InboundLinesInfo'] = myObj.myrows;
        console.log(infoJSON);
        console.log(!jQuery.isEmptyObject(infoJSON['InboundLinesInfo']));
        if (!jQuery.isEmptyObject(infoJSON['InboundLinesInfo'])) {
            $('input[name="DynamicField2"]').val(JSON.stringify(infoJSON));
        } else {
            event.preventDefault();
            toastr.warning("Pallet line it's required");
        }

    });
}

function callModal(products, type) {
    var modal =
        '<div class="modal fade divBody" id="myModalScan" role="dialog" data-replace="true" style="display: none;"  aria-hidden="true" data-backdrop="false" >' +
        '    <div class="modal-dialog modal-lg">' +
        '        <div class="modal-content" id="prodUnitsContent">' +
        '            <div class="modal-header">' +
        '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>' +
        '                <h4 class="modal-title">Please add inbound line information:</h4>' +
        '            </div>' +
        '            <div class="modal-body">' +
        '                <div class="row">' +
        '                        <div class="form-group col-lg-6">' +
        '                            <label>Select a Model</label><br>' +
        '                            <select name="selectModel" id="selectModel" class="form-control inboundLines selectModel select2 " >' ;
        //'                                <option value="0" selected="selected">[- Please select a value -]</option>';
    $.each(products, function (i, v) {
        modal += ' <option value="' + v.id + '" palletsize="' + v.palletsize + '" cartonsize="' + v.cartonsize + '">' + v.model + '</option>';
    });
    modal += '                            </select>' +
        '                        </div>' + //collg6
        '                        <div class="form-group col-lg-6">' +
        '                            <label for="PalletSSCC">Pallet Number</label>' +
        '                                <select name="palletsscc" value="" class="form-control text-uppercase inboundLines palletsscc select2" id="palletsscc" placeholder="Pallet Number" data-title="Please enter a  Number"  ></select>' +
        '                        </div>' + //collg6
        '                </div>' +
        '                <div class="row">' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="cartonsinpallet">Cartons in Pallet<span class="label label-default stdPackPallet"></span><span class="calc"></span></label>' +
        '                                    <input type="text" name="cartonsinpallet" value="" class="form-control text-uppercase qtyInput inboundLines cartonsinpallet" id="cartonsinpallet" placeholder="Cartons in Pallet" data-title="Please enter a Number"   >' +
        '                            </div>' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="unitsincarton">Units in Carton <span class="label label-default stdPackCarton unitsincarton"></span><span class="calc"></span></label>' +
        '                                    <input type="text" name="unitsincarton" value="" class="form-control text-uppercase qtyInput inboundLines unitsincarton" id="unitsincarton" placeholder="units in carton" data-title="Please enter a valid Number"  >' +
        '                            </div>' +
        '                            <div class="form-group col-lg-4">' +
        '                                <label for="Area">Location</label>' +
        '                                    <input type="text" name="location" value="" class="form-control text-uppercase inboundLines" id="location" placeholder="location" data-title="Please enter a location"  >' +
        '                            </div>' +
        '                </div>' +
        '            <div class="row">' +
        '                <div class="form-group col-lg-12">' +
        '                    <label for="comments">Comments</label>' +
        '                    <textarea id="comments" class="form-control inboundLines" name="comments" rows="5" placeholder="Comments" ></textarea>' +
        '                </div>' +
        '            </div>' +
        '            </div>' +
        '            <div class="modal-footer">' +
        '                <button id="close" type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="icon fa fa-close"></i> Cancel</button>' +
        '                <button id="add" type="button" class="btn btn-info pull-right add"><i class="icon fa fa-cubes"></i> Add pallet</button>' +
        '            </div>' +
        '        </div>' +
        '        <!-- /.modal-content -->' +
        '    </div>' +
        '</div>' +
        '<div class="no-padding"> ';
    modal += '</div> ';
    modal = $(modal);
    return modal;
}
