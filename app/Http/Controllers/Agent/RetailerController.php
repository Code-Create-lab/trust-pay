<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class RetailerController extends Controller
{
    public function index()
    {
        $page_title = __('My Retailers');
        $token = (object)session()->get('sender_remittance_token');
        $retailers = User::orderByDesc("id")->paginate(12);

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

        // Form se jo aa raha hai
        $user->firstname   = $request->first_name;
        $user->lastname    = $request->last_name;
        $user->email       = $request->email;
        $user->username    = $request->username;
        $user->mobile      = $request->phone_number;
        $user->mobile_code   = '+91';
        $user->full_mobile   = $request->phone_number;
        $user->refferal_user_id  = '3';
        // Password auto generate
        $user->password    = bcrypt('Pass@1234');

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

        // id ke basis par data fetch karo
        $retailer = User::findOrFail($id);

        // data ko view me bhejo
        return view('agent.sections.retailers.edit', compact('page_title', 'retailer'));
    }

    public function updateRetailer(Request $request)
    {
        $request->validate([
            'id'          => 'required|exists:users,id',
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $request->id,
            'username'    => 'required|string|unique:users,username,' . $request->id,
            'phone_number'=> 'required|string|max:15|unique:users,mobile,' . $request->id,
        ]);

        $user = User::findOrFail($request->id);

        // Form se update karna
        $user->firstname   = $request->first_name;
        $user->lastname    = $request->last_name;
        $user->email       = $request->email;
        $user->username    = $request->username;
        $user->mobile      = $request->phone_number;
        $user->mobile_code = '+91';
        $user->full_mobile = $request->phone_number;

        $user->save();

        return redirect()->route('agent.retailer.recipient.index')->with('success', 'Retailer updated successfully!');
    }

    public function deleteRetailer($id)
    {
        $retailer = User::find($id);

        if (!$retailer) {
            return response()->json([
                'success' => false,
                'message' => 'Retailer not found!'
            ], 404);
        }

        $retailer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Retailer deleted successfully!'
        ]);
    }

}
