<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Controllers\LogsController;
use App\Models\Role;
use App\User;


class ProfileController extends Controller
{

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.main.profile');
    }

    public function accountSettings()
    {
        return view('pages.main.account-settings');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function is_inArr($dataArr, $item)
    {
        if (count($dataArr) > 0) {
            (in_array($item, $dataArr))
                ? $bool = true
                : $bool = false;
        }

        return $bool;
    }

    protected function getUserRoleId($role)
    {
        $roleId = Role::where('name', $role)->value('id');
        return $roleId;
    }

    protected function getRole($id)
    {
        $role = Role::where('id', $id)->value('name');
        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updates(Request $request, $id)
    {

        $user = User::find($id);
        $old_username = $user->username;
        $user->username = $new_username = $request->input('Username');
        $user->email = $request->input('Email');
        $user->tel_no = $request->input('Contact');
        $user->address = $request->input('Address');
        $OldPassword = $request->input('OldPassword');
        $NewPassword = $request->input('NewPassword');
        $ConfirmPassword = $request->input('PasswordConfirm');

        if ($request->hasfile('image')) {

            $this->validate($request, [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move("uploads/images/" . $this->getRole(Auth::user()->role_id) . "", $filename);
            $user->image = $filename;
        } else {
            //$user->image = '';
        }

        $arr =  $this->getUsernamesArr();


        if (in_array($old_username, $arr)) {
            for ($i = 0; $i < count($arr); $i++) {
                if ($arr[$i] == $old_username) {
                    $index = $i;
                    break;
                } else {
                    $index = -1;
                }
            }

            $newArr = Arr::except($arr, $index);
        } else {
            $newArr = $arr;
        }

        $bool = $this->is_inArr($newArr, $new_username);

        if ($bool === true) {
            $sessionVariable = 'error';
            $message = "this username " . $new_username . " is already taken up, please enter a different one!";
        } else if ($bool === false) {

            if ($request->filled('OldPassword') && $request->filled('NewPassword') && isset($ConfirmPassword)) {
                //   dd("Whatsap");
                if (Hash::check($OldPassword, Auth::user()->password)) {
                    if ($NewPassword == $ConfirmPassword) {
                        $user->password = Hash::make($ConfirmPassword);
                    } else {
                        $sessionVariable = 'error';
                        $message = "Your new passwords don't match, please enter matching passwords";
                        return response()->json([$sessionVariable => $message]);
                    }
                } else {
                    $sessionVariable = 'error';
                    $message = "You have entered old password that doesn't match the current stored password, please try again!";
                    return response()->json([$sessionVariable => $message]);
                }
            } else {
                $user->password = Auth::user()->password;
            }

            $result = $user->save();
            if ($result) {
                $gender = $this->getGender(Auth::user()->id);
                $action = "updated " . $gender . " profile";
                LogsController::logger($request, $action, now());
                $actionx = Str::replaceFirst($gender, 'your', $action);
                $sessionVariable = 'success';
                $message = $this->ActionMessage($actionx);
            } else {
                $sessionVariable = 'error';
                $message = 'Profile update failed';
            }
        }

        return response()
            ->json([$sessionVariable => $message]);
    }



    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'username' => 'required',
            'email' => 'sometimes|nullable',
            'contact' => 'required',
            'address' => 'required'
        ]);

        try {

            $user = User::find($id);
            $old_username = $user->username;
            $user->username = $new_username = $request->input('username');
            $user->email = $request->input('email');
            $user->tel_no = $request->input('contact');
            $user->address = $request->input('address');

            $OldPassword = $request->input('old_password');
            $NewPassword = $request->input('new_password');
            $ConfirmPassword = $request->input('password_confirm');

            if ($request->hasfile('image')) {

                // $this->validate($request, [
                //     'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // ]);

                // $fileName = time().'.'.$request->file('image')->getClientOriginalExtension();
                // $filePath = $request->file('image')->storeAs("avatars/".strtolower(Helper::getRole(Auth::user()->role)), $fileName, 'public');
                // $user->image = $filePath;


                $this->validate($request, [
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension(); //getting image extension
                $filename = time() . '.' . $extension;
                $file->move("uploads/images/" . $this->getRole(Auth::user()->role_id) . "", $filename);
                $user->image = $filename;
            }

            $arr =  $this->getUsernamesArr();

            if (in_array($old_username, $arr)) {
                for ($i = 0; $i < count($arr); $i++) {
                    if ($arr[$i] == $old_username) {
                        $index = $i;
                        break;
                    } else {
                        $index = -1;
                    }
                }

                $newArr = Arr::except($arr, $index);
            } else {
                $newArr = $arr;
            }

            $bool = $this->is_inArr($newArr, $new_username);

            if ($bool === true) {
                $sessionVariable = 'error';
                $message = "Username " . $new_username . " is already taken up, please enter a different one!";
            } else if ($bool === false) {

                if ($request->filled('old_password') && $request->filled('new_password') && isset($ConfirmPassword)) {

                    if (Hash::check($OldPassword, Auth::user()->password)) {
                        if ($NewPassword == $ConfirmPassword) {
                            $user->password = Hash::make($ConfirmPassword);
                        } else {
                            $sessionVariable = 'error';
                            $message = "Your new passwords do not match, please enter matching passwords";
                            return back()->withInput()->with('error', $message);
                        }
                    } else {
                        $sessionVariable = 'error';
                        $message = "You have entered old password that does not match the current stored password, please try again!";
                        return back()->withInput()->with('error', $message);
                    }
                } else {
                    $user->password = Auth::user()->password;
                }

                if ($user->save()) {
                    $gender = $this->getGender(Auth::user()->id);
                    $action = "updated " . $gender . " profile";
                    LogsController::logger($request, $action, now());
                    $actionx = Str::replaceFirst($gender, 'your', $action);
                    $sessionVariable = 'success';
                    $message = $this->ActionMessage($actionx);
                } else {
                    $sessionVariable = 'error';
                    $message = 'Profile update failed';
                }
            }

            return back()->with([$sessionVariable => $message]);
        } catch (\Exception $ex) {
            $exception_message = $ex->getMessage();
            return back()->withInput()->with('error', $exception_message);
        }
    }


















    protected function getUsernamesArr()
    {

        $usernames = User::pluck('username');
        $dataArr = array();
        foreach ($usernames as $username) {
            $dataArr[] = $username;
        }
        return $dataArr;
    }

    public function getGender($id)
    {

        $gender = User::where('id', $id)->value('gender');
        (strtolower($gender) == 'male')
            ? $value = 'his'
            : $value = 'her';
        return $value;
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


    protected function ActionMessage($action)
    {
        $message = "You have successfully " . $action . "";
        return $message;
    }
}
