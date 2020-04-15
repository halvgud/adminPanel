<?php

namespace App\Http\Controllers\Voyager;

use App\InboundReceiving;
use App\InboundLine;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Events\BreadDataAdded;
use Illuminate\Routing\Controller as BaseController;

class InboundReceivingController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    //...
    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType =Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));
        $inboundReceiving = new InboundReceiving;
        $inboundLines= json_decode($request->input('DynamicField'), true)['InboundLinesInfo'];
        
        $inboundReceiving->status = $request->input('status');
        $inboundReceiving->ship_from = $request->input('ship_from');
        $inboundReceiving->inbound_number = $request->input('inbound_number');
        $inboundReceiving->reference = $request->input('reference');
        $inboundReceiving->qty = $request->input('qty');
        $inboundReceiving->tracking_number = $request->input('tracking_number');
        $inboundReceiving->save();
        $inboundReceivingId= $inboundReceiving->id;
        foreach ($inboundLines as &$valor) {
           $inboundLine = new InboundLine;
           $inboundLine->inbound_receiving_id=$inboundReceivingId;

            $inboundLine->palletsscc = $valor['palletsscc'];
            $inboundLine->received_cartonsinpallet = $valor['cartonsinpallet'];
            $inboundLine->received_unitsincarton = $valor['unitsincarton'];
            $inboundLine->cartonsinpallet = $valor['cartonsinpallet'];
            $inboundLine->unitsincarton = $valor['unitsincarton'];
            $inboundLine->location = $valor['location'];
            $inboundLine->comments = isset($valor['comments']) ? $valor['comments'] :  '';
            $inboundLine->product_id = $valor['product_id']; //$valor['palletsscc'];
            $inboundLine->save();
        }
        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::generic.successfully_added_new') . " {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }
    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);            
            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }
        $dataTypeContent2 = DB::table('inbound_lines')->join('products','inbound_lines.product_id','products.id')->where('inbound_receiving_id', $id)->get()->all();
        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);
        $dataTypeContent2 = $this->resolveRelations($dataTypeContent2, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);
        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted', 'dataTypeContent2'));
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }
        $dataTypeContent2 = DB::table('inbound_lines')->join('products', 'inbound_lines.product_id', 'products.id')->where('inbound_receiving_id', $id)
        ->select('inbound_lines.product_id', 'products.model', 'palletsscc','unitsincarton','cartonsinpallet','location','comments','inbound_lines.id')->get()->all();


        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'dataTypeContent2'));
    }

    /**
     * Update BREAD.
     *
     * @param \Illuminate\Http\Request $request
     * @param number                   $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $this->authorize('browse_bread');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        try {
            $slug = $this->getSlug($request);
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            $inboundReceiving = InboundReceiving::find($id);
            $inboundLines = json_decode($request->input('DynamicField'), true)['InboundLinesInfo'];
            $inboundReceiving->status = $request->input('status');
            $inboundReceiving->ship_from = $request->input('ship_from');
            $inboundReceiving->inbound_number = $request->input('inbound_number');
            $inboundReceiving->reference = $request->input('reference');
            $inboundReceiving->qty = $request->input('qty');
            $inboundReceiving->update();
            DB::table('inbound_lines')->where('inbound_receiving_id', $id)->delete();
            foreach ($inboundLines as &$valor) {
                $inboundLine = new InboundLine;
                $inboundLine->inbound_receiving_id =  $id;
                $inboundLine->palletsscc = $valor['palletsscc'];
                $inboundLine->cartonsinpallet = $valor['cartonsinpallet'];
                $inboundLine->unitsincarton = $valor['unitsincarton'];
                $inboundLine->received_cartonsinpallet = $valor['cartonsinpallet'];
                $inboundLine->received_unitsincarton = $valor['unitsincarton'];
                $inboundLine->location = $valor['location'];
                $inboundLine->comments = isset($valor['comments']) ? $valor['comments'] :  '';
                $inboundLine->product_id = $valor['product_id']; //$valor['palletsscc'];
                $inboundLine->save();
            }
            /*$dataType = Voyager::model('DataType')->find($id);

            // Prepare Translations and Transform data
            $translations = is_bread_translatable($dataType)
                ? $dataType->prepareTranslations($request)
                : [];
            dd($request->all());
            $res = $dataType->updateDataType($request->all(), true);
            $data = $res
                ? $this->alertSuccess(__('voyager::bread.success_update_bread', ['datatype' => $dataType->name]))
                : $this->alertError(__('voyager::bread.error_updating_bread'));
            if ($res) {
                event(new BreadUpdated($dataType, $data));
            }

            // Save translations if applied
            $dataType->saveTranslations($translations);*/
            return redirect()->route('voyager.'.$dataType->slug.'.index')
            ->with($this->alertSuccess(__('voyager::bread.success_update_bread', ['datatype' => $dataType->name])));
        } catch (Exception $e) {
            return back()->with($this->alertException($e, __('voyager::generic.update_failed')));
        }
    }
}
