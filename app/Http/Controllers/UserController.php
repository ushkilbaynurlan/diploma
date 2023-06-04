<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\UserAddRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();


        if($user->can('manage-users'))
        {

            // $this->authorize(User::class, 'index');

            $users = User::where('parent', '=', Auth::user()->getCreatedBy())->get();

            return view('admin.users.index', compact('users'));
        }
        else
        {

            return view('403');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        if($user->can('create-users'))
        {
            $roles = Role::get();

            return view('admin.users.create', compact('roles'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        $user = \Auth::user();
        if($user->can('create-users'))
        {
            $validation = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ];
            if($request->avatar)
            {
                $validation['avatar'] = 'required|image';
            }
            $request->validate($validation);

            $post = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'parent' => Auth::user()->getCreatedBy(),
            ];

            if($request->avatar)
            {
               $filenameWithExt = $request->file('avatar')->getClientOriginalName();
               $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
               $extension       = $request->file('avatar')->getClientOriginalExtension();
               $fileNameToStore = $filename . '_' . time() . '.' . $extension;
               $url = '';
               $dir        = 'public/';
               $path = Utility::upload_file($request,'avatar',$fileNameToStore,$dir,[]);
               if($path['flag'] == 1){
                   $url = $path['url'];
               }else{
                   return redirect()->back()->with('error', __($path['msg']));
               }
               $post['avatar'] = $fileNameToStore;
            }

            $user = User::create($post);
            $role = Role::find(2);
            if($role)
            {
                $user->assignRole($role);
            }

            $user_role = Role::where('name',Auth::user()->getCreatedBy())->pluck('id');
            $role = Role::find($user_role);
            if($role)
            {
                $user->assignRole($role);
                $user->userDefaultDataRegister($user->id);
            }

            $uArr = [
                'email' => $user->email,
                'password' => $request->password,
            ];

            $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);
            // dd($resp);

            return redirect()->route('admin.users')->with('success', __('User created successfully'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $userObj = \Auth::user();
        if($userObj->can('edit-users') || $user->id == $userObj->id)
        {
            $roles = Role::get();

            return view('admin.users.edit', compact('user', 'userObj', 'roles'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $userObj = \Auth::user();
        if($userObj->can('edit-users') || $user->id == $userObj->id)
        {
            $user->update(
                $request->only(
                    [
                        'name',
                        'email',
                    ]
                )
            );

            if($request->password)
            {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if($request->avatar)
            {
                $request->validate(['avatar' => 'required|image']);

                // $avatarName = 'avatar-' . time() . '.' . $request->avatar->getClientOriginalExtension();
                // $request->avatar->storeAs('public', $avatarName);
                // $user->update(['avatar' => $avatarName]);

                $filenameWithExt = $request->file('avatar')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('avatar')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $url = '';
                $dir        = 'public/';

                $path = Utility::upload_file($request,'avatar',$fileNameToStore,$dir,[]);
                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $user->update(['avatar' => $fileNameToStore]);
            }

            if($request->role && $request->user()->can('edit-users') && !$user->isme)
            {
                $role = Role::find($request->role);
                if($role)
                {
                    $user->syncRoles([$role]);
                }
            }

            return redirect()->route('admin.dashboard')->with('success', __('User updated successfully'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $objUser = \Auth::user();
        if($objUser->can('delete-users'))
        {
            $user->delete();

            return redirect()->route('admin.users')->with('success', __('User deleted successfully'));
        }
        else
        {
            return view('403');
        }
    }

    public function roles()
    {
        return response()->json(Role::get());
    }


    public function userPassword($id)
    {
        // dd($id);
        $eId  = \Crypt::decrypt($id);
        $user = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('admin.users.reset', compact('user', 'employee'));
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'password' => 'required|confirmed|same:password_confirmation',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $user      = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('admin.users')->with('success', 'User Password successfully updated.');
    }

}

