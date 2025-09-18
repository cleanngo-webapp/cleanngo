@extends('layouts.admin')

@section('title','Gallery Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-emerald-900">Gallery Management</h1>
        <p class="text-gray-600">Manage Gallery Images for each service</p>
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

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $service)
            <div onclick="window.location='{{ route('admin.gallery.service', $service['type']) }}'" 
                 class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col hover:shadow-xl transition-shadow duration-300">
                
                {{-- Service Image --}}
                <div class="aspect-[4/3] bg-white relative">
                    <img src="{{ asset('assets/' . $service['image']) }}" 
                         alt="{{ $service['name'] }}" 
                         class="w-full h-full object-cover">
                    
                    {{-- Image Count Badge --}}
                    <div class="absolute top-3 right-3 bg-emerald-600 text-white px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $service['image_count'] }} {{ $service['image_count'] == 1 ? 'image' : 'images' }}
                    </div>
                </div>
                
                {{-- Service Info --}}
                <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="text-lg font-semibold">{{ $service['name'] }}</div>
                        <p class="text-white/90 text-sm mt-2">{{ $service['description'] }}</p>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="mt-4 flex justify-center gap-2">
                        <span class="inline-block bg-white text-emerald-700 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100 transition-colors duration-200 cursor-pointer">
                            Manage Images
                        </span>
                        <button onclick="event.stopPropagation(); showCommentsModal('{{ $service['type'] }}', '{{ $service['name'] }}')" 
                                class="inline-block bg-emerald-600 text-white font-semibold px-4 py-2 rounded-full shadow hover:bg-emerald-500 transition-colors duration-200 cursor-pointer">
                                <i class="ri-chat-3-line mr-1"></i>Comments
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Instructions --}}
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">How to use Gallery Management:</h3>
        <ul class="text-blue-800 space-y-1">
            <li>• Click on any service card above to manage its gallery images</li>
            <li>• Upload new images, edit existing ones, or delete unwanted images</li>
            <li>• Images will automatically appear in the customer gallery view</li>
            <li>• Use the sort order feature to control image display sequence</li>
        </ul>
    </div>
</div>

{{-- Comments Modal --}}
<div id="commentsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl w-full max-w-4xl p-6 m-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 id="modalServiceName" class="text-2xl font-bold text-gray-900">Service Comments</h2>
            <button onclick="closeCommentsModal()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold cursor-pointer">
                ✕
            </button>
        </div>
        
        {{-- Comments List with Preloader --}}
        <div id="commentsList" class="space-y-4">
            {{-- Preloader will be shown here while loading --}}
        </div>
    </div>
</div>

{{-- Delete Comment Confirmation Modal --}}
<div id="deleteCommentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl w-full max-w-md p-6 m-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Delete Comment</h3>
            <button onclick="closeDeleteCommentModal()" class="text-gray-500 hover:text-gray-700 text-xl font-bold cursor-pointer">
                ✕
            </button>
        </div>
        
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-900 font-medium">Are you sure you want to delete this comment?</p>
                    <p class="text-sm text-gray-500">This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Customer:</span> <span id="deleteCommentCustomer"></span>
                </p>
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteCommentModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 cursor-pointer">
                Cancel
            </button>
            <button onclick="confirmDeleteComment()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 cursor-pointer">
                Delete Comment
            </button>
        </div>
    </div>
</div>

{{-- Preloader Template --}}
<div id="preloaderTemplate" class="hidden">
    <div class="space-y-4">
        <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-gray-200 rounded-full loading-dots"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded loading-dots w-1/4"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-1/2"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-3/4"></div>
            </div>
        </div>
        <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-gray-200 rounded-full loading-dots"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded loading-dots w-1/3"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-2/3"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-1/2"></div>
            </div>
        </div>
        <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-gray-200 rounded-full loading-dots"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded loading-dots w-1/4"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded loading-dots w-1/2"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for comments functionality
let currentServiceType = null;
let currentServiceName = null;
let commentToDelete = null;

