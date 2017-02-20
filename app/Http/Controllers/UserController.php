<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;
use App\User;
use App\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('see users')) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $users = User::all();
        return view('users/list', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('create users')) {
            return redirect()->route('users.show', Auth::user()->id);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('create users')) {
            return redirect()->route('users.show', Auth::user()->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (Gate::denies('see users') && Gate::denies('see own user', $user)) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $roles = $user->roles->implode('label', ', ');
        return view('users/view', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (Gate::denies('edit users') && Gate::denies('edit own user', $user)) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $roles = Role::all();

        return view('users/edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (Gate::denies('edit users') && Gate::denies('edit own user', $user)) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $this->validate($request, [
            'name' => 'required|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->fill($request->all())->save();
        $user->roles()->sync($request->input('roles'));

        return redirect()->route('users.show', $user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (Gate::denies('delete users') && Gate::denies('delete own user', $user)) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('message', $user . ' ' . trans('was deleted'));
    }
}
