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
        $services = Service::select('id', 'title', 'icon_service_id')
            ->with(['icon_service:id,title'])
            ->get();
        return $this->sendSuccess('Services Retrieved Successfully', $services);
    }
    public function show($id)
    {
        $service = Service::with(['icon_service:id,title'])->findOrFail($id);
        return $this->sendSuccess('Service Data Retrieved Successfully ', $service);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon_service_id' => 'required|string|exists:icon_services,id',
        ]);
        $service = Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon_service_id' => $request->icon_service_id,
        ]);
        return $this->sendSuccess('Services Added Successfully ', $service, 201);
    }
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $validatedRequest = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'icon_service_id' => 'string|exists:icon_services,id',
        ]);
        $service->update($validatedRequest);
        return $this->sendSuccess('Services Updated Successfully ', $service, 201);
    }
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $imagePath = public_path(str_replace(asset('/'), '', $service->image));
        if (File::exists($imagePath) && !str_contains($service->image, 'default.jpg')) {
            File::delete($imagePath);
        }
        $service->delete();
        return $this->sendSuccess('Services Removed Successfully');
    }
}