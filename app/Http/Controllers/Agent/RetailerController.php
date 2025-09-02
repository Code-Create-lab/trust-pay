<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RetailerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $page_title = __('My Retailers');
        $token = (object)session()->get('sender_remittance_token');
        $retailers = User::where('refferal_user_id',$user->id)->orderByDesc("id")->paginate(12);

        return view('agent.sections.retailers.index',compact('page_title','retailers'));
    }

    public function addRetailer(){
        $page_title =__( "Add Retailer");
        return view('agent.sections.retailers.add',compact('page_title'));
    }

    public function storeRetailer(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'username'     => 'required|string|unique:users,username',
            'phone_number' => 'required|string|max:15|unique:users,mobile',
        ]);

        $user = new User();

        $user->firstname   = $request->first_name;
        $user->lastname    = $request->last_name;
        $user->email       = $request->email;
        $user->username    = $request->username;
        $user->mobile      = $request->phone_number;
        $user->mobile_code   = '+91';
        $user->full_mobile   = $request->phone_number;
        $user->refferal_user_id  = auth()->user()->id;
        $user->password    = bcrypt('Pass@1234');
        $user->email_verified  = 1;
        $user->email_verified_at  = now();

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Retailer added successfully!',
            'data'    => $user
        ]);
    }

    public function editRetailer($id)
    {
        $page_title = __("Edit Retailer");
        $retailer = User::findOrFail($id);
        return view('agent.sections.retailers.edit', compact('page_title', 'retailer'));
    }

    public function updateRetailer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|exists:users,id',
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $request->id,
            'username'    => 'required|string|unique:users,username,' . $request->id,
            'phone_number'=> 'required|string|max:15|unique:users,mobile,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($request->id);

        $user->firstname   = $request->first_name;
        $user->lastname    = $request->last_name;
        $user->email       = $request->email;
        $user->username    = $request->username;
        $user->mobile      = $request->phone_number;
        $user->mobile_code = '+91';
        $user->full_mobile = $request->phone_number;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Retailer updated successfully!'
        ]);
    }

    public function deleteRetailer($id)
    {
        $retailer = User::find($id);

        if (!$retailer) {
            return response()->json([
                'success' => false,
                'message' => 'Retailer not found!'
            ], 404)->header('Content-Type', 'application/json');
        }

        $retailer->delete();
        return redirect()->back()->with('success','Retailer deleted successfully!');
    }
}
