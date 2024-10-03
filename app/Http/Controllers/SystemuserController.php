<?php

namespace App\Http\Controllers;

use App\Models\Systemuser;
// use App\Http\Requests\StoreSystemuserRequest;
// use App\Http\Requests\UpdateSystemuserRequest;
use Illuminate\Http\Request;

class SystemuserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $systemusers = Systemuser::all();
        return $systemusers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formField = $request->validate([
            'first_name' => 'required|string|max:225',
            'last_name' => 'required|string|max:225',
            'address' => 'required|string|max:225',
            'phone' => 'required|string|max:225',
            'email' => 'required|string|max:225',
            'password' => 'required|string|max:225',
            'role' => 'required|string|max:225',
        ]);
        $systemusers = Systemuser::create($formField);
        return $systemusers;
        // $account = new Systemuser([
        //     'first_name' => $request->get('first_name'),
        //     'last_name' => $request->get('last_name'),
        //     'address' => $request->get('address'),
        //     'phone' => $request->get('phone'),
        //     'email' => $request->get('email'),
        //     'password' => $request->get('password'),
        //     'role' => $request->get('role')
        // ]);
        // $account->save();
        // return response()->json('Successfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(Systemuser $systemuser)
    {
        return $systemuser;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemuserRequest $request, Systemuser $systemuser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Systemuser $systemuser)
    {
        //
    }
}
