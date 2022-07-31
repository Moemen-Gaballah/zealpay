<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BabyRequest;
use App\Http\Resources\BabyResource;
use App\Models\Baby;
use App\Services\BabyService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class BabyController extends Controller
{
    use APIResponse;
    public $babyService;

    public function __construct(BabyService $babyService)
    {
        $this->babyService = $babyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->page_size ?? 12;
        $babies = $this->babyService->get($pageSize);

        return $this->sendResponse($babies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BabyRequest $request)
    {
        $baby = $this->babyService->store($request->validated());

        $data = new BabyResource($baby);

        return $this->sendResponse($data, 'done added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Baby  $baby
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $baby = $this->babyService->find($id);

        return $this->sendResponse(new BabyResource($baby));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Baby  $baby
     * @return \Illuminate\Http\Response
     */
    public function update(BabyRequest $request, $id)
    {
        $baby = Baby::findOrFail($id);
        if(auth('api')->id() != $baby->user_id){
            return $this->sendError('Unauthorized', null,401);
        }

        $baby = $this->babyService->update($request->validated(), $baby);
        $data = new BabyResource($baby);

        return $this->sendResponse($data, 'done updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Baby  $baby
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $baby = Baby::findOrFail($id);
        if(auth('api')->id() != $baby->user_id){
            return $this->sendError('Unauthorized', null,401);
        }
        $baby = $this->babyService->delete($baby);

        return $this->sendResponse($baby, 'done deleted successfully');
    }
}
