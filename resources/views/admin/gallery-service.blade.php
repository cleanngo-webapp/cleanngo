@extends('layouts.admin')

@section('title', 'Gallery - ' . $serviceName)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header with back button --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.gallery') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                ← Back to Gallery
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-emerald-900">{{ $serviceName }}</h1>
                <p class="text-gray-600">Manage gallery images for this service</p>
            </div>
        </div>
    </div>

    {{-- Display success/error messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Upload Form --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload New Image</h2>
        
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="service_type" value="{{ $serviceType }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Image Upload --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Image <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/*" 
                           required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Supported formats: JPEG, PNG, JPG, GIF, WebP (Max: 10MB)</p>
                </div>

                {{-- Alt Text --}}
                <div>
                    <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Alt Text (Optional)
                    </label>
                    <input type="text" 
                           id="alt_text" 
                           name="alt_text" 
                           placeholder="Describe the image for accessibility"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            {{-- Upload Button --}}
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors duration-200 cursor-pointer">
                    Upload Image
                </button>
            </div>
        </form>
    </div>

    {{-- Existing Images --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Existing Images ({{ $images->count() }})</h2>
        
        @if($images->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($images as $image)
                    <div class="border border-gray-200 rounded-lg overflow-hidden {{ !$image->is_active ? 'opacity-50' : '' }}">
                        {{-- Image Preview --}}
                        <div class="aspect-square bg-gray-100">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $image->alt_text ?: $image->original_name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        
                        {{-- Image Info --}}
                        <div class="p-3">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $image->original_name }}</p>
                            <p class="text-xs text-gray-500">Order: {{ $image->sort_order }}</p>
                            @if($image->alt_text)
                                <p class="text-xs text-gray-600 mt-1">{{ Str::limit($image->alt_text, 50) }}</p>
                            @endif
                            
                            {{-- Status Badge --}}
                            <div class="mt-2">
                                @if($image->is_active)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Hidden</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div class="p-3 pt-0 space-y-2">
                            {{-- Edit Form (Inline) --}}
                            <form action="{{ route('admin.gallery.update', $image->id) }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PUT')
                                
                                <input type="text" 
                                       name="alt_text" 
                                       value="{{ $image->alt_text }}"
                                       placeholder="Alt text"
                                       class="w-full text-xs px-2 py-1 border border-gray-300 rounded">
                                
                                <div class="flex space-x-1">
                                    <input type="number" 
                                           name="sort_order" 
                                           value="{{ $image->sort_order }}"
                                           placeholder="Order"
                                           class="w-16 text-xs px-2 py-1 border border-gray-300 rounded cursor-pointer">
                                    
                                    <select name="is_active" class="text-xs px-2 py-1 border border-gray-300 rounded cursor-pointer">
                                        <option value="1" {{ $image->is_active ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$image->is_active ? 'selected' : '' }}>Hidden</option>
                                    </select>
                                </div>
                                
                                 <div class="flex space-x-1">
                                     <button type="button" 
                                             onclick="showUpdateModal({{ $image->id }})"
                                             class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs px-2 py-1 rounded transition-colors duration-200 cursor-pointer">
                                         Update
                                     </button>
                                     
                                     <button type="button" 
                                             onclick="showDeleteModal({{ $image->id }}, '{{ $image->original_name }}')"
                                             class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded transition-colors duration-200 cursor-pointer">
                                         Delete
                                     </button>
                                 </div>
                            </form>
                            
                            {{-- Delete Form (Hidden) --}}
                            <form id="delete-form-{{ $image->id }}" 
                                  action="{{ route('admin.gallery.destroy', $image->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">📷</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No images yet</h3>
                <p class="text-gray-500">Upload your first image using the form above.</p>
            </div>
         @endif
     </div>
 </div>

 {{-- Confirmation Modals --}}
 {{-- Delete Confirmation Modal --}}
 <div id="deleteModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" style="display: none;">
     <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
         <div class="p-6">
             <div class="flex items-center mb-4">
                 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                     <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                     </svg>
                 </div>
             </div>
             <div class="text-center">
                 <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Image</h3>
                 <p class="text-sm text-gray-500 mb-4">
                     Are you sure you want to delete <span id="deleteImageName" class="font-semibold"></span>? 
                     This action cannot be undone.
                 </p>
                 <div class="flex space-x-3 justify-center">
                     <button type="button" 
                             onclick="closeDeleteModal()"
                             class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                         Cancel
                     </button>
                     <button type="button" 
                             onclick="confirmDelete()"
                             class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                         Delete
                     </button>
                 </div>
             </div>
         </div>
     </div>
 </div>

 {{-- Update Confirmation Modal --}}
 <div id="updateModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" style="display: none;">
     <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
         <div class="p-6">
             <div class="flex items-center mb-4">
                 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                     <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                     </svg>
                 </div>
             </div>
             <div class="text-center">
                 <h3 class="text-lg font-medium text-gray-900 mb-2">Update Image</h3>
                 <p class="text-sm text-gray-500 mb-4">
                     Are you sure you want to update this image's details?
                 </p>
                 <div class="flex space-x-3 justify-center">
                     <button type="button" 
                             onclick="closeUpdateModal()"
                             class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                         Cancel
                     </button>
                     <button type="button" 
                             onclick="confirmUpdate()"
                             class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                         Update
                     </button>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <script>
 let currentImageId = null;
 let currentForm = null;

 // Delete Modal Functions
 function showDeleteModal(imageId, imageName) {
     currentImageId = imageId;
     document.getElementById('deleteImageName').textContent = imageName;
     const modal = document.getElementById('deleteModal');
     modal.style.display = 'flex';
     modal.classList.remove('hidden');
     document.body.style.overflow = 'hidden';
 }

 function closeDeleteModal() {
     const modal = document.getElementById('deleteModal');
     modal.style.display = 'none';
     modal.classList.add('hidden');
     document.body.style.overflow = 'auto';
     currentImageId = null;
 }

 function confirmDelete() {
     if (currentImageId) {
         document.getElementById('delete-form-' + currentImageId).submit();
     }
 }

 // Update Modal Functions
 function showUpdateModal(imageId) {
     currentImageId = imageId;
     currentForm = document.querySelector(`form[action*="/gallery/${imageId}"]`);
     const modal = document.getElementById('updateModal');
     modal.style.display = 'flex';
     modal.classList.remove('hidden');
     document.body.style.overflow = 'hidden';
 }

 function closeUpdateModal() {
     const modal = document.getElementById('updateModal');
     modal.style.display = 'none';
     modal.classList.add('hidden');
     document.body.style.overflow = 'auto';
     currentImageId = null;
     currentForm = null;
 }

 function confirmUpdate() {
     if (currentForm) {
         currentForm.submit();
     }
 }

 // Close modals when clicking outside
 document.getElementById('deleteModal').addEventListener('click', function(e) {
     if (e.target === this) {
         closeDeleteModal();
     }
 });

 document.getElementById('updateModal').addEventListener('click', function(e) {
     if (e.target === this) {
         closeUpdateModal();
     }
 });

 // Close modals with Escape key
 document.addEventListener('keydown', function(e) {
     if (e.key === 'Escape') {
         closeDeleteModal();
         closeUpdateModal();
     }
 });
 </script>
 @endsection
