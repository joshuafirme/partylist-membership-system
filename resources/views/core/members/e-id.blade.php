<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $member->name }} - Digital e-ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Hide everything except the ID card when printing */
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-card { 
                box-shadow: none !important; 
                border: 1px solid #e2e8f0 !important;
                margin: 0 !important;
                transform: scale(1) !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4 font-sans">

    <!-- Action Toolbar (Hidden on Print) -->
    <div class="no-print w-full max-w-sm flex justify-between items-center mb-6">
        <a href="{{ route('members.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors flex items-center text-sm font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
            <i class="fa-solid fa-print mr-2"></i> Print / Save PDF
        </button>
    </div>

    <!-- The ID Card Container -->
    <div class="print-card bg-white w-full max-w-[340px] rounded-2xl shadow-xl overflow-hidden relative border border-slate-200">
        
        <!-- Header / Banner Area -->
        <div class="bg-blue-600 px-6 py-6 text-center relative overflow-hidden">
            <!-- Decorative Background Element -->
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-10"></div>
            
            <h1 class="text-white font-bold text-lg tracking-wide uppercase relative z-10">Official Member</h1>
            <p class="text-blue-100 text-xs mt-1 relative z-10">Party-List System</p>
        </div>

        <!-- Profile Photo Overlapping Header -->
        <div class="flex justify-center -mt-12 relative z-20">
            <div class="w-24 h-24 bg-white rounded-full p-1 shadow-md">
                @if($member->profile_photo_path)
                    <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover rounded-full">
                @else
                    <!-- Fallback Avatar -->
                    <div class="w-full h-full rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-3xl font-bold uppercase">
                        {{ substr($member->name, 0, 1) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Member Details -->
        <div class="px-6 pt-4 pb-6 text-center">
            <h2 class="text-xl font-bold text-slate-900 leading-tight">{{ $member->name }}</h2>
            <p class="text-sm text-blue-600 font-semibold mt-1">{{ $member->role->name ?? 'Constituent' }}</p>
            
            <div class="mt-4 bg-slate-50 rounded-lg p-3 border border-slate-100 text-left">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">ID Number</span>
                    <span class="text-xs font-mono font-bold text-slate-800">{{ $member->membership_number }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Team</span>
                    <span class="text-xs font-medium text-slate-800">{{ $member->team->name ?? 'Unassigned' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Location</span>
                    <span class="text-xs font-medium text-slate-800 truncate max-w-[140px] text-right">
                        {{ $member->city ?? $member->province ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="px-6 pb-8 text-center flex flex-col items-center">
            <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-3">Scan for Attendance</p>
            
            <!-- This empty div is where qrcode.js will render the canvas -->
            <div id="qrcode" class="p-2 bg-white border-2 border-slate-100 rounded-xl inline-block shadow-sm"></div>
            
            <p class="text-[9px] text-slate-400 mt-3 font-medium">Valid for Official Party-List Events Only</p>
        </div>
        
        <!-- Status Strip at the bottom -->
        @if($member->status == 1)
            <div class="h-2 w-full bg-emerald-500"></div>
        @else
            <div class="h-2 w-full bg-red-500"></div>
        @endif

    </div>

    <!-- Generate QR Code Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Retrieve the token from the backend
            const token = "{{ $member->qr_token }}";
            
            // Generate the QR Code
            new QRCode(document.getElementById("qrcode"), {
                text: token,
                width: 140,
                height: 140,
                colorDark : "#0f172a", // slate-900
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H // High error correction for better scanning
            });
        });
    </script>
</body>
</html>