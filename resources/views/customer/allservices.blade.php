@extends('layouts.app')

@section('title','All Services')

@section('content')
<div class="max-w-7xl mx-auto pt-20">
    <h1 class="text-2xl md:text-2xl font-extrabold text-emerald-900">OFFERED SERVICES</h1>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Carpet Deep Cleaning --}}
        <div onclick="window.location='{{ route('customer.services') }}#carpet'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-carpet-cleaning.webp') }}" alt="Carpet deep cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Carpet Deep Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Removes dirt and allergens to restore freshness and promote a healthier home.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#carpet" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                    <button onclick="event.stopPropagation(); showCommentsModal('carpet', 'Carpet Deep Cleaning')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Enhanced Disinfection --}}
        <div onclick="window.location='{{ route('customer.services') }}#disinfection'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-home-dis.webp') }}" alt="Enhanced Disinfection" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Enhanced Disinfection</div>
                    <p class="text-white/90 text-sm mt-2">Advanced disinfection for safer homes and workplaces.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#disinfection" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                    <button onclick="event.stopPropagation(); showCommentsModal('disinfection', 'Enhanced Disinfection')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>

        {{-- Glass Cleaning --}}
        <div onclick="window.location='{{ route('customer.services') }}#glass'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-glass-cleaning.webp') }}" alt="Glass Cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Glass Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Streak-free shine for windows and glass surfaces.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#glass" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                    <button onclick="event.stopPropagation(); showCommentsModal('glass', 'Glass Cleaning')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>

        {{-- Home Service Car Interior Detailing --}}
        <div onclick="window.location='{{ route('customer.services') }}#carInterior'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-car-detailing.webp') }}" alt="Home service car interior detailing" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Home Service Car Interior Detailing</div>
                    <p class="text-white/90 text-sm mt-2">Specialized deep cleaning right at your doorstep for a refreshed car interior.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#carInterior" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                    <button onclick="event.stopPropagation(); showCommentsModal('carInterior', 'Home Service Car Interior Detailing')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>
        
        
        {{-- Post Construction Cleaning --}}
        <div onclick="window.location='{{ route('customer.services') }}#postConstruction'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-post-cons-cleaning.webp') }}" alt="Post Construction Cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Post Construction Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Thorough cleanup to remove dust and debris for move-in ready spaces.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#postConstruction" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                    <button onclick="event.stopPropagation(); showCommentsModal('postConstruction', 'Post Construction Cleaning')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>

        {{-- Sofa/ Mattress Deep Cleaning --}}
        <div onclick="window.location='{{ route('customer.services') }}#sofa'" class="bg-white rounded-2xl shadow overflow-hidden flex flex-col cursor-pointer">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-sofa-mattress-cleaning.webp') }}" alt="Sofa/ mattress deep cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Sofa / Mattress Deep Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Eliminates dust, stains, and allergens to restore comfort and hygiene.</p>
                </div>
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('customer.services') }}#sofa" onclick="event.stopPropagation()" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                        <button onclick="event.stopPropagation(); showCommentsModal('sofa', 'Sofa / Mattress Deep Cleaning')" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-full shadow hover:shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="ri-chat-3-line mr-1"></i>Comments
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Comments Modal --}}
<div id="commentsModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-emerald-600 text-white p-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold" id="modalServiceName">Service Comments</h3>
            <button onclick="closeCommentsModal()" class="text-white hover:text-gray-200 text-xl font-bold cursor-pointer">
                ×
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-4 max-h-[60vh] overflow-y-auto">
            {{-- Add Comment Form --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg h-80 flex flex-col">
                <h4 class="text-md font-semibold text-gray-900 mb-3">Add Your Comment</h4>
                <form id="commentForm" class="space-y-3 flex-1 flex flex-col">
                    <input type="hidden" id="serviceType" name="service_type">
                    
                    {{-- Rating --}}
                    <div class="flex-shrink-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating (Optional)</label>
                        <div class="flex space-x-1" id="ratingStars">
                          <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl cursor-pointer" data-rating="1">★</button>
                          <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl cursor-pointer" data-rating="2">★</button>
                          <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl cursor-pointer" data-rating="3">★</button>
                          <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl cursor-pointer" data-rating="4">★</button>
                          <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl cursor-pointer" data-rating="5">★</button>
                        </div>
                        <input type="hidden" id="rating" name="rating">
                      </div>

                    {{-- Comment Text --}}
                    <div class="flex-1 flex flex-col">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Your Comment</label>
                        <textarea id="comment" name="comment" rows="3" 
                                  placeholder="Share your experience with this service..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none min-h-[80px] max-h-[120px] overflow-y-auto"
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 1 character, maximum 1000 characters</p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end flex-shrink-0">
                        <button type="submit" id="submitCommentBtn"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors duration-200 cursor-pointer">
                            <span id="submitText">Post Comment</span>
                            <span id="submitLoading" class="hidden">Posting...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Comments List --}}
            <div id="commentsList" class="space-y-4">
                {{-- Preloader will be shown here while loading --}}
            </div>
        </div>
    </div>
