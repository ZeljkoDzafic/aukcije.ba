{{--
    KYC Backoffice UI
    T-1551: Admin pregled dokumenata sa lightbox, approve/reject, status history
--}}

<div class="space-y-6">
    @if ($statusMessage !== '')
        <x-alert variant="success">{{ $statusMessage }}</x-alert>
    @endif

    <x-card class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            KYC Verifikacije
        </h2>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">
                <strong>{{ $pendingKyc->count() }}</strong> na čekanju
            </span>
            <select wire:model.live="statusFilter" class="input text-sm">
                <option value="pending">Na čekanju</option>
                <option value="approved">Odobreno</option>
                <option value="rejected">Odbijeno</option>
            </select>
        </div>
    </div>
    
    {{-- KYC Queue --}}
    <div class="space-y-4">
        @forelse($pendingKyc as $kyc)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden">
                            @if($kyc->user->profile?->avatar_url)
                                <img src="{{ $kyc->user->profile->avatar_url }}" alt="" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">
                                    {{ strtoupper(substr($kyc->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $kyc->user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $kyc->user->email }}</p>
                            <p class="text-xs text-gray-500">
                                Prijavljen: {{ $kyc->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-badge variant="warning">{{ strtoupper($kyc->status) }}</x-badge>
                    </div>
                </div>
                
                {{-- Documents Preview --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach($kyc->documents as $document)
                        <div class="relative group">
                            <img
                                src="{{ $document->url }}"
                                alt="KYC Document"
                                class="w-full h-48 object-cover rounded-lg border border-gray-200"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center">
                                <button wire:click="viewDocument('{{ $document->url }}')" class="opacity-0 group-hover:opacity-100 text-white font-medium">
                                    Uvećaj
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Quick Actions --}}
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ count($kyc->documents) }} dokumenata</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="approveKyc('{{ $kyc->id }}')" class="btn-success text-sm">Odobri</button>
                        <button wire:click="rejectKyc('{{ $kyc->id }}')" class="btn-danger text-sm">Odbij</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Nema KYC zahtjeva za pregled</p>
            </div>
        @endforelse
    </div>
    
    {{-- Document Viewer Modal --}}
    @if($showDocumentViewer)
        <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4">
            <div class="relative max-w-6xl w-full">
                <button wire:click="closeDocumentViewer" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img
                    src="{{ $selectedDocument }}"
                    alt="Document"
                    class="w-full h-auto rounded-lg"
                />
            </div>
        </div>
    @endif
    
    {{-- Pagination --}}
    </x-card>
</div>
