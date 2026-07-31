<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <title>{{ config('app.name', 'Warehouse IT RSCM') }}</title> --}}
    <title>IT Maintenance RSCM</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<style>
        body {
            overflow-x: hidden;
        }

        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* Desktop: Adjust main content when sidebar is visible (not hidden) */
        @media (min-width: 768px) {
            #main-content:not(.sidebar-hidden) {
                margin-left: 16rem;
            }
            #main-content.sidebar-hidden {
                margin-left: 0;
            }
        }

        /* Mobile: Always no margin */
        @media (max-width: 767px) {
            #main-content {
                margin-left: 0;
            }
        }

         /* Efek blur pada body */
        .is-blurred {
            filter: blur(10px);
            transition: filter 0.5s ease;
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex flex-col h-screen">
        <!-- Include Navbar (Fixed Height) -->
        @include('layouts.navbar')

        <!-- Main Container (Flex grow) -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Include Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <main id="main-content" class="flex-1 overflow-y-auto">
                <div class="p-4 md:p-8">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Include Footer -->
        @include('layouts.footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const overlay = document.getElementById('sidebar-overlay');

            if (!toggle || !sidebar) {
                console.error('Sidebar elements not found');
                return;
            }

            // Function to set sidebar visibility state
            function setSidebarHidden(isHidden) {
                if (isHidden) {
                    sidebar.classList.add('-translate-x-full');
                    if (mainContent) mainContent.classList.add('sidebar-hidden');
                    if (overlay) overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    if (mainContent) mainContent.classList.remove('sidebar-hidden');
                    if (overlay && window.innerWidth < 768) {
                        overlay.classList.remove('hidden');
                    }
                }
            }

            // Function to get whether sidebar should be hidden
            function shouldSidebarBeHidden() {
                return sidebar.classList.contains('-translate-x-full');
            }

            // Toggle sidebar visibility (works on ALL screen sizes)
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const isCurrentlyHidden = shouldSidebarBeHidden();
                setSidebarHidden(!isCurrentlyHidden);
            });

            // Close sidebar when clicking on overlay
            if (overlay) {
                overlay.addEventListener('click', function () {
                    setSidebarHidden(true);
                });
            }

            // Close sidebar when clicking on a link (mobile only)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        setSidebarHidden(true);
                    }
                });
            });

            // Handle resize events - restore default state based on screen size
            let resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () {
                    if (window.innerWidth >= 768) {
                        // Desktop: show sidebar by default (unless user toggled it off)
                        // Check if sidebar was manually hidden
                        const wasManuallyHidden = sidebar.hasAttribute('data-manually-hidden');
                        if (!wasManuallyHidden) {
                            setSidebarHidden(false);
                        }
                    } else {
                        // Mobile: hide sidebar by default
                        setSidebarHidden(true);
                    }
                }, 250);
            });

            // Initial state on page load
            if (window.innerWidth < 768) {
                setSidebarHidden(true);
            } else {
                setSidebarHidden(false);
            }

            // Track manual toggle to remember user preference
            const originalToggle = toggle.onclick;
            toggle.addEventListener('click', function () {
                if (shouldSidebarBeHidden()) {
                    sidebar.removeAttribute('data-manually-hidden');
                } else {
                    sidebar.setAttribute('data-manually-hidden', 'true');
                }
            });

            // Prevent body scroll when sidebar is open on mobile
            sidebar.addEventListener('transitionend', function () {
                if (window.innerWidth < 768) {
                    if (shouldSidebarBeHidden()) {
                        document.body.style.overflow = 'auto';
                    } else {
                        document.body.style.overflow = 'hidden';
                    }
                }
            });
        });

         (function() {
                let timeout;
                const idleTime = 5 * 60 * 1000; // 5 menit

                function applyBlur() {
                    document.body.classList.add('is-blurred');
                }

                function removeBlur() {
                    if (document.body.classList.contains('is-blurred')) {
                        document.body.classList.remove('is-blurred');
                    }
                    resetTimer();
                }

                function resetTimer() {
                    clearTimeout(timeout);
                    timeout = setTimeout(applyBlur, idleTime);
                }

                // List kejadian yang dianggap sebagai "Aktivitas"
                const activityEvents = [
                    'mousedown', 'mousemove', 'keydown',
                    'scroll', 'touchstart', 'click'
                ];

                // Daftarkan semua event ke window
                activityEvents.forEach(function(eventName) {
                    window.addEventListener(eventName, removeBlur, true);
                });

                // Jalankan timer saat halaman pertama kali dibuka
                resetTimer();
            })();
    </script>

</body>
</html>
