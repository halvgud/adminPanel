<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\InboundLine;
use Validator;


class InboundLineController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $InboundLines = InboundLine::all();
        return $this->sendResponse($InboundLines->toArray(), 'InboundLines retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $InboundLine = InboundLine::create($input);


        return $this->sendResponse($InboundLine->toArray(), 'InboundLine created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $InboundLine = InboundLine::find($id);


        if (is_null($InboundLine)) {
            return $this->sendError('InboundLine not found.');
        }


        return $this->sendResponse($InboundLine->toArray(), 'InboundLine retrieved successfully.');
    }


   
}
