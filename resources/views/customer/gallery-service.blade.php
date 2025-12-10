@extends('layouts.app')

@section('title', 'Gallery - ' . $serviceName)

@section('content')
<div class="max-w-7xl mx-auto pt-20">
    {{-- Header with back button --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customer.gallery') }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                ← Back to Gallery
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-emerald-900">{{ $serviceName }}</h1>
                <p class="text-gray-600">Gallery images for this service</p>
            </div>
        </div>
    </div>

    {{-- Gallery Images Grid --}}
    @if($images->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($images as $image)
                <div class="group cursor-pointer" onclick="openImageModal('{{ $image->url }}', '{{ $image->alt_text ?: $serviceName }}')">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <img src="{{ $image->url }}" 
                             alt="{{ $image->alt_text ?: $serviceName }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-full flex items-center justify-center bg-gray-200" style="display: none;">
                            <div class="text-center text-gray-500">
                                <i class="ri-image-2-line text-4xl mb-2"></i>
                                <p class="text-sm">Image not available</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-gray-400 text-8xl mb-6"><i class="ri-image-2-line"></i></div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">No Images Yet</h3>
            <p class="text-gray-600 max-w-md mx-auto">
                We're working on adding photos for this service. Check back soon!
            </p>
        </div>
    @endif
</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-[90vh] w-full">
        <button onclick="closeImageModal()" 
                class="absolute top-5 right-2 text-white text-3xl font-bold hover:text-gray-300 z-10 cursor-pointer bg-black/50 rounded-full w-10 h-10 flex items-center justify-center">
            ×
        </button>
        <img id="modalImage" src="" alt="" class="w-full h-full object-contain rounded-lg">
        <div id="modalCaption" class="text-white text-center mt-4 text-lg font-medium bg-black/50 rounded-lg px-4 py-2">
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc, caption) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalCaption').textContent = caption;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection
