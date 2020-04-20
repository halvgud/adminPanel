@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))
<style>
    th { white-space: nowrap; }
</style>
<style>
    /* styles for responsive pivot UI + bootstrap-like styles */			

    .pivotHolder table.pvtUi {
        table-layout:fixed;
    }
    .pivotHolder select {
        visibility:hidden;
    }
    .pivotHolder select.form-control {
        visibility:visible;
    }

    .pivotHolder > table.pvtUi, .pivotHolder table.pvtTable {
        width:100%;
        margin-bottom:0px;
    }
    .pivotHolder > table.pvtUi>tbody>tr>td, .pivotHolder > table.pvtUi>tbody>tr>th {
        border: 1px solid #ddd;
    }
    .pivotHolder .pvtAxisContainer li span.pvtAttr {
        height:auto;
        white-space:nowrap;
    }
    .pivotHolder .pvtAxisContainer.pvtUnused, .pivotHolder .pvtAxisContainer.pvtCols {
        vertical-align:middle;
    }

    .pivotHolder > table.pvtUi>tbody>tr:first-child > td:first-child {
        width:250px;
    }

    .pivotHolder td.pvtRendererArea {
        padding-bottom:0px;
        padding-right:0px;
        border-bottom-width:0px !important;
        border-right-width:0px !important;
    }
    .pivotHolder td.pvtVals br { display:none; }			

    .pvtRendererArea>div {
        overflow:auto;
    }

    .pvtTableRendererHolder {
        max-height:800px;  /* limit table height if needed */
    }	
</style>
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(isset($dataType->order_column) && isset($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div id="output"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop

@section('javascript')
    <!-- DataTables -->

    <script>

        $(document).ready(function () {
                var nrecoPivotExt = new NRecoPivotTableExtensions({
                wrapWith: '<div class="pvtTableRendererHolder"></div>', // special div is needed by fixed headers when used with pivotUI
                fixedHeaders: true
            });
            const myServices = @json($dataTypeContent2);

            var sum = $.pivotUtilities.aggregatorTemplates.sum;
            var numberFormat = $.pivotUtilities.numberFormat;
            var intFormat = numberFormat({digitsAfterDecimal: 0});
            var propertiesTable = {
                    rows: ['forecast_id','contract','payment_term','model','quantity','shipment_date'],
                    cols: ['date'],
                   // vals: ['qty'],
                 aggregator: sum(intFormat)(["qty"]),
                    onRefresh: function (pivotUIOptions) {
                        nrecoPivotExt.initFixedHeaders($('#' + 'output' + ' table.pvtTable'));
                        $('#' + 'output' + ' select.pvtAttrDropdown:not(.form-control)').addClass('form-control input-sm');
                        $('#' + 'output' + ' select.pvtAggregator:not(.form-control), #' + 'output' + ' select.pvtRenderer:not(.form-control)').addClass('form-control input-sm');
                        $('#' + 'output' + '>table:not(.table)').addClass('table');
                    },rendererOptions: {
                    table: {
                        clickCallback: function(e, value, filters, pivotData){
                            var names = [];
                            console.log(filters);
                            pivotData.forEachMatchingRecord(filters,
                                function(record){ names.push(record.delivered); });
                            alert(names.join("\n"));
                        }
                    }
                }
                }
             $('#output' ).pivot(myServices, propertiesTable);
             $('.pvtTable').addClass('table table-condensed dataTable'); 
             // $('.pvtAxisLabel th[html="contract"] ').before('<th>actions</th>');
             $('.pvtTable').find('tr').each(function(i,v){
                 if(i==1){
                     $(this).find('th').eq(0).html('Actions');
                 }
                 if(i>1&&i<$('.pvtTable').find('tr').length-1){
                    var currentVal=$(this).find('th').eq(0).html();
                    console.log('{{url()->current()}}');
                    $(this).find('th').eq(0).html(
                        '<a href="{{url()->current()}}/'+currentVal+'" title="View" class="btn btn-sm btn-warning pull-right view"><i class="voyager-eye"></i> <span class="hidden-xs hidden-sm"></span></a>'+
                        '<a href="{{url()->current()}}/'+currentVal+'/edit" title="Edit" class="btn btn-sm btn-primary pull-right edit"><i class="voyager-edit"></i> <span class="hidden-xs hidden-sm"></span></a>');
                 }
             });
            @if (!$dataType->server_side)
            var options={!!json_encode(
                    array_merge([
                         'bPaginate'=>false,
                         'bInfo'=>false,
                        "columnDefs" => [['targets' => [-1,-2,-3],  'className'=>'dt-body-right']],
                    ])
                , true) !!};

            var table = $('#dataTable').DataTable(options);
            $('tfoot').show();
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>
@stop