</div>

{{-- Edit Comment Modal --}}
<div id="editCommentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-hidden">
        <div class="p-6 h-full flex flex-col">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex-shrink-0">Edit Comment</h3>
            
            <form id="editCommentForm" class="space-y-4 flex-1 flex flex-col">
                <input type="hidden" id="editCommentId">
                
                {{-- Rating --}}
                <div class="flex-shrink-0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <div class="flex space-x-1" id="editRatingStars">
                        <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl" data-rating="1">★</button>
                        <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl" data-rating="2">★</button>
                        <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl" data-rating="3">★</button>
                        <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl" data-rating="4">★</button>
                        <button type="button" class="text-gray-300 hover:text-yellow-400 text-xl" data-rating="5">★</button>
                    </div>
                    <input type="hidden" id="editRating" name="rating">
                </div>

                {{-- Comment Text --}}
                <div class="flex-1 flex flex-col">
                    <label for="editComment" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                    <textarea id="editComment" name="comment" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none min-h-[80px] max-h-[200px] overflow-y-auto"
                              required></textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex space-x-3 justify-end flex-shrink-0">
                    <button type="button" 
                            onclick="closeEditCommentModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                        Update Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteCommentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Comment</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete this comment? This action cannot be undone.
                </p>
                <div class="flex space-x-3 justify-center">
                    <button type="button" 
                            onclick="closeDeleteCommentModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" 
                            onclick="confirmDeleteComment()"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                        Delete Comment
                    </button>
                </div>
            </div>
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
let currentServiceType = null;
let currentServiceName = null;
let commentToDelete = null;

// Show comments modal
function showCommentsModal(serviceType, serviceName) {
    currentServiceType = serviceType;
    currentServiceName = serviceName;
    
    document.getElementById('modalServiceName').textContent = serviceName + ' - Comments';
    document.getElementById('serviceType').value = serviceType;
    
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
    
    // Reset form
    document.getElementById('commentForm').reset();
    resetRating();
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
        const response = await fetch(`/service-comments/${serviceType}`);
        console.log('Comments response status:', response.status);
        
        const data = await response.json();
        console.log('Comments data received:', data);
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        displayComments(data.comments);
    } catch (error) {
        console.error('Error loading comments:', error);
        document.getElementById('commentsList').innerHTML = `
            <div class="text-center py-8">
                <div class="flex justify-center items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-red-500 text-sm">Failed to load comments. Please try again.</p>
            </div>
        `;
    }
}

