<?php
namespace App\Http\Controllers\API;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
class ProjectController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $projects = Project::all();
        return $this->sendSuccess('Projects Retrieved Successfully ', $projects);
    }
    public function show($id)
    {
        $project = Project::with('images')->findOrFail($id);
        return $this->sendSuccess('Project Data Retrieved Successfully ', $project);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'imageCover' => 'required|image|mimes:jpeg,png,jpg,gif|max:4000',
        ]);
        $originalName = $request->imageCover->getClientOriginalName();
        $imageName = time() . '_' . $originalName;
        $request->imageCover->move(public_path('uploads/projects'), $imageName);
        $imageUrl = asset('uploads/projects/' . $imageName);
        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'imageCover' => $imageUrl,
        ]);
        return $this->sendSuccess('Project Added Successfully ', $project, 201);
    }
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'imageCover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
       $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];
        if ($request->hasFile('imageCover')) {
            $oldImagePath = public_path('uploads/projects/' . basename($project->imageCover));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $originalName = $request->imageCover->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->imageCover->move(public_path('uploads/projects'), $imageName);
            $data['imageCover'] = asset('uploads/projects/' . $imageName);
        }
        $project->update($data);
        return $this->sendSuccess('Project Updated Successfully', $project, 200);
    }
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $imagePath = public_path(str_replace(asset('/'), '', $project->imageCover));
        if (File::exists($imagePath) && !str_contains($project->imageCover, 'default.jpg')) {
            File::delete($imagePath);
        }
        $project->delete();
        return $this->sendSuccess('Project Removed Successfully');
    }
}