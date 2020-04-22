@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))
<style>
    th { white-space: nowrap; }
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
            <div class="col-md-9">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-condensed">
                                <thead>
                                    <tr>
                                        @foreach($dataType->browseRows as $row)
                                        <th style='    text-align: center;'>
                                            {{ $row->getTranslatedAttribute('display_name') }}
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    <tr>
                                        @foreach($dataType->browseRows as $row)
                                            @php
                                            if ($data->{$row->field.'_browse'}) {
                                                $data->{$row->field} = $data->{$row->field.'_browse'};
                                            }
                                            @endphp
                                            <td product_id='{{$data->product_id}}'>
                                                @include('voyager::multilingual.input-hidden-bread-browse')
                                                    {{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                                 
                            </table>
                        </div>
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
        var sumCustomerTotal=0;
        var sumTotalPaid=0;
        var sumAmountDue=0;

        $(document).ready(function () {
               $("#dataTable").append(
       $('<tfoot/>').append( $("#dataTable thead tr").clone() )
   );
            @if (!$dataType->server_side)
            var options={!!json_encode(
                    array_merge([
                         'bPaginate'=>false,
                         'bInfo'=>false,
                        "columnDefs" => [['targets' => [-1,-2,-3],  'className'=>'dt-body-right'],['width'=>'30%','targets'=>[0]]],
                        'footer'=>true
                    ])
                , true) !!};
                options['rowCallback']=function(row,data){
                    console.log(row);
                    console.log(data);
                    var prodid= $('td:eq(0)',row).attr('product_id');
                    $('td:eq(0)',row).prepend(   '<a href="{{url()->current()}}/filter/'+prodid+'" title="View" class="btn btn-sm btn-warning view"><i class="voyager-eye"></i> <span class="hidden-xs hidden-sm"></span></a>');
                };
                options['footerCallback']=  function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                            console.log(api.column(0).data());
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        if(typeof i === 'string'){
                            var x=i.replace('$','').replace(/[^\d\.\-]/g, ""); /*.replace('<div>','').replace('</div>','').replace(',','');
                            console.log(x);*/
                            return parseFloat(x);
                        }
                        return i;
                    };
                
                    sumAmountDue = api.column( 3 ).data().reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    sumTotalPaid = api.column( 2 ).data().reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    sumCustomerTotal = api.column( 1 ).data().reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );                        
                    $( api.column( 0 ).footer() ).html('Total ');
                    $( api.column( 3 ).footer() ).html(''+(sumAmountDue+'')/*.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')*/);
                    $( api.column( 2 ).footer() ).html(''+(sumTotalPaid+'')/*.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')*/);
                    $( api.column( 1 ).footer() ).html(''+(sumCustomerTotal+'')/*.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')*/); 
            }
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
