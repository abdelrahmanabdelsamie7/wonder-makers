<?php
namespace App\Http\Controllers\API;
use App\Models\Service;
use App\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
class ServiceController extends Controller
{
    use ResponseJsonTrait;
    public function __construct(){
       $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $services = Service::all();
        return $this->sendSuccess('Services Retrieved Successfully', $services);
    }
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return $this->sendSuccess('Service Data Retrieved Successfully ', $service);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'required|string',
        ]);
        $service = Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
        ]);
        return $this->sendSuccess('Services Added Successfully ', $service, 201);
    }
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $validatedRequest = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'icon' => 'string',
        ]);
        $service->update($validatedRequest);
        return $this->sendSuccess('Services Updated Successfully ', $service, 201);
    }
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return $this->sendSuccess('Services Removed Successfully');
    }
}