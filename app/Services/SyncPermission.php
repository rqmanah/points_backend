<?php

namespace App\Services;


use Spatie\Permission\Models\Role;

class SyncPermission
{

    public function syncPermission($id,$permissions)
    {

        $role = Role::where('id',$id)->first();
        if ($role){
            $role->syncPermissions($permissions);
        }
    }

}
