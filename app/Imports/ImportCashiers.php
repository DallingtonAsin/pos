<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\User;

class ImportCashiers implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $first_name = $row["firstname"];
        $username = strtolower(Str::random(6) . "." . $first_name);

        (isset($row["email"]))
            ? $email =  $row["email"]
            : $email = null;

        (isset($row["contact2"]))
            ? $email =  $row["contact2"]
            : $email = null;

        return new User([
            'first_name' => $first_name,
            'last_name' => $row["lastname"],
            'name' => $row["firstname"] . " " . $row["lastname"],
            'username' => $username,
            'gender' => $row["gender"],
            'email' => $email,
            'user_role' => $this->getUserRoleId("Cashier"),
            'tel_no' => $row["contact1"],
            'alt_telno' => $row["contact2"],
            'address' => $row["address"],
            'nationalID_no' => $row["nationalidno"],
            'isActive' => true,
            'password' => Hash::make("12345678"),
        ]);
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
}
