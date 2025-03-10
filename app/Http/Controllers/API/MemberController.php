<?php
namespace App\Http\Controllers\API;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class MemberController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
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
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4000',
            'facebook_link' => 'nullable|url',
            'linkedIn_link' => 'nullable|url',
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
        ]);
        $originalName = $request->image->getClientOriginalName();
        $imageName = time() . '_' . $originalName;
        $request->image->move(public_path('uploads/members'), $imageName);
        $imageUrl = asset('uploads/members/' . $imageName);
        $member = Member::create([
            'name' => $request->name,
            'position' => $request->position,
            'image' => $imageUrl,
            'facebook_link' => $request->facebook_link,
            'linkedIn_link' => $request->linkedIn_link,
            'phone' => $request->phone,
        ]);

        return $this->sendSuccess('Member Added Successfully ', $member, 201);
    }
    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $request->validate([
            'name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook_link' => 'nullable|url',
            'linkedIn_link' => 'nullable|url',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9]+$/',
        ]);
        $data = [
            'name' => $request->name ?? $member->name,
            'position' => $request->position ?? $member->position,
            'facebook_link' => $request->facebook_link ?? $member->facebook_link,
            'linkedIn_link' => $request->linkedIn_link ?? $member->linkedIn_link,
            'phone' => $request->phone ?? $member->phone,
        ];
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/members/' . basename($member->image));
            if (file_exists($oldImagePath) && !is_dir($oldImagePath)) {
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