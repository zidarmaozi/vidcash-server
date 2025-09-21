<div class="video-preview-container">
    <video 
        controls 
        preload="metadata"
        class="w-full max-w-4xl mx-auto rounded-lg shadow-lg"
        style="max-height: 500px;"
        poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial, sans-serif' font-size='16' fill='%236b7280'%3ELoading Video...%3C/text%3E%3C/svg%3E"
        onerror="handleVideoError(this)"
        onloadeddata="handleVideoLoaded(this)"
    >
        <source src="{{ $videoUrl }}" type="video/mp4">
        <source src="{{ $videoUrl }}" type="video/webm">
        <source src="{{ $videoUrl }}" type="video/ogg">
        Your browser does not support the video tag.
    </video>
    
    <div id="video-error-message" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-md text-center">
        <div class="flex items-center justify-center">
            <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span class="text-red-800 font-medium">Video could not be loaded</span>
        </div>
        <p class="text-red-600 text-sm mt-2">The video file may not exist or there might be a network issue.</p>
        <p class="text-red-600 text-sm">Please check the CDN URL or try again later.</p>
    </div>
    
    <div class="mt-4 text-center">
        <a 
            href="{{ $videoUrl }}" 
            target="_blank" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-orange-600 text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            Open Video in New Tab
        </a>
        
        <button 
            onclick="copyVideoUrl('{{ $videoUrl }}')"
            class="ml-3 inline-flex items-center px-4 py-2 bg-gray-600 text-orange-600 text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Copy Video URL
        </button>
    </div>
    
    <script>
        function handleVideoError(videoElement) {
            const errorMessage = document.getElementById('video-error-message');
            if (errorMessage) {
                errorMessage.classList.remove('hidden');
            }
            
            // Hide the video element
            videoElement.style.display = 'none';
        }
        
        function handleVideoLoaded(videoElement) {
            const errorMessage = document.getElementById('video-error-message');
            if (errorMessage) {
                errorMessage.classList.add('hidden');
            }
            
            // Show the video element
            videoElement.style.display = 'block';
        }
        
        function copyVideoUrl(url) {
            navigator.clipboard.writeText(url).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';
                button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-gray-600', 'hover:bg-gray-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy video URL');
            });
        }
    </script>
</div>
