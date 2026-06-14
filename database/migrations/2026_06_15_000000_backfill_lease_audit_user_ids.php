<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * leases.created_by / updated_by are meant to hold the *user id* (resolved via
 * the Lease::creator()/updater() relationships). A bug in LeaseForm stored the
 * user's *name* string instead, so the relationship couldn't resolve it and the
 * detail modal showed "N/A". This backfills legacy rows: name → matching user
 * id, and a missing created_by → the lease's owner. Idempotent (numeric values
 * are left untouched), so it is safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        $resolve = function ($val, $ownerId) {
            if ($val === null || $val === '') {
                return $ownerId;
            }
            if (is_numeric($val)) {
                return (int) $val;
            }
            $id = DB::table('users')->where('name', $val)->value('id');

            return $id ?: $ownerId;
        };

        foreach (DB::table('leases')->get() as $lease) {
            DB::table('leases')->where('id', $lease->id)->update([
                'created_by' => $resolve($lease->created_by, $lease->owner_id),
                // Keep "never edited" as null; only normalise non-null values.
                'updated_by' => ($lease->updated_by === null || $lease->updated_by === '')
                    ? null
                    : $resolve($lease->updated_by, $lease->owner_id),
            ]);
        }
    }

    public function down(): void
    {
        // Irreversible data backfill — no-op.
    }
};
