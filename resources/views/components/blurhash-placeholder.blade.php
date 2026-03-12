{{--
    Blurhash Image Placeholder Component
    Shows blurhash placeholder while image loads
--}}

@props([
    'src' => null,
    'blurhash' => null,
    'alt' => '',
    'width' => 800,
    'height' => 600,
    'class' => '',
])

<div 
    x-data="imageLoader('{{ $src }}', '{{ $blurhash }}')"
    class="relative overflow-hidden bg-gray-100 {{ $class }}"
    style="aspect-ratio: {{ $width }}/{{ $height }};"
>
    {{-- Blurhash Placeholder --}}
    <canvas
        x-ref="blurhashCanvas"
        class="absolute inset-0 w-full h-full object-cover"
        @load="decodeBlurhash"
    ></canvas>
    
    {{-- Actual Image --}}
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
        :class="loaded ? 'opacity-100' : 'opacity-0'"
        @load="loaded = true"
        loading="lazy"
    />
    
    {{-- Loading Skeleton --}}
    <div 
        x-show="!loaded"
        class="absolute inset-0 animate-pulse bg-gradient-to-r from-gray-100 via-gray-200 to-gray-100"
    ></div>
</div>

@push('scripts')
<script>
function imageLoader(src, blurhash) {
    return {
        loaded: false,
        blurhash: blurhash,
        
        init() {
            if (this.blurhash) {
                this.$nextTick(() => {
                    this.decodeBlurhash();
                });
            }
        },
        
        decodeBlurhash() {
            if (!this.blurhash || !this.$refs.blurhashCanvas) return;
            
            // In production, use blurhash library
            // For now, create a simple gradient placeholder
            const canvas = this.$refs.blurhashCanvas;
            const ctx = canvas.getContext('2d');
            
            // Set canvas size
            canvas.width = 32;
            canvas.height = 32;
            
            // Decode blurhash (simplified - use blurhash library in production)
            const colors = this.extractColors(this.blurhash);
            
            // Create gradient
            const gradient = ctx.createLinearGradient(0, 0, 32, 32);
            gradient.addColorStop(0, colors[0] || '#e5e7eb');
            gradient.addColorStop(0.5, colors[1] || '#f3f4f6');
            gradient.addColorStop(1, colors[2] || '#e5e7eb');
            
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, 32, 32);
        },
        
        extractColors(blurhash) {
            // Simplified color extraction
            // In production, use: import { decode } from 'blurhash'
            return ['#e5e7eb', '#f3f4f6', '#e5e7eb'];
        }
    }
}
</script>
@endpush

@push('styles')
<style>
/* Blurhash canvas should be pixelated for proper rendering */
canvas[x-ref="blurhashCanvas"] {
    image-rendering: pixelated;
    image-rendering: -moz-crisp-edges;
    image-rendering: crisp-edges;
}
</style>
@endpush
