{{--
    KYC Status Dashboard Component
    T-1152: KYC Status Dashboard
    Buyer/seller vidi nivo, šta fali, CTA za upload dokumenta
--}}

<div class="card">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        KYC verifikacija
    </h2>

    @if($statusMessage !== '')
        <div class="alert alert-success mb-4">{{ $statusMessage }}</div>
    @endif
    
    {{-- Current Level --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">
                Trenutni nivo: {{ $user->kyc_level }}/3
            </span>
            <span class="text-sm text-gray-500">
                {{ $levelNames[$user->kyc_level] }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div
                class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                style="width: {{ ($user->kyc_level / 3) * 100 }}%"
            ></div>
        </div>
    </div>
    
    {{-- Level Steps --}}
    <div class="space-y-4">
        {{-- Level 1: Email --}}
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                @if($user->email_verified_at)
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @else
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                        1
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Email verifikacija</h3>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                @if(!$user->email_verified_at)
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-1">
                        Pošalji ponovo
                    </button>
                @endif
            </div>
        </div>
        
        {{-- Level 2: Phone --}}
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                @if($user->phone_verified_at)
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @else
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                        2
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">SMS verifikacija</h3>
                @if($user->phone)
                    <p class="text-sm text-gray-600">{{ $user->phone }}</p>
                @endif
                @if(!$user->phone_verified_at)
                    <button
                        wire:click="sendSmsOtp"
                        class="btn-primary text-sm mt-2"
                    >
                        Verifikuj telefon
                    </button>
                @endif
            </div>
        </div>
        
        {{-- Level 3: Document --}}
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                @if($user->hasRole('verified_seller'))
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @else
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                        3
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Verifikacija dokumenta</h3>
                <p class="text-sm text-gray-600">
                    Upload lične karte ili pasoša
                </p>
                @if(!$user->hasRole('verified_seller'))
                    <button
                        wire:click="openDocumentUpload"
                        class="btn-primary text-sm mt-2"
                    >
                        Upload dokumenta
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Benefits --}}
    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
        <h3 class="font-semibold text-gray-900 mb-3">
            🎁 Prednosti verifikacije
        </h3>
        <ul class="space-y-2 text-sm text-gray-700">
            <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Nivo 1: Gledanje aukcija
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Nivo 2: Licitiranje i kupovina
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Nivo 3: Prodaja i isplate
            </li>
        </ul>
    </div>
</div>
