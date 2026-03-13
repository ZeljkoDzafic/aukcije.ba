{{--
    2FA Enrollment UI (Livewire Component)
    T-1151: 2FA Enrollment UI
    QR kod → unos koda → backup codes
--}}

<div class="max-w-2xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Dvofaktorska autentifikacija (2FA)
        </h2>

        @if($statusMessage !== '')
            <div class="alert alert-success mb-4">{{ $statusMessage }}</div>
        @endif
        
        {{-- Already Enabled --}}
        @if($alreadyEnabled)
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    2FA je već aktiviran
                </h3>
                <p class="text-gray-600 mb-4">
                    Vaš nalog je već zaštićen dvofaktorskom autentifikacijom.
                </p>
                <button wire:click="disable2FA" class="btn-danger">
                    Deaktiviraj 2FA
                </button>
            </div>
        @else
            {{-- Step 1: Show QR Code --}}
            @if($step === 1)
                <div class="space-y-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            Skenirajte QR kod sa Google Authenticator aplikacijom
                        </p>
                        
                        {{-- QR Code --}}
                        <div class="inline-block p-6 bg-white rounded-lg border-2 border-gray-200">
                            <div class="text-xs uppercase tracking-[0.3em] text-gray-400">Tajni ključ</div>
                            <div class="mt-3 font-mono text-lg text-gray-900">
                                {{ $qrCodeSecret }}
                            </div>
                        </div>
                        
                        {{-- Manual Entry --}}
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">
                                Ili unesite kod ručno:
                            </p>
                            <code class="px-3 py-2 bg-gray-100 rounded text-lg font-mono">
                                {{ $qrCodeSecret }}
                            </code>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button wire:click="nextStep" class="btn-primary">
                            Dalje
                        </button>
                    </div>
                </div>
            @endif
            
            {{-- Step 2: Verify Code --}}
            @if($step === 2)
                <div class="space-y-6">
                    <div>
                        <label class="label" for="code">
                            Unesite 6-znamenkasti kod iz aplikacije
                        </label>
                        <input
                            type="text"
                            id="code"
                            wire:model="code"
                            wire:input="verifyCode"
                            maxlength="6"
                            class="input text-center text-2xl tracking-widest"
                            placeholder="000000"
                            autofocus
                        />
                        
                        @error('code')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-between">
                        <button wire:click="previousStep" class="btn-secondary">
                            Nazad
                        </button>
                        <button wire:click="enable2FA" class="btn-primary">
                            Aktiviraj 2FA
                        </button>
                    </div>
                </div>
            @endif
            
            {{-- Step 3: Backup Codes --}}
            @if($step === 3)
                <div class="space-y-6">
                    <div class="alert alert-success">
                        <strong>✅ 2FA je uspješno aktiviran!</strong>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">
                            📋 Backup kodovi
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Sačuvajte ove kodove na sigurnom mjestu. Koristite ih ako izgubite pristup telefonu.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-2 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                            @foreach($backupCodes as $code)
                                <code class="text-sm font-mono text-gray-900">
                                    {{ $code }}
                                </code>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 flex justify-between">
                            <button wire:click="downloadBackupCodes" class="btn-secondary">
                                Preuzmi kodove
                            </button>
                            <button wire:click="finish" class="btn-primary">
                                Završi
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