// Display comments in the modal
function displayComments(comments) {
    console.log('Displaying comments:', comments);
    const commentsList = document.getElementById('commentsList');
    
    if (comments.length === 0) {
        console.log('No comments to display');
        commentsList.innerHTML = `
            <div class="text-center py-8">
                <div class="flex justify-center items-center mb-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-500 text-sm">No comments yet. Be the first to share your experience!</p>
            </div>
        `;
        return;
    }
    
    console.log('Rendering', comments.length, 'comments');
    
    commentsList.innerHTML = comments.map(comment => `
        <div class="border border-gray-200 rounded-lg p-4 fade-in-up">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center space-x-3">
                    ${comment.customer_avatar ? 
                        `<img src="${comment.customer_avatar}" alt="${comment.customer_name}" class="w-10 h-10 rounded-full object-cover border-2 border-emerald-200">` :
                        `<div class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-emerald-200 flex items-center justify-center">
                            <span class="text-sm font-semibold text-emerald-600">
                                ${comment.customer_name ? comment.customer_name.charAt(0).toUpperCase() : 'A'}
                            </span>
                        </div>`
                    }
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-900">${comment.customer_name}</span>
                        ${comment.rating ? `<div class="flex text-yellow-400 text-sm">${'★'.repeat(comment.rating)}${'☆'.repeat(5-comment.rating)}</div>` : ''}
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">${comment.formatted_date}</span>
                    ${comment.is_edited ? '<span class="text-xs text-gray-400">(edited)</span>' : ''}
                </div>
            </div>
            <div class="flex justify-between items-end">
                <p class="text-gray-700 flex-1 mr-4">${comment.comment}</p>
                ${comment.can_edit || comment.can_delete ? `
                    <div class="flex space-x-2 flex-shrink-0">
                        ${comment.can_edit ? `
                            <button onclick="editComment(${comment.id}, '${comment.comment.replace(/'/g, "\\'")}', ${comment.rating || 0})" 
                                    class="group relative p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 cursor-pointer"
                                    title="Edit comment">
                                <i class="ri-edit-line text-lg"></i>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                    Edit
                                </div>
                            </button>
                        ` : ''}
                        ${comment.can_delete ? `
                            <button onclick="deleteComment(${comment.id})" 
                                    class="group relative p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200 cursor-pointer"
                                    title="Delete comment">
                                <i class="ri-delete-bin-line text-xl"></i>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                    Delete
                                </div>
                            </button>
                        ` : ''}
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

// Rating functionality
function setupRating(starsContainer, ratingInput) {
    const stars = starsContainer.querySelectorAll('button');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            const rating = index + 1;
            ratingInput.value = rating;
            
            // Update star display
            stars.forEach((s, i) => {
                s.textContent = i < rating ? '★' : '☆';
                s.className = i < rating ? 'text-yellow-400 text-xl' : 'text-gray-300 hover:text-yellow-400 text-xl';
            });
        });
        
        star.addEventListener('mouseenter', () => {
            const rating = index + 1;
            stars.forEach((s, i) => {
                s.textContent = i < rating ? '★' : '☆';
                s.className = i < rating ? 'text-yellow-400 text-xl' : 'text-gray-300 text-xl';
            });
        });
    });
    
    starsContainer.addEventListener('mouseleave', () => {
        const currentRating = parseInt(ratingInput.value) || 0;
        stars.forEach((s, i) => {
            s.textContent = i < currentRating ? '★' : '☆';
            s.className = i < currentRating ? 'text-yellow-400 text-xl' : 'text-gray-300 hover:text-yellow-400 text-xl';
        });
    });
}

// Reset rating display
function resetRating() {
    const stars = document.querySelectorAll('#ratingStars button');
    const ratingInput = document.getElementById('rating');
    ratingInput.value = '';
    
    stars.forEach(star => {
        star.textContent = '☆';
        star.className = 'text-gray-300 hover:text-yellow-400 text-xl';
    });
}

// Submit comment form
document.getElementById('commentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('Form submit event triggered!');
    
    // Show loading state
    const submitBtn = document.getElementById('submitCommentBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Debug: Log the data being sent
    console.log('Submitting comment data:', data);
    console.log('Form validation check - comment length:', data.comment ? data.comment.length : 'no comment');
    console.log('Form validation check - service type:', data.service_type);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        showNotification('Security token missing. Please refresh the page.', 'error');
        resetSubmitButton();
        return;
    }
    
    try {
        console.log('Making request to /service-comments with data:', data);
        
        const response = await fetch('/service-comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        const result = await response.json();
        console.log('Response data:', result);
        
        if (response.ok && result.success) {
            console.log('Comment added successfully, reloading comments...');
            // Reload comments
            loadComments(currentServiceType);
            // Reset form
            this.reset();
            resetRating();
            // Show success message
            showNotification('Comment added successfully!', 'success');
        } else {
            // Handle validation errors
            if (result.details) {
                let errorMessage = 'Validation errors:\n';
                for (const [field, errors] of Object.entries(result.details)) {
                    errorMessage += `${field}: ${errors.join(', ')}\n`;
                }
                showNotification(errorMessage, 'error');
            } else {
                throw new Error(result.error || 'Failed to add comment');
            }
        }
    } catch (error) {
        console.error('Error adding comment:', error);
        showNotification('Failed to add comment: ' + error.message, 'error');
    } finally {
        resetSubmitButton();
    }
});

