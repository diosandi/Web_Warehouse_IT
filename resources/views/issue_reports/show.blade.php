@extends('layouts.app')

@section('content')
@php
    $isManager = Auth::user()->canManageIssueReports();
    $statusClasses = [
        'open' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'resolved' => 'bg-green-100 text-green-800',
        'closed' => 'bg-gray-100 text-gray-800',
    ];
    $priorityClasses = [
        'low' => 'bg-gray-100 text-gray-800',
        'normal' => 'bg-blue-100 text-blue-800',
        'high' => 'bg-orange-100 text-orange-800',
        'urgent' => 'bg-red-100 text-red-800',
    ];
@endphp

<br>
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $issueReport->ticket_number }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $issueReport->title }}</p>
        </div>
        <a href="{{ route('issue_reports.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
                <div class="flex flex-wrap gap-2 mb-5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$issueReport->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                        {{ $issueReport->status_label }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $priorityClasses[$issueReport->priority] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                        {{ $issueReport->priority_label }}
                    </span>
                </div>

                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-3">Deskripsi Kendala</h2>
                <p class="text-sm leading-6 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $issueReport->description }}</p>

                @if($issueReport->evidence_path)
                    @php
                        $evidenceExtension = strtolower(pathinfo($issueReport->evidence_path, PATHINFO_EXTENSION));
                        $isVideoEvidence = in_array($evidenceExtension, ['mp4', 'webm', 'avi', 'mov', 'mkv'], true);
                    @endphp
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Bukti / Foto / Video</h3>
                        @if($isVideoEvidence)
                            <video controls class="w-full max-h-96 rounded-lg border border-gray-200 dark:border-gray-600 bg-black dark:bg-gray-100">
                                <source src="{{ asset('storage/' . $issueReport->evidence_path) }}" type="video/{{ $evidenceExtension === 'mkv' ? 'x-matroska' : $evidenceExtension }}">
                                Browser kamu tidak mendukung pemutaran video.
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $issueReport->evidence_path) }}" alt="Bukti laporan" class="max-h-96 rounded-lg border border-gray-200 object-contain">
                        @endif
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Live Messages</h2>
                @php
                    $chatNoticeClasses = $canSendMessages
                        ? 'border-green-200 bg-green-50 text-green-800'
                        : ($issueReport->status === \App\Models\IssueReport::STATUS_OPEN
                            ? 'border-yellow-200 bg-yellow-50 text-yellow-800'
                            : 'border-gray-200 bg-gray-50 text-gray-700');
                @endphp
                <div class="mb-4 rounded-lg border px-4 py-3 text-sm {{ $chatNoticeClasses }}">
                    {{ $canSendMessages ? 'Chat aktif selama laporan berstatus Diproses.' : $messageLockReason }}
                </div>
                <div id="messages-container" class="space-y-3 max-h-96 overflow-y-auto pr-2" data-initial-messages='@json($initialMessages)'>
                    @forelse($initialMessages as $message)
                        @php
                            $isMineMessage = ($message['sender']['id'] ?? null) === Auth::id();
                        @endphp
                        <div class="max-w-[85%] {{ $isMineMessage ? 'ml-auto' : 'mr-auto' }}" data-message-id="{{ $message['id'] }}">
                            <div class="rounded-2xl px-3 py-2 {{ $isMineMessage ? 'bg-green-600 text-white ml-auto' : 'bg-gray-100 text-gray-800 mr-auto' }}">
                                <p class="text-xs font-semibold opacity-80">{{ $message['sender']['name'] ?? 'System' }}</p>
                                <p class="text-sm whitespace-pre-line">{{ $message['message'] }}</p>
                                <p class="text-xs mt-1 opacity-75">{{ $message['created_at'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada pesan.</p>
                    @endforelse
                </div>
                <p id="message-status" class="mt-3 hidden text-sm"></p>
                @if($canSendMessages)
                    <form id="message-form" method="POST" action="{{ route('issue_reports.store_message', $issueReport) }}" class="mt-4 flex gap-2">
                        @csrf
                        <input type="text" id="message-input" name="message" placeholder="Ketik pesan untuk admin / client..." class="flex-1 px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                        <button type="submit" class="btn btn-success">Kirim</button>
                    </form>
                @else
                    <div class="mt-4 flex gap-2">
                        <input type="text" placeholder="Chat tidak aktif" class="flex-1 rounded-lg border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-500" disabled>
                        <button type="button" class="btn btn-secondary opacity-60 cursor-not-allowed" disabled>Kirim</button>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Detail Laporan</h2>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Pelapor</dt>
                        <dd class="font-semibold text-gray-800 dark:text-gray-100">{{ $issueReport->reporter->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Kategori Kendala</dt>
                        <dd class="font-semibold text-gray-800 dark:text-gray-100">{{ $issueReport->issue_category_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Perangkat</dt>
                        <dd class="text-gray-800 dark:text-gray-100">
                            {{ $issueReport->item->kategori ?? '-' }} - {{ $issueReport->item->serial_number ?? '-' }} - {{ $issueReport->item->merk ?? '-' }} - {{ $issueReport->item->type ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Lokasi</dt>
                        <dd class="uppercase text-gray-800 dark:text-gray-100">{{ $issueReport->location->gedung ?? '-' }} - {{ $issueReport->location->ruangan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tanggal Lapor</dt>
                        <dd class="text-gray-800 dark:text-gray-100">{{ $issueReport->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Diselesaikan Oleh</dt>
                        <dd class="text-gray-800 dark:text-gray-100">{{ $issueReport->resolver->name ?? '-' }}</dd>
                    </div>
                    @if($issueReport->admin_note)
                        <div class="rounded-lg border border-green-100 bg-green-50 p-4">
                            <dt class="text-xs font-semibold uppercase text-green-700">Catatan Admin</dt>
                            <dd class="mt-2 text-gray-800 dark:text-gray-100 whitespace-pre-line">{{ $issueReport->admin_note }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($isManager)
                <form method="POST" action="{{ route('issue_reports.update_status', $issueReport) }}" class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Update Antrian</h2>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Status</label>
                        <select name="status" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                            @foreach($statusOptions as $status => $label)
                                <option value="{{ $status }}" {{ old('status', $issueReport->status) === $status ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Prioritas</label>
                        <select name="priority" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                            @foreach($priorityOptions as $priority => $label)
                                <option value="{{ $priority }}" {{ old('priority', $issueReport->priority) === $priority ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Catatan Admin untuk Client</label>
                        <textarea name="admin_note" rows="5" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('admin_note', $issueReport->admin_note) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Simpan Status</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('messages-container');
        const form = document.getElementById('message-form');
        const input = document.getElementById('message-input');
        const status = document.getElementById('message-status');
        const submitButton = form?.querySelector('button[type="submit"]');
        const currentUser = @json(['id' => Auth::id(), 'name' => Auth::user()->name]);
        const fetchMessagesUrl = @json(route('issue_reports.fetch_messages', $issueReport));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let currentMessages = readInitialMessages();

        function setMessageStatus(message, isError = false) {
            if (!status) {
                return;
            }

            status.textContent = message;
            status.className = 'mt-3 text-sm ' + (isError ? 'text-red-600' : 'text-green-700');
            status.classList.toggle('hidden', !message);
        }

        function readInitialMessages() {
            if (!container) {
                return [];
            }

            try {
                const messages = JSON.parse(container.dataset.initialMessages || '[]');
                return Array.isArray(messages) ? messages : [];
            } catch (error) {
                console.error('Failed to parse initial messages', error);
                return [];
            }
        }

        function isMyMessage(message) {
            return message.sender && String(message.sender.id) === String(currentUser.id);
        }

        function appendMessageElement(message) {
            if (!container) {
                return;
            }

            const existingPlaceholder = container.querySelector('p.text-sm.text-gray-500');
            if (existingPlaceholder) {
                existingPlaceholder.remove();
            }

            const isMine = isMyMessage(message);
            const bubbleClass = isMine
                ? 'bg-green-600 text-white ml-auto'
                : 'bg-gray-100 text-gray-800 mr-auto';

            const wrapper = document.createElement('div');
            wrapper.className = 'max-w-[85%] ' + (isMine ? 'ml-auto' : 'mr-auto');
            if (message.id) {
                wrapper.dataset.messageId = message.id;
            } else if (message.temp_id) {
                wrapper.dataset.tempMessageId = message.temp_id;
            }

            const bubble = document.createElement('div');
            bubble.className = 'rounded-2xl px-3 py-2 ' + bubbleClass;

            const sender = document.createElement('p');
            sender.className = 'text-xs font-semibold opacity-80';
            sender.textContent = message.sender ? message.sender.name : 'System';

            const body = document.createElement('p');
            body.className = 'text-sm whitespace-pre-line';
            body.textContent = message.message || '';

            const timestamp = document.createElement('p');
            timestamp.className = 'text-xs mt-1 opacity-75';
            timestamp.textContent = message.created_at || '';

            bubble.appendChild(sender);
            bubble.appendChild(body);
            bubble.appendChild(timestamp);
            wrapper.appendChild(bubble);
            container.appendChild(wrapper);
            container.scrollTop = container.scrollHeight;
        }

        function setMessages(messages) {
            currentMessages = Array.isArray(messages) ? messages : [];
            renderMessages();
        }

        function upsertMessage(message, tempId = null) {
            const messageId = message.id ? String(message.id) : null;

            currentMessages = currentMessages.filter((item) => {
                if (tempId && item.temp_id === tempId) {
                    return false;
                }

                if (messageId && item.id && String(item.id) === messageId) {
                    return false;
                }

                return true;
            });

            currentMessages.push(message);
            renderMessages();
        }

        function removeTempMessage(tempId) {
            currentMessages = currentMessages.filter((message) => message.temp_id !== tempId);
            renderMessages();
        }

        function renderMessages() {
            if (!container) {
                return;
            }

            container.innerHTML = '';

            if (!currentMessages.length) {
                container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pesan.</p>';
                return;
            }

            currentMessages.forEach((message) => {
                appendMessageElement(message);
            });
        }

        async function fetchMessages() {
            try {
                const response = await fetch(`${fetchMessagesUrl}?t=${Date.now()}`, {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) {
                    throw new Error('Request failed with status ' + response.status);
                }

                const data = await response.json();
                setMessages(data);
            } catch (error) {
                console.error('Failed to fetch messages', error);
            }
        }

        form?.addEventListener('submit', async function (event) {
            if (!window.fetch || !window.URLSearchParams) {
                return;
            }

            event.preventDefault();

            const message = input?.value.trim();
            if (!message) {
                return;
            }

            const tempId = 'temp-' + Date.now();

            upsertMessage({
                temp_id: tempId,
                message,
                sender: {
                    id: currentUser.id,
                    name: currentUser.name,
                },
                created_at: 'Baru saja',
            });

            input.value = '';
            setMessageStatus('');
            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: new URLSearchParams({
                        message,
                        _token: csrfToken,
                    })
                });

                if (response.ok) {
                    const payload = await response.json().catch(() => null);
                    if (payload) {
                        upsertMessage(payload, tempId);
                    }
                    fetchMessages();
                } else {
                    removeTempMessage(tempId);
                    const errorData = await response.json().catch(() => null);
                    const firstValidationError = Object.values(errorData?.errors || {})[0]?.[0];
                    const errorMessage = errorData?.message || firstValidationError || 'Pesan gagal dikirim.';
                    setMessageStatus(errorMessage, true);
                    if (input && !input.value.trim()) {
                        input.value = message;
                    }
                }
            } catch (error) {
                console.error('Failed to send message', error);
                removeTempMessage(tempId);
                setMessageStatus('Pesan gagal dikirim. Coba lagi.', true);
                if (input && !input.value.trim()) {
                    input.value = message;
                }
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });

        renderMessages();
        fetchMessages();
        const pollTimer = window.setInterval(fetchMessages, 3000);

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                fetchMessages();
            }
        });

        window.addEventListener('beforeunload', function () {
            window.clearInterval(pollTimer);
        });
    });
</script>
@endsection
