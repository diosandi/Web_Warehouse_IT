<?php

use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\IssueReport;
use App\Models\Items;
use App\Models\Locations;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores an uploaded evidence photo for an issue report', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client']);
    $location = Locations::create([
        'gedung' => 'Gedung A',
        'ruangan' => 'R01',
        'type' => 'warehouse',
    ]);
    $distribution = Distribution::create([
        'location_id' => $location->id,
        'user_id' => $user->id,
        'nama_user' => $user->name,
        'divisi' => 'IT',
        'tanggal_distribusi' => now()->toDateString(),
        'status' => 'dipakai',
        'keterangan' => 'test',
    ]);
    $item = Items::create([
        'kategori' => 'PC',
        'merk' => 'Dell',
        'type' => 'OptiPlex',
        'asset' => 'A-001',
        'serial_number' => 'SN-001',
        'service_tag' => 'ST-001',
        'processor' => 'Intel',
        'ram_gb' => 8,
        'storage_gb' => 256,
        'vga' => '-',
        'os' => 'Windows 11',
        'tahun' => 2024,
        'status' => 'used',
        'barang_masuk_id' => null,
        'storage_location_id' => null,
        'condition_note' => 'ok',
    ]);
    $distributionItem = DistributionItem::create([
        'distribution_id' => $distribution->id,
        'item_id' => $item->id,
        'status' => 'dipakai',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $response = $this->actingAs($user)
        ->post('/laporan-kendala', [
            'distribution_item_id' => $distributionItem->id,
            'title' => 'Monitor mati',
            'description' => 'Monitor tidak menyala setelah dipakai semalam.',
            'priority' => 'high',
            'evidence' => $file,
        ]);

    $response->assertRedirect();

    $report = IssueReport::latest()->first();
    expect($report)->not->toBeNull();
    expect($report->evidence_path)->not->toBeNull();
    Storage::disk('public')->assertExists($report->evidence_path);
});

it('stores an other category issue report without a device', function () {
    $user = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($user)
        ->post('/laporan-kendala', [
            'issue_category' => IssueReport::CATEGORY_OTHER,
            'title' => 'Jaringan ruangan lemot',
            'description' => 'Koneksi jaringan di ruangan sering putus dan lambat saat dipakai.',
            'priority' => 'normal',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('issue_reports', [
        'reporter_id' => $user->id,
        'issue_category' => IssueReport::CATEGORY_OTHER,
        'title' => 'Jaringan ruangan lemot',
        'distribution_item_id' => null,
        'item_id' => null,
    ]);
});
