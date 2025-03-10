<?php
namespace App\Http\Controllers\API;
use App\Models\ImageProject;
use Illuminate\Http\Request;
use App\Traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
class ImageProjectController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $imageProjects = ImageProject::all();
        return $this->sendSuccess('Images Of Projects Retrieved Successfully ', $imageProjects);
    }
    public function show($id)
    {
        $imageProject = ImageProject::findOrFail($id);
        return $this->sendSuccess('Image Project Retrieved Successfully ', $imageProject);
    }
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|string|exists:projects,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4000',
        ]);
        $originalName = $request->image->getClientOriginalName();
        $imageName = time() . '_' . $originalName;
        $request->image->move(public_path('uploads/projects'), $imageName);
        $imageUrl = asset('uploads/projects/' . $imageName);
        $imageProject = ImageProject::create([
            'project_id' => $request->project_id,
            'image' => $imageUrl,
        ]);
        return $this->sendSuccess('Image Project Added Successfully ', $imageProject, 201);
    }
    public function update(Request $request, $id)
    {
        $imageProject = ImageProject::findOrFail($id);
        $request->validate([
            'project_id' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data = [
            'project_id' => $request->project_id,
        ];
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/projects/' . basename($imageProject->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $originalName = $request->image->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->image->move(public_path('uploads/projects'), $imageName);
            $data['image'] = asset('uploads/projects/' . $imageName);
        }
        $imageProject->update($data);
        return $this->sendSuccess('Image Project Updated Successfully', $imageProject, 201);
    }
    public function destroy($id)
    {
        $imageProject = ImageProject::findOrFail($id);
        $imagePath = public_path(str_replace(asset('/'), '', $imageProject->image));
        if (File::exists($imagePath) && !str_contains($imageProject->image, 'default.jpg')) {
            File::delete($imagePath);
        }
        $imageProject->delete();
        return $this->sendSuccess('Image Project Removed Successfully');
    }
}