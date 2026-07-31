<?php

use App\Models\IssueReport;
use App\Models\IssueReportMessage;
use App\Models\User;

it('stores a live message for an issue report', function () {
    $reporter = User::factory()->create(['role' => 'client', 'username' => 'client_live']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_live']);

    $report = IssueReport::create([
        'reporter_id' => $reporter->id,
        'title' => 'Monitor error',
        'description' => 'Monitor tidak menyala saat dinyalakan.',
        'priority' => 'high',
        'status' => IssueReport::STATUS_IN_PROGRESS,
    ]);

    $response = $this->actingAs($admin)
        ->postJson('/laporan-kendala/' . $report->id . '/messages', [
            'message' => 'Saya sedang meninjau laporan ini.',
        ]);

    $response->assertCreated();
    $response->assertJsonPath('message', 'Saya sedang meninjau laporan ini.');
    $this->assertDatabaseHas('issue_report_messages', [
        'issue_report_id' => $report->id,
        'message' => 'Saya sedang meninjau laporan ini.',
    ]);
});

it('locks live messages while an issue report is still new', function () {
    $reporter = User::factory()->create(['role' => 'client', 'username' => 'client_locked_new']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_locked_new']);

    $report = IssueReport::create([
        'reporter_id' => $reporter->id,
        'title' => 'Mouse error',
        'description' => 'Mouse tidak bisa klik kiri.',
        'priority' => 'normal',
        'status' => IssueReport::STATUS_OPEN,
    ]);

    $response = $this->actingAs($admin)
        ->postJson('/laporan-kendala/' . $report->id . '/messages', [
            'message' => 'Saya cek dulu.',
        ]);

    $response->assertForbidden();
    $response->assertJsonPath('message', 'Chat akan dibuka saat status laporan Diproses.');
    $this->assertDatabaseMissing('issue_report_messages', [
        'issue_report_id' => $report->id,
        'message' => 'Saya cek dulu.',
    ]);
});

it('locks live messages after an issue report is finished', function () {
    $reporter = User::factory()->create(['role' => 'client', 'username' => 'client_locked_done']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_locked_done']);

    foreach ([IssueReport::STATUS_RESOLVED, IssueReport::STATUS_CLOSED] as $status) {
        $report = IssueReport::create([
            'reporter_id' => $reporter->id,
            'title' => 'Printer selesai ' . $status,
            'description' => 'Printer sudah selesai ditangani.',
            'priority' => 'normal',
            'status' => $status,
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/laporan-kendala/' . $report->id . '/messages', [
                'message' => 'Tambahan setelah selesai.',
        ]);

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Chat sudah ditutup karena laporan sudah selesai atau ditutup.');
        $this->assertDatabaseMissing('issue_report_messages', [
            'issue_report_id' => $report->id,
            'message' => 'Tambahan setelah selesai.',
        ]);
    }
});

it('fetches live messages from oldest to newest', function () {
    $reporter = User::factory()->create(['role' => 'client', 'username' => 'client_fetch']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_fetch']);

    $report = IssueReport::create([
        'reporter_id' => $reporter->id,
        'title' => 'Keyboard error',
        'description' => 'Keyboard tidak merespon saat digunakan.',
        'priority' => 'normal',
        'status' => IssueReport::STATUS_OPEN,
    ]);

    IssueReportMessage::create([
        'issue_report_id' => $report->id,
        'sender_id' => $reporter->id,
        'message' => 'Pesan pertama',
    ]);

    IssueReportMessage::create([
        'issue_report_id' => $report->id,
        'sender_id' => $admin->id,
        'message' => 'Pesan kedua',
    ]);

    $response = $this->actingAs($reporter)
        ->get('/laporan-kendala/' . $report->id . '/messages');

    $response->assertOk();
    $response->assertJsonPath('0.message', 'Pesan pertama');
    $response->assertJsonPath('1.message', 'Pesan kedua');
});

it('shows admin note to the reporting client', function () {
    $reporter = User::factory()->create(['role' => 'client', 'username' => 'client_note']);

    $report = IssueReport::create([
        'reporter_id' => $reporter->id,
        'title' => 'CPU panas',
        'description' => 'CPU terasa sangat panas setelah dipakai sebentar.',
        'priority' => 'high',
        'status' => IssueReport::STATUS_IN_PROGRESS,
        'admin_note' => 'Sudah dijadwalkan pengecekan oleh teknisi.',
    ]);

    $response = $this->actingAs($reporter)
        ->get('/laporan-kendala/' . $report->id);

    $response->assertOk();
    $response->assertSee('Catatan Admin');
    $response->assertSee('Sudah dijadwalkan pengecekan oleh teknisi.');
});