// Reset submit button state
function resetSubmitButton() {
    const submitBtn = document.getElementById('submitCommentBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
}

// Edit comment
function editComment(commentId, commentText, rating) {
    document.getElementById('editCommentId').value = commentId;
    document.getElementById('editComment').value = commentText;
    document.getElementById('editRating').value = rating;
    
    // Update rating stars
    const stars = document.querySelectorAll('#editRatingStars button');
    stars.forEach((star, index) => {
        star.textContent = index < rating ? '★' : '☆';
        star.className = index < rating ? 'text-yellow-400 text-xl' : 'text-gray-300 hover:text-yellow-400 text-xl';
    });
    
    // Auto-resize the textarea after setting content
    const editTextarea = document.getElementById('editComment');
    if (editTextarea) {
        // Use setTimeout to ensure the modal is rendered before resizing
        setTimeout(() => {
            autoResizeTextarea(editTextarea);
        }, 100);
    }
    
    const modal = document.getElementById('editCommentModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close edit comment modal
function closeEditCommentModal() {
    const modal = document.getElementById('editCommentModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Delete comment - show confirmation modal
function deleteComment(commentId) {
    commentToDelete = commentId;
    const modal = document.getElementById('deleteCommentModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close delete confirmation modal
function closeDeleteCommentModal() {
    const modal = document.getElementById('deleteCommentModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    commentToDelete = null;
}

// Confirm delete comment
async function confirmDeleteComment() {
    if (!commentToDelete) {
        return;
    }
    
    try {
        const response = await fetch(`/service-comments/${commentToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadComments(currentServiceType);
            showNotification('Comment deleted successfully!', 'success');
        } else {
            throw new Error(result.error || 'Failed to delete comment');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showNotification('Failed to delete comment. Please try again.', 'error');
    } finally {
        closeDeleteCommentModal();
    }
}

// Submit edit comment form
document.getElementById('editCommentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const commentId = document.getElementById('editCommentId').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`/service-comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeEditCommentModal();
            loadComments(currentServiceType);
            showNotification('Comment updated successfully!', 'success');
        } else {
            throw new Error(result.error || 'Failed to update comment');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showNotification('Failed to update comment. Please try again.', 'error');
    }
});

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Auto-resize textarea function
function autoResizeTextarea(textarea) {
    // Reset height to auto to get the correct scrollHeight
    textarea.style.height = 'auto';
    
    // Get the minimum and maximum heights from CSS classes
    const minHeight = 80; // min-h-[80px]
    const maxHeight = textarea.id === 'editComment' ? 200 : 120; // max-h-[200px] for edit, max-h-[120px] for main
    
    // Calculate new height based on content
    const newHeight = Math.max(minHeight, Math.min(maxHeight, textarea.scrollHeight));
    
    // Set the new height
    textarea.style.height = newHeight + 'px';
    
    // Show/hide scrollbar based on content
    if (textarea.scrollHeight > maxHeight) {
        textarea.style.overflowY = 'auto';
    } else {
        textarea.style.overflowY = 'hidden';
    }
}

// Initialize rating functionality when modal opens
document.addEventListener('DOMContentLoaded', function() {
    setupRating(document.getElementById('ratingStars'), document.getElementById('rating'));
    setupRating(document.getElementById('editRatingStars'), document.getElementById('editRating'));
    
    // Set up auto-resize for textareas
    const commentTextarea = document.getElementById('comment');
    const editCommentTextarea = document.getElementById('editComment');
    
    if (commentTextarea) {
        // Auto-resize on input
        commentTextarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
        
        // Initial resize
        autoResizeTextarea(commentTextarea);
    }
    
    if (editCommentTextarea) {
        // Auto-resize on input
        editCommentTextarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
        
        // Initial resize
        autoResizeTextarea(editCommentTextarea);
    }
    
    // Close modals when clicking outside
    document.getElementById('commentsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommentsModal();
        }
    });
    
    document.getElementById('editCommentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditCommentModal();
        }
    });
    
    document.getElementById('deleteCommentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteCommentModal();
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCommentsModal();
            closeEditCommentModal();
            closeDeleteCommentModal();
        }
    });
});
</script>
@endsection
