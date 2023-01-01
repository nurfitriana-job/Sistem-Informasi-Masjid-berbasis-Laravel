<?php

namespace App\Policies;

use App\Models\CommodityAcquisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommodityAcquisitionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_commodity::acquisition');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('view_commodity::acquisition');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_commodity::acquisition');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('update_commodity::acquisition');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('delete_commodity::acquisition');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_commodity::acquisition');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('force_delete_commodity::acquisition');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_commodity::acquisition');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('restore_commodity::acquisition');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_commodity::acquisition');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CommodityAcquisition $commodityAcquisition): bool
    {
        return $user->can('replicate_commodity::acquisition');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_commodity::acquisition');
    }
}
