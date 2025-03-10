<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IconService;
use App\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;

class IconServiceController extends Controller
{
     use ResponseJsonTrait;
     public function __construct(){
       $this->middleware('auth:admins')->only([ 'index' , 'store', 'update', 'destroy']);
    }
    public function index()
    {
        $iconServices = IconService::all();
        return $this->sendSuccess('Icons Of Services Retrieved Successfully ', $iconServices);
    }
    public function show($id)
    {
        $iconService = IconService::findOrFail($id);
        return $this->sendSuccess('Icon Service Retrieved Successfully ', $iconService);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);
        $iconService = IconService::create([
            'title' => $request->title,
        ]);
        return $this->sendSuccess('Icon Service Added Successfully ', $iconService, 201);
    }
    public function update(Request $request , string $id)
    {
        $iconService = IconService::findOrFail($id);
        $validatedRequest = $request->validate(rules: [
            'title' => 'string',
        ]);
        $iconService->update($validatedRequest) ;
        return $this->sendSuccess('Icon Service Updated Successfully ', $iconService, 201);
    }
    public function destroy($id)
    {
        $iconService = IconService::findOrFail($id);
        $iconService->delete();
        return $this->sendSuccess('Icon Service Deleted Successfully');
    }
}