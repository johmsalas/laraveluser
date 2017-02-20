<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @param  Permission $permission
     * @return mixed
     */
    public function addPermission(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }
}
