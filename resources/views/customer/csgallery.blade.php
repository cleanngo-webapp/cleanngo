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
                                {{-- View All Button --}}
                                <a href="{{ route('customer.gallery.service', $service['type']) }}" 
                                   class="mt-3 inline-block bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-sm font-medium cursor-pointer">
                                    View All →
                                </a>
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
    // Set the image source and caption
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalCaption').textContent = caption;
    
    // Show the modal by removing hidden class and adding flex
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    
    // Prevent body scrolling when modal is open
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    // Hide the modal by adding hidden class and removing flex
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    
    // Restore body scrolling
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


