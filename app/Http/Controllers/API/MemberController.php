<?php
namespace App\Http\Controllers\API;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class MemberController extends Controller
{
    public function __construct(){
       $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
     use ResponseJsonTrait ;
    public function index()
    {
        $members = Member::all();
        return $this->sendSuccess('Members Data Retrieved Successfully ', $members);
    }
    public function show($id)
    {
        $member = Member::findOrFail($id);
        return $this->sendSuccess('Member Data Retrieved Successfully ', $member);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4000',
        ]);
        $originalName = $request->image->getClientOriginalName();
        $imageName = time() . '_' . $originalName;
        $request->image->move(public_path('uploads/members'), $imageName);
        $imageUrl = asset('uploads/members/' . $imageName);
        $member = Member::create([
            'name' => $request->name,
            'position' => $request->position,
            'image' => $imageUrl,
        ]);
        return $this->sendSuccess('Member Added Successfully ', $member, 201);
    }
    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $request->validate([
            'name' => 'nullable|string',
            'position' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data = [
            'title' => $request->title,
            'position' => $request->position,
        ];
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/members/' . basename($member->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $originalName = $request->image->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->image->move(public_path('uploads/members'), $imageName);
            $data['image'] = asset('uploads/members/' . $imageName);
        }
        $member->update($data);
        return $this->sendSuccess('Member Data Updated Successfully', $member, 200);
    }
    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $imagePath = public_path(str_replace(asset('/'), '', $member->image));
        if (File::exists($imagePath) && !str_contains($member->image, 'default.jpg')) {
            File::delete($imagePath);
        }
        $member->delete();
        return $this->sendSuccess('Member Data Removed Successfully');
    }
}