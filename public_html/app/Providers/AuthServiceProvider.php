<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;    

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // --- KODE GATE SUDAH DIGANTI DENGAN YANG LEBIH AMAN ---
        // Mendefinisikan Gate 'access-admin'
        Gate::define('access-admin', function (User $user) {
            // Cek method isAdminOrOwner() (dari file lama Anda)
            if (method_exists($user, 'isAdminOrOwner')) {
                // Anda bisa juga menambahkan pengecekan 'staff' di sini jika mau
                // return $user->isAdminOrOwner() || $user->hasAnyRole(['staff']);
                return $user->isAdminOrOwner();
            }
            
            // Fallback jika method tidak ada (misal: cek role 'admin')
            if (method_exists($user, 'hasRole')) {
                 return $user->hasRole('admin'); // atau 'admin', 'owner', 'staff'
            }

            // Default, tolak akses jika tidak ada method yang cocok
            return false;
        });
        // --- SELESAI PENAMBAHAN ---
    }
}
