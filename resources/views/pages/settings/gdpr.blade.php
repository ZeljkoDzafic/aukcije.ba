{{--
    GDPR Settings UI
    T-1456: Na profilu: "Preuzmi podatke" i "Izbriši račun" sa potvrdom
--}}

<div class="card">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        🔒 Privatnost i GDPR
    </h2>
    
    <div class="space-y-6">
        {{-- Download Data --}}
        <div class="flex items-start justify-between p-4 bg-blue-50 rounded-lg">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-2">
                    📥 Preuzmi svoje podatke
                </h3>
                <p class="text-sm text-gray-600 mb-2">
                    Preuzmite kopiju svih svojih ličnih podataka u JSON formatu.
                </p>
                <p class="text-xs text-gray-500">
                    Rok: 24 sata | Format: JSON | Veličina: ~500KB
                </p>
            </div>
            <button
                wire:click="exportData"
                wire:loading.attr="disabled"
                class="btn-primary whitespace-nowrap"
            >
                <span wire:loading.remove>Preuzmi podatke</span>
                <span wire:loading>
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
        
        {{-- Data Export Status --}}
        @if($exportRequested)
            <div class="alert alert-info">
                <strong>⏳ Export u toku...</strong>
                <p class="text-sm mt-1">
                    Podaci će biti spremni za {{ $exportReadyAt->diffForHumans() }}. Poslat ćemo vam email.
                </p>
            </div>
        @endif
        
        @if($exportReady)
            <div class="alert alert-success">
                <strong>✅ Export spreman!</strong>
                <div class="mt-2">
                    <a href="{{ $exportUrl }}" class="btn-primary text-sm">
                        Preuzmi export
                    </a>
                </div>
            </div>
        @endif
        
        <hr class="border-gray-200">
        
        {{-- Delete Account --}}
        <div class="flex items-start justify-between p-4 bg-red-50 rounded-lg">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-2">
                    🗑️ Izbriši svoj račun
                </h3>
                <p class="text-sm text-gray-600 mb-2">
                    Trajno brisanje računa i svih ličnih podataka.
                </p>
                <p class="text-xs text-red-600 font-medium">
                    ⚠️ Ova akcija je nepovratna!
                </p>
            </div>
            <button
                wire:click="confirmDelete"
                class="btn-danger whitespace-nowrap"
            >
                Izbriši račun
            </button>
        </div>
        
        {{-- Delete Confirmation Modal --}}
        @if($showDeleteConfirm)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        Da li ste sigurni?
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Ovo će trajno izbrisati:
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 mb-6">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Vaš profil i lične podatke
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Sve vaše aukcije i ponude
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Pregled poruka i obavještenja
                        </li>
                    </ul>
                    <p class="text-sm text-gray-500 mb-6">
                        Napomena: Transakcioni podaci se čuvaju 7 godina zbog zakonskih obaveza.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            wire:click="cancelDelete"
                            class="btn-secondary"
                        >
                            Odustani
                        </button>
                        <button
                            wire:click="deleteAccount"
                            class="btn-danger"
                        >
                            Da, izbriši moj račun
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Privacy Settings --}}
        <div class="space-y-4">
            <h3 class="font-semibold text-gray-900">
                Postavke privatnosti
            </h3>
            
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">Profil vidljiv javnosti</p>
                    <p class="text-sm text-gray-600">Dozvoli drugima da vide vaš profil i reputaciju</p>
                </div>
                <input
                    type="checkbox"
                    wire:model="settings.profile_public"
                    wire:change="saveSettings"
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
            </label>
            
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">Email notifikacije</p>
                    <p class="text-sm text-gray-600">Primi emailove o aukcijama i ponudama</p>
                </div>
                <input
                    type="checkbox"
                    wire:model="settings.email_notifications"
                    wire:change="saveSettings"
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
            </label>
            
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">SMS notifikacije</p>
                    <p class="text-sm text-gray-600">Primi SMS o važnim obavijestima</p>
                </div>
                <input
                    type="checkbox"
                    wire:model="settings.sms_notifications"
                    wire:change="saveSettings"
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
            </label>
        </div>
    </div>
</div>
