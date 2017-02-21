<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Permission;

class AuthServiceProvider extends ServiceProvider{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        App\User::class => App\Policies\UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (Schema::hasTable('permissions')) {
            foreach ($this->getPermissions() as $permission) {
                Gate::define($permission->name, function ($user, $userTarget = null) use ($permission) {
                    if($permission=='see own user') {
                        var_dump($user);
                        var_dump($userTarget);
                        exit();
                    }
                    $typeOwn = strpos($permission, ' own ') > 0;
                    return (!$typeOwn && $user->hasPermission($permission)) ||
                        ($typeOwn && $userTarget->id == $user->id);
                });
            }
        }

    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
