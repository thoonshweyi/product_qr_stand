<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\ProductWorkflow;
use App\Models\User;
use App\Models\WorkflowStep;

class ProductPol
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    // Admin can view all leave data
    public function viewany(User $user)
    {
        // check if the user has the 'Admin' role
        return $user->hasRoles(['Admin', 'Administrator', 'Checker']);
    }

    // Users can view their own leave datas
    public function view(User $user, Product $product)
    {
        // allow if the user has the required permission or is the owner of the leave
        return $user->hasPermission('view_resource') || $user->isOwner($product);
    }

    public function create(User $user)
    {
        return $user->hasRoles(['Administrator', 'Editor', 'Checker']);
    }

    public function edit(User $user, Product $product)
    {
        // allow Admin, Teacher to edit all leave records
        if ($user->hasRoles(['Admin', 'Administrator', 'Checker'])) {
            return true;
        }
        return false;
        // return $product->user_id == $user->id;
    }

    public function update(User $user, Product $product)
    {
        if ($user->hasRoles(['Administrator', 'Checker'])) {
            return true;
        }

        return $user->isOwner($product);
    }

    public function delete(User $user, Product $product)
    {
        if ($user->hasRoles(['Administrator'])) {
            return true;
        }
        return false;
        // return $user->hasPermission('delete_resource') || $user->isOwner($product);
    }
}
