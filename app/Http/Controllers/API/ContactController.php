<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\contact;
use App\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ResponseJsonTrait;
    public function __construct(){
       $this->middleware('auth:admins')->only(['index', 'show', 'destroy']);
    }
    public function index()
    {
        $contacts = contact::all();
        return $this->sendSuccess('Contacts Data Retrieved Successfully ', $contacts);
    }
    public function show($id)
    {
        $contact = contact::findOrFail($id);
        return $this->sendSuccess('Contact Data Retrieved Successfully ', $contact);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'description' => 'required|string',
            'message' => 'required|string',
        ]);
        $contact = contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'message' => $request->message,
        ]);
        return $this->sendSuccess('Contact Data Added Successfully ', $contact, 201);
    }
    public function destroy($id)
    {
        $contact = contact::findOrFail($id);
        $contact->delete();
        return $this->sendSuccess('Contact Data Removed Successfully');
    }
}