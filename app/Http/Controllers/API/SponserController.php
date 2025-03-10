<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Sponser;
use App\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SponserController extends Controller
{
    use ResponseJsonTrait;
    // Admin Authorization
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    // Get All Sponsers
    public function index()
    {
        $sponsers = Sponser::all();
        return $this->sendSuccess('Sponsers Retrieved Successfully ', $sponsers);
    }
    // Get Specific Sponser By Id
    public function show($id)
    {
        $sponser = Sponser::findOrFail($id);
        return $this->sendSuccess('Sponser Data Retrieved Successfully ', $sponser);
    }
    // Admin Add New Sponser
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4000',
        ]);
        $originalName = $request->image->getClientOriginalName();
        $imageName = time() . '_' . $originalName;
        $request->image->move(public_path('uploads/sponsers'), $imageName);
        $imageUrl = asset('uploads/sponsers/' . $imageName);
        $sponser = Sponser::create([
            'title' => $request->title,
            'image' => $imageUrl,
        ]);
        return $this->sendSuccess('Sponser Added Successfully', $sponser, 201);
    }
    // Admin Update Sponser Data
    public function update(Request $request, string $id)
    {
        $sponser = Sponser::findOrFail($id);
        $request->validate([
            'title' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4000',
        ]);
        $data = [
            'title' => $request->title,
        ];
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/sponsers/' . basename($sponser->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $originalName = $request->image->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->image->move(public_path('uploads/sponsers'), $imageName);
            $data['image'] = asset('uploads/sponsers/' . $imageName);
        }
        $sponser->update($data);
        return $this->sendSuccess('Sponser Updated Successfully', $sponser, 200);
    }
    // Admin Remove Sponser
    public function destroy($id)
    {
        $sponser = Sponser::findOrFail($id);
        $imagePath = public_path(str_replace(asset('/'), '', $sponser->image));
        if (File::exists($imagePath) && !str_contains($sponser->image, 'default.jpg')) {
            File::delete($imagePath);
        }
        $sponser->delete();
        return $this->sendSuccess('Sponser Data Removed Successfully');
    }
}