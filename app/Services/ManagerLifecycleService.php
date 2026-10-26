<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * ManagerLifecycleService
 *
 * SRP: all business rules for manager account lifecycle live here,
 * completely decoupled from HTTP concerns (requests, redirects, flash messages).
 *
 * The controller merely translates HTTP <-> service calls (Thin Controller).
 * The service depends on the Eloquent model, so it can be unit-tested
 * against a real/in-memory database without simulating HTTP.
 */
class ManagerLifecycleService
{
    /** Managers waiting for admin approval (full list — rendered as a table). */
    public function pendingManagers(?string $search = null): Collection
    {
        return User::query()
            ->role(User::ROLE_MANAGER)
            ->status(User::STATUS_PENDING)
            ->search($search)
            ->latest()
            ->get();
    }

    /** Approved managers, paginated 10 per page (as per original behaviour). */
    public function approvedManagers(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->role(User::ROLE_MANAGER)
            ->status(User::STATUS_APPROVED)
            ->search($search)
            ->latest()
            ->paginate($perPage)
            ->withQueryString(); // preserves ?search=... across pages (replaces manual appends())
    }

    /** Count of deactivated manager accounts (dashboard badge). */
    public function deactivatedManagersCount(): int
    {
        return User::query()
            ->role(User::ROLE_MANAGER)
            ->status(User::STATUS_DEACTIVATED)
            ->count();
    }

    /** All deactivated manager accounts (dedicated listing page). */
    public function deactivatedManagers(): Collection
    {
        return User::query()
            ->role(User::ROLE_MANAGER)
            ->status(User::STATUS_DEACTIVATED)
            ->latest()
            ->get();
    }

    /** Approve a pending manager. */
    public function approve(User $manager): void
    {
        $manager->approve();
    }

    /** Reject a pending manager by permanently deleting the account. */
    public function reject(User $manager): void
    {
        $manager->delete();
    }

    /** Deactivate an approved manager (soft-block, account is kept). */
    public function deactivate(User $manager): void
    {
        $manager->deactivate();
    }

    /** Reactivate a previously deactivated manager. */
    public function reactivate(User $manager): void
    {
        $manager->approve();
    }

    /** Permanently delete a manager account. */
    public function destroy(User $manager): void
    {
        $manager->delete();
    }
}
