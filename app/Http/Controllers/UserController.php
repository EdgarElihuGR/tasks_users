<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //Declaration of vars in order to use the same form template
        $user = new User; 
        $title = "Nuevo Usuario";
        $txtButton = "Agregar";
        $route = route('users.store');
        $roles = Role::pluck('title', 'id');
        return view('users.create', compact('user','title','txtButton','route','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request, [
            'name' => 'required|max:80|string',
            'email' => 'required|unique:users',
            'password' => 'required',
            'roles.*' => 'integer',
            'roles' => 'required|array',
        ]);

        //Save validated fields to user
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        //Save roles to user
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
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
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //Declaration of vars in order to use the same form template
        $update = true;
        $title = "Actualizar Usuario";
        $txtButton = "Actualizar";
        $route = route('users.update',['user' => $user]);
        $roles = Role::pluck('title', 'id');
        $user->load('roles');

        return view('users.edit', compact('user','title','txtButton','route','update','roles'));
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
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request, [
            'name' => 'required|max:80|string',
            'email' => 'required|unique:users,email,' . $user->id,
            'roles.*' => 'integer',
            'roles' => 'required|array',
        ]);

        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();

        return redirect()->route('users.index');
    }
}
