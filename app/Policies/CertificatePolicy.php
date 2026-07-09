<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Certificate;

class CertificatePolicy
{
    /**
     * Determine whether the user can view any certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('certificates.view');
    }

    /**
     * Determine whether the user can generate certificates.
     */
    public function generate(User $user): bool
    {
        return $user->hasPermissionTo('certificates.generate');
    }

    /**
     * Determine whether the user can revoke certificates.
     */
    public function revoke(User $user): bool
    {
        return $user->hasPermissionTo('certificates.revoke');
    }
}
