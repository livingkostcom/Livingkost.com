<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data fix: rooms whose stored status is 'occupied' but have NO active lease
     * were left stuck (the LeaseObserver used to free rooms only for
     * 'closed'/'terminated', missing 'completed'/'cancelled'). Reset them to
     * 'available' so availability counts (landing, dashboard) are correct.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE rooms
            SET status = 'available'
            WHERE status = 'occupied'
              AND NOT EXISTS (
                  SELECT 1 FROM leases
                  WHERE leases.room_id = rooms.id
                    AND leases.status = 'active'
                    AND leases.deleted_at IS NULL
              )
        ");
    }

    public function down(): void
    {
        // Data correction — not reversible.
    }
};
