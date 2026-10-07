<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_TO_NEW = [
        'Cek kondisi fisik' => 'Nonaktifkan aplikasi latar belakang',
        'Cek suhu dan kipas' => 'Bersihkan cache browser',
        'Cek storage' => 'Cek storage dan bersihkan Storage Sense',
        'Cek RAM dan performa' => 'Cek RAM dan optimasi performa',
        'Cek antivirus' => 'Cek antivirus dan firewall',
        'Cek koneksi jaringan' => 'Cek koneksi jaringan dan fisik perangkat',
    ];

    public function up(): void
    {
        $this->replaceChecklistLabels(self::OLD_TO_NEW);
    }

    public function down(): void
    {
        $this->replaceChecklistLabels(array_flip(self::OLD_TO_NEW));
    }

    private function replaceChecklistLabels(array $labels): void
    {
        DB::table('maintenance_berkalas')
            ->whereNotNull('checklist')
            ->orderBy('id')
            ->chunkById(100, function ($maintenances) use ($labels) {
                foreach ($maintenances as $maintenance) {
                    $checklist = json_decode($maintenance->checklist, true);

                    if (! is_array($checklist)) {
                        continue;
                    }

                    $updatedChecklist = array_map(
                        fn ($item) => $labels[$item] ?? $item,
                        $checklist,
                    );

                    if ($updatedChecklist === $checklist) {
                        continue;
                    }

                    DB::table('maintenance_berkalas')
                        ->where('id', $maintenance->id)
                        ->update(['checklist' => json_encode($updatedChecklist)]);
                }
            });
    }
};
