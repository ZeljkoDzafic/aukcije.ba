{{--
    Cookie Consent Banner (GDPR Compliance)
    Shows cookie consent banner with category selection
--}}

@props(['position' => 'bottom'])

<div 
    x-data="cookieConsent()"
    x-show="!consentGiven"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-y-0"
    x-transition:leave-end="translate-y-full"
    class="fixed {{ $position === 'top' ? 'top-0' : 'bottom-0' }} left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg"
    style="display: none;"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Info Section --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    🍪 Kolačići (Cookies)
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Koristimo kolačiće kako bismo poboljšali rad platforme, mjerili upotrebu i prikazivali relevantniji sadržaj.
                    Odaberite koje kategorije želite prihvatiti.
                </p>
                
                {{-- Cookie Categories --}}
                <div class="space-y-3">
                    {{-- Necessary (Always On) --}}
                    <label class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                checked 
                                disabled
                                class="w-4 h-4 text-gray-400 border-gray-300 rounded"
                            >
                            <span class="ml-3 text-sm font-medium text-gray-900">
                                Neophodni
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">Uvijek aktivno</span>
                    </label>
                    
                    {{-- Analytics --}}
                    <label class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                x-model="consent.analytics"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-3 text-sm font-medium text-gray-900">
                                Analitika
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">Mjerenje performansi i ponašanja korisnika</span>
                    </label>
                    
                    {{-- Marketing --}}
                    <label class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                x-model="consent.marketing"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-3 text-sm font-medium text-gray-900">
                                Marketing
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">Remarketing i promotivne kampanje</span>
                    </label>
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex flex-col space-y-3">
                <button
                    @click="acceptAll"
                    class="w-full btn-primary py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Prihvati sve
                </button>
                
                <button
                    @click="acceptSelected"
                    class="w-full btn-secondary py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Prihvati odabrane
                </button>
                
                <button
                    @click="rejectAll"
                    class="w-full py-3 px-4 text-sm text-gray-600 hover:text-gray-900 font-medium focus:outline-none"
                >
                    Odbaci sve osim neophodnih
                </button>
                
                <a
                    href="{{ route('content.show', ['slug' => 'politika-privatnosti']) }}"
                    target="_blank"
                    class="text-xs text-blue-600 hover:text-blue-800 underline text-center"
                >
                    Politika privatnosti
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cookieConsent() {
    return {
        consentGiven: false,
        consent: {
            necessary: true,
            analytics: false,
            marketing: false,
        },
        
        init() {
            // Check if consent already given
            const savedConsent = localStorage.getItem('cookie_consent');
            if (savedConsent) {
                this.consent = JSON.parse(savedConsent);
                this.consentGiven = true;
            } else {
                // Show banner after 2 seconds
                setTimeout(() => {
                    this.$el.style.display = 'block';
                }, 2000);
            }
        },
        
        acceptAll() {
            this.consent = {
                necessary: true,
                analytics: true,
                marketing: true,
            };
            this.saveConsent();
        },
        
        acceptSelected() {
            this.consent.necessary = true; // Always necessary
            this.saveConsent();
        },
        
        rejectAll() {
            this.consent = {
                necessary: true,
                analytics: false,
                marketing: false,
            };
            this.saveConsent();
        },
        
        saveConsent() {
            localStorage.setItem('cookie_consent', JSON.stringify(this.consent));
            this.consentGiven = true;
            this.$el.style.display = 'none';
            
            // Send consent to backend
            fetch('/cookie-consent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(this.consent),
            });
            
            // Initialize tracking scripts based on consent
            this.initializeTracking();
        },
        
        initializeTracking() {
            // Google Analytics
            if (this.consent.analytics && typeof gtag === 'function') {
                gtag('consent', 'update', {
                    'analytics_storage': 'granted'
                });
            }
            
            // Facebook Pixel
            if (this.consent.marketing && typeof fbq === 'function') {
                fbq('consent', 'update');
            }
        }
    }
}
</script>
@endpush
