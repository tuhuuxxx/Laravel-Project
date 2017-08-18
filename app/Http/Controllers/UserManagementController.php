<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
         $users = DB::table('users')->paginate(5);
       
        return view('users-mgmt.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users-mgmt.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateInput($request);
        User::create([
           'username' => $request['username'],
           'email' => $request['email'],
           'password' => bcrypt($request['password']),
           'firstname' => $request['firstname'],
           'lastname' => $request['lastname']
       ]);

       return redirect()->intended('/user-management');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {   
        $users = $this->doSearchingQuery($request->totalsearch);
        
        return view('users-mgmt.index', ['users' => $users]);
    }

    private function doSearchingQuery($value)
    {
        $columns = ['username', 'firstname', 'lastname'];
        $query = User::select('*');

        foreach($columns as $column) {
            $query->orWhere($column, 'like', '%'.$value.'%');
        }

        return $query->paginate(3);
    }

    public function validateInput($request)
    {
        $this->validate($request, [
            'username' => 'required|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60'
        ]);
    }
}
