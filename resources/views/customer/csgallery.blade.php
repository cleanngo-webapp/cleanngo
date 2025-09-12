@extends('layouts.app')

@section('title','Gallery')

@section('content')
<div class="max-w-7xl mx-auto pt-20">
    <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-extrabold text-emerald-900">Our Work Gallery</h1>
        <p class="mt-2 text-gray-600">See the quality of our cleaning services</p>
    </div>

    {{-- Services Grid with Gallery Images --}}
    <div class="space-y-12">
        @foreach($services as $service)
            @if(isset($galleryImages[$service['type']]) && $galleryImages[$service['type']]->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    {{-- Service Header --}}
                    <div class="bg-emerald-700 text-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold">{{ $service['name'] }}</h2>
                                <p class="text-white/90 mt-1">{{ $service['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ $galleryImages[$service['type']]->count() }}</div>
                                <div class="text-white/90 text-sm">{{ $galleryImages[$service['type']]->count() == 1 ? 'Image' : 'Images' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Gallery Images Grid --}}
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($galleryImages[$service['type']] as $image)
                                <div class="group cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $image->image_path) }}', '{{ $image->alt_text ?: $service['name'] }}')">
                                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $image->alt_text ?: $service['name'] }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- No Images Message --}}
    @if(empty($galleryImages))
        <div class="text-center py-16">
            <div class="text-gray-400 text-8xl mb-6">📷</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Gallery Coming Soon</h3>
            <p class="text-gray-600 max-w-md mx-auto">
                We're working on adding photos of our amazing work. Check back soon to see the quality of our cleaning services!
            </p>
        </div>
    @endif
</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 text-white text-2xl font-bold hover:text-gray-300 z-10">
            ×
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full rounded-lg">
        <div id="modalCaption" class="text-white text-center mt-4"></div>
    </div>
</div>

<script>
function openImageModal(imageSrc, caption) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalCaption').textContent = caption;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
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


