@extends('core.layouts.app')

@section('title', 'QR Scanner - System Portal')

@section('content')
    <div class="max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-slate-900">Live Attendance Scanner</h2>
            <p class="text-sm text-slate-500 mt-1">Select an active event and scan member e-ID QR codes to record time-in.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Scanner Container (Left) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full">
                    
                    <!-- Event Selection Header -->
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center gap-4">
                        <label class="font-medium text-slate-700 text-sm whitespace-nowrap">Target Event:</label>
                        <select id="eventSelect" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            <option value="">-- Select Event to Begin Scanning --</option>
                            @foreach($activeEvents as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->event_date->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Camera Viewport -->
                    <div class="p-6 flex-1 flex flex-col items-center justify-center bg-slate-900 relative">
                        <!-- Overlay when no event is selected -->
                        <div id="scannerOverlay" class="absolute inset-0 z-10 bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center text-center p-6 transition-opacity">
                            <div class="w-16 h-16 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                                <i class="fa-solid fa-calendar-check text-2xl"></i>
                            </div>
                            <h3 class="text-white font-medium text-lg mb-2">Select an Event</h3>
                            <p class="text-slate-400 text-sm max-w-sm">Please select a target event from the dropdown above to activate the camera.</p>
                        </div>
                        
                        <!-- The actual scanner div -->
                        <div id="reader" class="w-full max-w-lg mx-auto rounded-lg overflow-hidden shadow-2xl bg-black"></div>
                    </div>
                </div>
            </div>

            <!-- Scan Results Log (Right) -->
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm flex flex-col h-[500px] lg:h-auto">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-medium text-slate-800">Recent Scans</h3>
                    <span id="scanCount" class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">0</span>
                </div>
                
                <div id="scanLog" class="p-4 flex-1 overflow-y-auto space-y-3 custom-scrollbar">
                    <!-- Default Empty State -->
                    <div id="emptyLog" class="h-full flex flex-col items-center justify-center text-center opacity-50">
                        <i class="fa-solid fa-qrcode text-4xl text-slate-300 mb-3"></i>
                        <p class="text-sm text-slate-500">Scan results will appear here.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection

@push('script')
    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            let html5QrcodeScanner = null;
            const eventSelect = document.getElementById('eventSelect');
            const scannerOverlay = document.getElementById('scannerOverlay');
            const scanLog = document.getElementById('scanLog');
            const emptyLog = document.getElementById('emptyLog');
            const scanCountBadge = document.getElementById('scanCount');
            let scanCount = 0;
            let isProcessing = false; // Prevent double scanning

            // Initialize scanner settings
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // Handle dropdown change
            eventSelect.addEventListener('change', function() {
                if (this.value) {
                    scannerOverlay.style.opacity = '0';
                    setTimeout(() => scannerOverlay.classList.add('hidden'), 300);
                    startScanner();
                } else {
                    scannerOverlay.classList.remove('hidden');
                    setTimeout(() => scannerOverlay.style.opacity = '1', 10);
                    stopScanner();
                }
            });

            function startScanner() {
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            }

            function stopScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Ignore scans if we are already processing an API request or no event is selected
                if (isProcessing || !eventSelect.value) return;
                
                isProcessing = true;
                
                // Optional: Provide audio feedback
                // new Audio('/path-to-beep.mp3').play();

                processQR(decodedText);
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning
            }

            function processQR(qrToken) {
                const eventId = eventSelect.value;

                fetch('{{ route("scanner.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_token: qrToken,
                        event_id: eventId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    logResult(data);
                    
                    // Resume scanning after 1.5 seconds
                    setTimeout(() => {
                        isProcessing = false;
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    logResult({
                        status: 'error',
                        message: 'Network error. Please try again.'
                    });
                    
                    setTimeout(() => {
                        isProcessing = false;
                    }, 1500);
                });
            }

            function logResult(data) {
                if (emptyLog) emptyLog.style.display = 'none';

                let icon, bgClass, textClass;

                if (data.status === 'success') {
                    icon = '<i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>';
                    bgClass = 'bg-emerald-50 border-emerald-100';
                    textClass = 'text-emerald-800';
                    scanCount++;
                    scanCountBadge.innerText = scanCount;
                } else if (data.status === 'warning') {
                    icon = '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl"></i>';
                    bgClass = 'bg-amber-50 border-amber-100';
                    textClass = 'text-amber-800';
                } else {
                    icon = '<i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>';
                    bgClass = 'bg-red-50 border-red-100';
                    textClass = 'text-red-800';
                }

                // Format the user info string if available
                let userInfo = '';
                if (data.user) {
                    userInfo = `<div class="font-bold text-slate-800 mt-1">${data.user.name}</div>
                                <div class="text-xs text-slate-500">${data.user.team}</div>`;
                }

                const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                const logItem = `
                    <div class="flex items-start p-3 rounded-lg border ${bgClass} animate-fade-in-down">
                        <div class="mt-0.5 mr-3">${icon}</div>
                        <div class="flex-1">
                            <div class="text-sm font-medium ${textClass}">${data.message}</div>
                            ${userInfo}
                        </div>
                        <div class="text-xs text-slate-400 font-medium">${timestamp}</div>
                    </div>
                `;

                // Add to the top of the log
                scanLog.insertAdjacentHTML('afterbegin', logItem);
            }
        });
    </script>
    
    <style>
        /* Small animation for incoming log items */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out forwards;
        }
        
        /* Cleanup html5-qrcode default ugly UI */
        #reader { border: none !important; }
        #reader__dashboard_section_csr span { color: white !important; font-family: ui-sans-serif, system-ui, sans-serif !important; }
        #reader button { 
            background: #3b82f6 !important; 
            color: white !important; 
            border: none !important; 
            padding: 8px 16px !important; 
            border-radius: 6px !important; 
            font-weight: 500 !important;
            cursor: pointer;
            margin: 10px 0;
        }
        #reader a { color: #60a5fa !important; }
    </style>
@endpush