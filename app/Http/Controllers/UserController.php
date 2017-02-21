<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ExportRepository;
use App\Repositories\ImportRepository;
use Gate;
use App\User;
use App\Role;

class UserController extends Controller
{
    protected $exportRepository;
    protected $importRepository;

    public function __construct(
        ExportRepository $exportRepository,
        ImportRepository $importRepository
    ) {
        $this->exportRepository = $exportRepository;
        $this->importRepository = $importRepository;
    }

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

        $users = User::paginate(2);
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
        if (Auth::user()->can('edit roles')) {
            $user->roles()->sync($request->input('roles'));
        }        

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

    public function export($format = 'xlsx') {

        if (Gate::denies('see users')) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $filename = date('Y-m-d') . ' ' . trans('Users');
        $users = User::select('name', 'email', 'phone')->get();
        switch ($format) {
            case 'xls':
                $this->exportRepository->downloadXLS($filename, $users);
                break;
            case 'csv':
                $this->exportRepository->downloadCSV($filename, $users);
                break;
            case 'tsv':
                $this->exportRepository->downloadTSV($filename, $users);
                break;
            default:
                $this->exportRepository->downloadXLSX($filename, $users);
                break;
        }
    }

    public function import(Request $request) {

        if (Gate::denies('edit users') && Gate::denies('edit own user', $user)) {
            return redirect()->route('users.show', Auth::user()->id);
        }

        $successfulMessages = [];
        $errorMessages = [];
        $rejectedUsers = null;

        if ($request->file('imported')->isValid()) {
             $extension = strtolower($request->imported->extension());
             $path = $request->imported->path();
             $rejectedUsers = collect();
             try {
                 switch ($extension) {
                     case 'xls':
                         $rejectedUsers = $this->importRepository->importXLS($path);
                         $successfulMessages[] = trans('The file was imported');
                         break;
                     case 'xlsx':
                         $rejectedUsers = $this->importRepository->importXLSX($path);
                         $successfulMessages[] = trans('The file was imported');
                         break;
                     case 'csv':
                         $rejectedUsers = $this->importRepository->importCSV($path);
                         $successfulMessages[] = trans('The file was imported');
                         break;
                     case 'tsv':
                         $rejectedUsers = $this->importRepository->importTSV($path);
                         $successfulMessages[] = trans('The file was imported');
                         break;
                     default:
                         $errorMessages[] = trans('Unknown format:' . $extension);
                         break;
                 }
             } catch (Exception $e) {

             }
        }

        if ($rejectedUsers && $rejectedUsers->count() ) {
            $notMigrated = trans('The following users were not included') . ': ' .
                $rejectedUsers->reduce(function($carry, $user) {
                    $output = '';
                    if (!empty($user['name'])) {
                        $output = $user['name'];
                    } elseif (!empty($user['email'])) {
                        $output = $user['email'];
                    }

                    if (!empty($output)) {
                        $carry->push($output);
                    }
                    return $carry;
                }, collect())->implode(', ');
            $errorMessages[] = $notMigrated;
        }

        return back()
            ->withErrors($errorMessages)
            ->withSuccess($successfulMessages);
    }
}
