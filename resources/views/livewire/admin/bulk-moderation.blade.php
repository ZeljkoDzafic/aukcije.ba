{{--
    Admin Bulk Moderation UI
    T-1550: Moderation queue sa checkbox selekcijom, bulk approve/reject
--}}

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Moderacija aukcija
        </h2>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">
                <strong>{{ $pendingAuctions->count() }}</strong> na čekanju
            </span>
        </div>
    </div>
    
    {{-- Bulk Actions Toolbar --}}
    @if($selectedAuctions->count() > 0)
        <div class="mb-6 p-4 bg-blue-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="font-medium text-blue-900">
                    Odabrano: {{ $selectedAuctions->count() }} aukcija
                </span>
                <button
                    wire:click="bulkApprove"
                    class="btn-primary text-sm"
                >
                    ✅ Odobri odabrane
                </button>
                <button
                    wire:click="bulkReject"
                    class="btn-danger text-sm"
                >
                    ❌ Odbij odabrane
                </button>
            </div>
            <button
                wire:click="clearSelection"
                class="text-blue-600 hover:text-blue-700 text-sm"
            >
                Poništi odabir
            </button>
        </div>
    @endif
    
    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="label">Status</label>
            <select wire:model="statusFilter" class="input">
                <option value="all">Svi</option>
                <option value="pending">Na čekanju</option>
                <option value="approved">Odobreni</option>
                <option value="rejected">Odbijeni</option>
            </select>
        </div>
        <div>
            <label class="label">Kategorija</label>
            <select wire:model="categoryFilter" class="input">
                <option value="">Sve</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Sortiraj</label>
            <select wire:model="sortBy" class="input">
                <option value="newest">Najnoviji</option>
                <option value="oldest">Najstariji</option>
                <option value="price_low">Cijena (niska)</option>
                <option value="price_high">Cijena (visoka)</option>
            </select>
        </div>
        <div class="flex items-end">
            <input
                type="text"
                wire:model.debounce.300ms="search"
                class="input"
                placeholder="Pretraži..."
            />
        </div>
    </div>
    
    {{-- Auctions Table --}}
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="w-12">
                        <input
                            type="checkbox"
                            wire:model="selectAll"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                    </th>
                    <th>Aukcija</th>
                    <th>Prodavac</th>
                    <th>Kategorija</th>
                    <th>Cijena</th>
                    <th>Datum</th>
                    <th>Status</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingAuctions as $auction)
                    <tr class="table-row">
                        <td>
                            <input
                                type="checkbox"
                                wire:model="selectedAuctions"
                                value="{{ $auction->id }}"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                        </td>
                        <td>
                            <div class="flex items-center space-x-3">
                                @if($auction->primaryImage)
                                    <img src="{{ $auction->primaryImage->url }}" alt="" class="w-12 h-12 object-cover rounded" />
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ Str::limit($auction->title, 50) }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $auction->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <span>{{ $auction->seller->name }}</span>
                                @if($auction->seller->hasRole('verified_seller'))
                                    <span class="text-blue-500">✓</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $auction->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="font-medium">
                            {{ number_format($auction->start_price, 2) }} KM
                        </td>
                        <td class="text-sm text-gray-600">
                            {{ $auction->created_at->diffForHumans() }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $auction->status === 'pending' ? 'warning' : ($auction->status === 'approved' ? 'success' : 'danger') }}">
                                {{ $auction->status }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <button
                                    wire:click="approveAuction({{ $auction->id }})"
                                    class="text-green-600 hover:text-green-700"
                                    title="Odobri"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="rejectAuction({{ $auction->id }})"
                                    class="text-red-600 hover:text-red-700"
                                    title="Odbij"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <a
                                    href="{{ route('admin.auctions.show', $auction) }}"
                                    class="text-blue-600 hover:text-blue-700"
                                    title="Pogledaj"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            Nema aukcija za moderaciju
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($pendingAuctions->hasPages())
        <div class="mt-6">
            {{ $pendingAuctions->links() }}
        </div>
    @endif
</div>