// Show comments modal with preloader
function showCommentsModal(serviceType, serviceName) {
    currentServiceType = serviceType;
    currentServiceName = serviceName;
    
    // Update modal title
    document.getElementById('modalServiceName').textContent = serviceName + ' - Customer Comments';
    
    // Show modal
    const modal = document.getElementById('commentsModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Show preloader and load comments
    showPreloader();
    loadComments(serviceType);
}

// Close comments modal
function closeCommentsModal() {
    const modal = document.getElementById('commentsModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Show preloader animation
function showPreloader() {
    const commentsList = document.getElementById('commentsList');
    const preloaderTemplate = document.getElementById('preloaderTemplate');
    
    // Clone the preloader template and show it
    const preloaderClone = preloaderTemplate.cloneNode(true);
    preloaderClone.classList.remove('hidden');
    preloaderClone.id = 'activePreloader';
    
    commentsList.innerHTML = '';
    commentsList.appendChild(preloaderClone);
}

// Load comments for a service
async function loadComments(serviceType) {
    console.log('Loading comments for service:', serviceType);
    
    try {
        // Make API call to get comments
        const response = await fetch(`/admin/service-comments/${serviceType}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Comments response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Comments data received:', data);
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        // Display comments
        displayComments(data.comments || []);
        
    } catch (error) {
        console.error('Error loading comments:', error);
        displayError('Failed to load comments. Please try again.');
    }
}

// Display comments in the modal
function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    
    if (comments.length === 0) {
        commentsList.innerHTML = `
            <div class="text-center py-12">
                <div class="flex justify-center items-center mb-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Comments Yet</h3>
                <p class="text-gray-500">This service doesn't have any customer comments yet.</p>
            </div>
        `;
        return;
    }
    
    console.log('Rendering', comments.length, 'comments');
    
    // Create comments HTML
    const commentsHtml = comments.map(comment => `
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    ${comment.customer_avatar ? 
                        `<img src="${comment.customer_avatar}" alt="${comment.customer_name || 'Anonymous'}" class="w-10 h-10 rounded-full object-cover border-2 border-emerald-200">` :
                        `<div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <span class="text-emerald-600 font-semibold text-sm">
                                ${comment.customer_name ? comment.customer_name.charAt(0).toUpperCase() : 'A'}
                            </span>
                        </div>`
                    }
                    <div>
                        <div class="font-semibold text-gray-900">${comment.customer_name || 'Anonymous'}</div>
                        ${comment.rating ? `
                            <div class="flex text-yellow-400 text-sm">
                                ${'★'.repeat(comment.rating)}${'☆'.repeat(5-comment.rating)}
                            </div>
                        ` : ''}
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">${comment.formatted_date}</div>
                        ${comment.is_edited ? '<div class="text-xs text-gray-400">(edited)</div>' : ''}
                    </div>
                    <button onclick="showDeleteCommentModal(${comment.id}, '${comment.customer_name || 'Anonymous'}')" 
                            class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors duration-200 cursor-pointer"
                            title="Delete Comment">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="text-gray-700 leading-relaxed">
                ${comment.comment}
            </div>
        </div>
    `).join('');
    
    commentsList.innerHTML = commentsHtml;
}

// Display error message
function displayError(message) {
    const commentsList = document.getElementById('commentsList');
    commentsList.innerHTML = `
        <div class="text-center py-12">
            <div class="flex justify-center items-center mb-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Comments</h3>
            <p class="text-red-500">${message}</p>
            <button onclick="loadComments(currentServiceType)" class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                Try Again
            </button>
        </div>
    `;
}

// Close modal when clicking outside
document.getElementById('commentsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCommentsModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCommentsModal();
        closeDeleteCommentModal();
    }
});

// Delete Comment Modal Functions
function showDeleteCommentModal(commentId, customerName) {
    commentToDelete = commentId;
    document.getElementById('deleteCommentCustomer').textContent = customerName;
    
    const modal = document.getElementById('deleteCommentModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteCommentModal() {
    const modal = document.getElementById('deleteCommentModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    commentToDelete = null;
    
    // Reset button state when closing modal
    const deleteButton = document.querySelector('#deleteCommentModal button[onclick="confirmDeleteComment()"]');
    if (deleteButton) {
        deleteButton.textContent = 'Delete Comment';
        deleteButton.disabled = false;
    }
}

async function confirmDeleteComment() {
    if (!commentToDelete) return;
    
    try {
        // Show loading state
        const deleteButton = document.querySelector('#deleteCommentModal button[onclick="confirmDeleteComment()"]');
        const originalText = deleteButton.textContent;
        deleteButton.textContent = 'Deleting...';
        deleteButton.disabled = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        // Make API call to delete comment
        const response = await fetch(`/admin/service-comments/${commentToDelete}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Close delete modal
            closeDeleteCommentModal();
            
            // Reload comments to reflect the deletion
            loadComments(currentServiceType);
            
            // Show success message
            showNotification('Comment deleted successfully!', 'success');
        } else {
            throw new Error(result.error || 'Failed to delete comment');
        }
        
        // Reset button state on success
        deleteButton.textContent = 'Delete Comment';
        deleteButton.disabled = false;
        
    } catch (error) {
        console.error('Error deleting comment:', error);
        showNotification('Failed to delete comment: ' + error.message, 'error');
        
        // Reset button state
        const deleteButton = document.querySelector('#deleteCommentModal button[onclick="confirmDeleteComment()"]');
        deleteButton.textContent = 'Delete Comment';
        deleteButton.disabled = false;
    }
}

// Simple notification function (you can replace this with your preferred notification system)
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Close delete comment modal when clicking outside
document.getElementById('deleteCommentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteCommentModal();
    }
});
</script>
@endsection


