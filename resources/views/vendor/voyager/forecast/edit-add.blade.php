@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp
@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
<div class='row'>
    <div class="col-md-12">
        <input type="button" class="btn btn-primary info" value="Add new line" id='newLine'>
        <input type="hidden" class="btn btn-primary info" value="Add new line" id='DynamicField3' name='DynamicField3'>
    </div>
</div>
<table class="table table-condensed" id="forecast">
    <thead id="headers" class="headers">
        <tr>
            <th title='date'>Daily Capacity</th>
            <th title='qty'>Qty</th>
            <th title='in_transit'>In Transit</th>
            <th title='inspected'>Inspected</th>
            <th title='delivered'>Delivered</th>
        </tr>
    </thead>
    <tbody id="standardData">
        @if(isset($dataTypeContent2))
        @foreach($dataTypeContent2 as $row)
            <tr id="{{$row->id}}">
            <td title='date'><input type="date" class="form-control" name="shipment_date" placeholder="Shipment Date" value="{{$row->date}}" /></td>
            <td title='qty'><input type="number" class="form-control" name="qty" required="" step="any" placeholder="qty" value="{{$row->qty}}" /></td>
            <td title='in_transit'><input type="number" class="form-control" name="in_transit" required="" step="any" placeholder="in_transit" value="{{$row->in_transit}}" /></td>
            <td title='inspected'><input type="number" class="form-control" name="inspected" required="" step="any" placeholder="inspected" value="{{$row->inspected}}" /></td>
            <td title='delivered'><input type="number" class="form-control" name="delivered" required="" step="any" placeholder="delivered" value="{{$row->delivered}}" /> </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
     </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }
        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [year, month, day].join('-');
        }
        $('document').ready(function () {
                $('.toggleswitch').bootstrapToggle();
                var t = $('#forecast').DataTable();
                var counter = 1;
                var tomorrow = new Date();
                $('#newLine').on( 'click', function () {
                      var newDate=tomorrow.setDate(tomorrow.getDate() + 1);
                    t.row.add( [
                        '<input type="date" class="form-control" name="shipment_date" placeholder="Shipment Date" value="'+formatDate( newDate)+'">',
                        '<input type="number" class="form-control" name="qty" required="" step="any" placeholder="qty" value="0">',
                        '<input type="number" class="form-control" name="in_transit" required="" step="any" placeholder="in_transit" value="0">',
                        '<input type="number" class="form-control" name="inspected" required="" step="any" placeholder="inspected" value="0">',
                        '<input type="number" class="form-control" name="delivered" required="" step="any" placeholder="delivered" value="0">'
                    ] ).draw( false );
            
                    counter++;
                } );
 
    // Automatically add a first row of data
            //$('#newLine').click();

/*$('#forecast').on( 'dblclick', 'tbody tr td', function () {
  newInput(this);
} );
function newInput(elm) {
               $(elm).unbind('dblclick');
 
               var value = $(elm).text();
               $(elm).empty();
 
               $("<input>")
                   .attr('type', 'text')
                   .val(value)
                   .blur(function () {
                       closeInput(elm);
                   })
                   .appendTo($(elm))
                   .focus();
}
function closeInput(elm) {
    var value = $(elm).find('input').val();
    $(elm).empty().text(value);

    $(elm).bind("dblclick", function () {
        newInput(elm);
    });
}*/

var counter=0;

    $(document).submit(function myFormSubmitCallback(event) {
        var myRows = {};
        var headers = $("th").map(function () {
            return $(this).attr("title");
        }).get();
        var $rows = $("tbody tr").each(function (index) {
            $cells = $(this).find("td");
            var index2 = $(this).attr('id');
            if(index2==undefined)
                index2=counter;
            myRows[index2] = {};
            var obj = {};
            $cells.each(function (cellIndex) {
                var title = $(this).attr('title');
                console.log(title);
                if (headers[cellIndex]) {
                    if (title == 'id') {
                        obj[headers[cellIndex]] = index2;
                    } else if (title == 'model') {
                        obj[headers[cellIndex]] = $(this).html();
                        obj['product_id'] = $(this).attr('product_id');
                    } else {
                        obj[headers[cellIndex]] = $(this).find('input').val();
                    }

                }
            });
            myRows[index2] = obj;
            counter++;
        });

        var myObj = {};
        myObj.myrows = myRows;
        var infoJSON = {};
        infoJSON['InboundLinesInfo'] = myObj.myrows;
        console.log(infoJSON);
        console.log(!jQuery.isEmptyObject(infoJSON['InboundLinesInfo']));
        if (!jQuery.isEmptyObject(infoJSON['InboundLinesInfo'])) {
            $('input[name="DynamicField3"]').val(JSON.stringify(infoJSON));
        } else {
            event.preventDefault();
            toastr.warning("at least 1 line it's required");
        }

    });
            
            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
