<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentMethodPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->roles === 'Owner'
            ? Response::allow()
            : Response::deny('You do not have access to create this data');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateOrCreate(User $user, PaymentMethod $paymentMethod)
    {
        // Check if the user is the owner of the payment method
        if ($user->id === $paymentMethod->users_id) {
            return Response::allow();
        }

        // If not, deny access
        return Response::deny('You do not have access to update this data');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentMethod $paymentMethod)
    {
        return $user->id === $paymentMethod->users_id
            ? Response::allow()
            : Response::deny('You do not have access to delete this data');
    }
}
