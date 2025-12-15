<!-- Ensure Tailwind is available -->
<script src="https://cdn.tailwindcss.com"></script>

<div x-data="aiAvatar" class="fixed bottom-5 right-5 z-50" style="font-family: sans-serif;">
    <!-- Chat Trigger Button -->
    <button 
        @click="openModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg flex items-center gap-2 transition-all transform hover:scale-105"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <span class="font-semibold">Live AI Assistant</span>
    </button>

    <!-- Modal -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-20 right-5 w-96 bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col"
        style="display: none;"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg">AI Assistant</h3>
            <button @click="closeModal()" class="hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>

        <!-- Video Area -->
        <div class="relative bg-black h-80 flex items-center justify-center overflow-hidden" x-ref="videoContainer">
            <template x-if="!room">
                <div class="text-gray-400 flex flex-col items-center">
                    <div class="animate-pulse bg-gray-700 h-16 w-16 rounded-full mb-2"></div>
                    <p x-text="statusMessage"></p>
                </div>
            </template>
            <!-- Remote Video Attached Here -->
            
            <!-- Local Video Preview -->
            <div x-show="room" class="absolute bottom-4 right-4 w-24 h-32 bg-gray-900 rounded-lg shadow-lg border-2 border-white overflow-hidden z-20">
                <div x-ref="localVideoContainer" class="w-full h-full object-cover transform -scale-x-100"></div>
            </div>
        </div>

        <!-- Controls -->
        <div class="p-4 bg-gray-50 flex flex-col gap-3">
            <div class="text-sm text-gray-500 text-center h-6" x-text="statusMessage"></div>
            
            <div class="flex justify-center gap-4">
                <!-- Toggle Mic -->
                <button 
                    @click="toggleMic()" 
                    :class="{'bg-red-500 hover:bg-red-600': !isMicEnabled, 'bg-gray-200 hover:bg-gray-300 text-gray-700': isMicEnabled}"
                    class="rounded-full p-3 shadow-md transition-colors duration-200"
                    title="Toggle Microphone"
                >
                    <!-- Mic On -->
                    <svg x-show="isMicEnabled" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <!-- Mic Off -->
                    <svg x-show="!isMicEnabled" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>

                <!-- Toggle Camera -->
                <button 
                    @click="toggleCamera()" 
                    :class="{'bg-red-500 hover:bg-red-600': !isCameraEnabled, 'bg-gray-200 hover:bg-gray-300 text-gray-700': isCameraEnabled}"
                    class="rounded-full p-3 shadow-md transition-colors duration-200"
                    title="Toggle Camera"
                >
                    <!-- Camera On -->
                    <svg x-show="isCameraEnabled" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <!-- Camera Off -->
                    <svg x-show="!isCameraEnabled" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>

                <!-- Disconnect -->
                <button 
                    @click="closeModal()" 
                    class="bg-red-600 hover:bg-red-700 text-white rounded-full p-3 shadow-md transition-colors duration-200"
                    title="End Call"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 18a2 2 0 002 2h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8z" /> 
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="text-xs text-center text-gray-400">Interactive Video Mode</p>
        </div>
    </div>
</div>

<script>
    const setupAiAvatar = () => {
        Alpine.data('aiAvatar', () => {
            let room = null; // Non-reactive state for LiveKit Room

            return {
                isOpen: false,
                isMicEnabled: true,
                isCameraEnabled: true,
                statusMessage: 'Ready to chat',

                openModal() {
                    this.isOpen = true;
                    this.initSession();
                },

                closeModal() {
                    this.isOpen = false;
                    if (room) {
                        room.disconnect();
                        room = null;
                    }
                },

                async initSession() {
                    this.statusMessage = 'Connecting...';
                    try {
                        const response = await fetch('/api/ai/start-session', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();

                        if (data.token && data.url) {
                            await this.connectToRoom(data.url, data.token);
                        } else {
                            this.statusMessage = data.error || 'Failed to start session';
                            console.error('Session error:', data);
                        }
                    } catch (error) {
                        console.error('Session Init Error:', error);
                        this.statusMessage = 'Connection failed';
                    }
                },

                async connectToRoom(url, token) {
                    try {
                        room = new LivekitClient.Room({
                            adaptiveStream: true,
                            dynacast: true,
                            videoCaptureDefaults: {
                                resolution: LivekitClient.VideoPresets.h720.resolution,
                            },
                        });

                        await room.connect(url, token);
                        this.statusMessage = 'Connected';

                        // Enable Camera and Mic by default for full 2-way experience
                        await room.localParticipant.enableCameraAndMicrophone();
                        this.attachLocalVideo();

                        room.on(LivekitClient.RoomEvent.TrackSubscribed, (track, publication, participant) => {
                            if (track.kind === 'video') {
                                const element = track.attach();
                                element.className = 'w-full h-full object-cover';
                                this.$refs.videoContainer.innerHTML = ''; // Clear placeholder
                                this.$refs.videoContainer.appendChild(element);
                                // Re-append the local video container to keep it on top (simple z-index fix)
                                this.$refs.videoContainer.appendChild(this.$refs.localVideoContainer.parentNode); 
                            }
                            if (track.kind === 'audio') {
                                const element = track.attach();
                                document.body.appendChild(element);
                            }
                        });

                        room.on(LivekitClient.RoomEvent.Disconnected, () => {
                            this.statusMessage = 'Disconnected';
                            room = null;
                        });
                        
                        room.on(LivekitClient.RoomEvent.LocalTrackUnpublished, (publication) => {
                             // Update state if tracks are externally modified
                             if (publication.kind === 'audio') this.isMicEnabled = false;
                             if (publication.kind === 'video') this.isCameraEnabled = false;
                        });

                    } catch (error) {
                        console.error('LiveKit Connection Error:', error);
                        this.statusMessage = 'Video connection failed: ' + error.message;
                    }
                },

                attachLocalVideo() {
                     if (!room || !room.localParticipant) return;
                     
                     const videoTrack = Array.from(room.localParticipant.videoTrackPublications.values())
                        .map(pub => pub.track)
                        .find(track => track && track.kind === 'video');

                     if (videoTrack) {
                         const element = videoTrack.attach();
                         element.className = 'w-full h-full object-cover';
                         this.$refs.localVideoContainer.innerHTML = '';
                         this.$refs.localVideoContainer.appendChild(element);
                     }
                },

                async toggleMic() {
                    if (!room) return;
                    const current = room.localParticipant.isMicrophoneEnabled;
                    await room.localParticipant.setMicrophoneEnabled(!current);
                    this.isMicEnabled = !current;
                },

                async toggleCamera() {
                    if (!room) return;
                    const current = room.localParticipant.isCameraEnabled;
                    await room.localParticipant.setCameraEnabled(!current);
                    this.isCameraEnabled = !current;
                    
                    if (!current) { // If we just enabled it
                         // Wait a tick for track to publish then attach
                         setTimeout(() => this.attachLocalVideo(), 500);
                    } else {
                         this.$refs.localVideoContainer.innerHTML = ''; // Clear preview
                    }
                },
            };
        });
    };

    if (document.addEventListener) {
        document.addEventListener('alpine:init', setupAiAvatar);
    } else {
        // Fallback or immediate execution if Alpine is already loaded (though alpine:init is event based)
        // Check if Alpine global exists and manually register if needed, though listener is safest.
        setupAiAvatar();
    }
</script>
