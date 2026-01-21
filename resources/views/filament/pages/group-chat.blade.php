<x-filament-panels::page>
    <div x-data="adminChatApp()"
        class="flex flex-col h-[75vh] bg-[#efeae2] relative rounded-xl shadow-md overflow-hidden border border-gray-200">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none"
            style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat;">
        </div>

        <!-- Chat Header (Inside Widget) -->
        <div class="px-4 py-3 bg-[#008069] text-white flex justify-between items-center shadow-md z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <x-heroicon-m-users class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h1 class="font-semibold text-sm leading-tight">Global Community (Admin View)</h1>
                    <div class="text-[10px] opacity-90">Live Chat Configured</div>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2 z-10 custom-scrollbar" id="admin-messages-container"
            x-ref="messagesContainer">
            <template x-for="msg in messages" :key="msg.key">
                <div class="flex flex-col w-full">
                    <div class="flex w-full mb-1" :class="msg.sender_id == userId ? 'justify-end' : 'justify-start'">
                        <!-- Message Bubble -->
                        <div :id="'admin-msg-' + msg.key"
                            class="max-w-[85%] min-w-[60px] rounded-lg px-2 py-1 shadow-sm relative group text-sm transition-colors duration-500"
                            :class="msg.sender_id == userId ? 'bg-[#d9fdd3] rounded-tr-none' : 'bg-white rounded-tl-none'">

                            <!-- Sender Info -->
                            <template x-if="msg.sender_id != userId">
                                <div class="text-[11px] font-bold mb-0.5 pr-10"
                                    :class="msg.is_admin ? 'text-red-500' : 'text-orange-500'"
                                    x-text="msg.sender_name + (msg.is_admin ? ' (Admin)' : '')"></div>
                            </template>

                            <!-- Reply Context -->
                            <template x-if="msg.reply_to">
                                <div class="mb-1 p-1.5 rounded bg-black/5 border-l-[4px] border-[#06cf9c] text-[11px] cursor-pointer"
                                    @click="scrollToMessage(msg.reply_to.key)">
                                    <div class="font-bold text-[#06cf9c]" x-text="msg.reply_to.sender_name"></div>
                                    <div class="truncate opacity-70" x-text="msg.reply_to.text"></div>
                                </div>
                            </template>

                            <!-- Text -->
                            <div class="text-[13.5px] text-[#111b21] leading-relaxed pr-12 break-words"
                                x-text="msg.text"></div>

                            <!-- Meta -->
                            <div class="flex items-center justify-end gap-1 -mt-1 ml-2">
                                <div class="text-[9px] text-gray-500" x-text="formatTime(msg.timestamp)"></div>
                                <template x-if="msg.sender_id == userId">
                                    <span class="text-[#53bdeb]">
                                        <svg viewBox="0 0 16 15" width="12" height="12">
                                            <path fill="currentColor"
                                                d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L4.566 14.39 1.983 11.23a.365.365 0 0 0-.323-.119l-.396.11a.365.365 0 0 0-.172.587l3.664 3.73a.365.365 0 0 0 .54-.035L15.073 3.826a.365.365 0 0 0-.063-.51zm-4.213 3.98l-.394-.413-.472.448 3.444 3.608 3.59-4.517-.473-.377-3.117 3.92z">
                                            </path>
                                        </svg>
                                    </span>
                                </template>
                            </div>

                            <!-- Reply Button -->
                            <button @click="setReply(msg)"
                                class="absolute top-1 right-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 p-1 text-gray-400 hover:text-gray-600 transition-opacity">
                                <x-heroicon-m-arrow-uturn-left class="w-3 h-3" />
                            </button>

                            <!-- Delete Button (Admin Permission) -->
                            <button @click="deleteMessage(msg.key)"
                                class="absolute top-1 right-8 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 p-1 text-red-400 hover:text-red-600 transition-opacity">
                                <x-heroicon-m-trash class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="p-2 bg-[#f0f2f5] z-20 border-t">
            <div x-show="replyingTo" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2" style="display: none;">
                <div class="bg-white p-2 border-l-4 border-[#06cf9c] rounded-t-lg mb-1 flex justify-between shadow-sm">
                    <div class="overflow-hidden">
                        <div class="text-[12px] font-bold text-[#06cf9c]" x-text="replyingTo?.sender_name"></div>
                        <div class="text-[12px] text-gray-500 truncate" x-text="replyingTo?.text"></div>
                    </div>
                    <button @click="cancelReply" class="text-gray-400 text-xl font-light px-2">&times;</button>
                </div>
            </div>

            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <div
                    class="flex-1 bg-white rounded-full flex items-center px-4 py-1.5 shadow-sm border border-gray-100">
                    <input type="text" x-model="newMessage"
                        class="flex-1 border-none focus:ring-0 text-sm py-1 bg-transparent"
                        placeholder="Type a message as Admin..." required>
                </div>
                <button type="submit"
                    class="bg-[#00a884] p-3 rounded-full text-white shadow-md hover:bg-[#008f71] transition-all disabled:opacity-50"
                    :disabled="!newMessage.trim()">
                    <x-heroicon-m-paper-airplane class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }
    </style>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase, ref, push, onChildAdded, onChildRemoved, remove, serverTimestamp, query, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

        const firebaseConfig = @json($firebaseConfig);

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);
        const chatRef = ref(db, 'global_chat_messages');

        const adminChatComponent = () => ({
            userId: {{ auth()->id() }},
            userName: "{{ auth()->user()->name }}",
            newMessage: '',
            messages: [],
            replyingTo: null,

            init() {
                this.loadMessages();
            },

            loadMessages() {
                const q = query(chatRef, limitToLast(50));

                onChildAdded(q, (snapshot) => {
                    const data = snapshot.val();
                    data.key = snapshot.key;
                    if (!this.messages.find(m => m.key === data.key)) {
                        this.messages.push(data);
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });

                onChildRemoved(q, (snapshot) => {
                    this.messages = this.messages.filter(m => m.key !== snapshot.key);
                });
            },

            scrollToBottom() {
                const container = this.$refs.messagesContainer;
                if (container) container.scrollTop = container.scrollHeight;
            },

            scrollToMessage(key) {
                const msgEl = document.getElementById('admin-msg-' + key);
                if (msgEl) {
                    msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    msgEl.classList.add('bg-blue-100');
                    setTimeout(() => msgEl.classList.remove('bg-blue-100'), 1000);
                }
            },

            setReply(msg) {
                this.replyingTo = msg;
                document.querySelector('input[type="text"]').focus();
            },

            cancelReply() {
                this.replyingTo = null;
            },

            async deleteMessage(key) {
                if (!confirm('Hapus pesan ini sebagai ADMIN?')) return;
                try {
                    await remove(ref(db, 'global_chat_messages/' + key));
                } catch (e) {
                    alert('Gagal menghapus pesan: ' + e.message);
                }
            },

            formatTime(timestamp) {
                if (!timestamp) return '';
                return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            },

            async sendMessage() {
                if (!this.newMessage.trim()) return;

                const payload = {
                    text: this.newMessage,
                    sender_id: this.userId,
                    sender_name: this.userName,
                    timestamp: serverTimestamp(),
                    is_admin: true,
                };

                if (this.replyingTo) {
                    payload.reply_to = {
                        key: this.replyingTo.key,
                        sender_name: this.replyingTo.sender_name,
                        text: this.replyingTo.text
                    };
                }

                await push(chatRef, payload);

                this.newMessage = '';
                this.replyingTo = null;
            }
        });

        const registerAlpine = () => {
            Alpine.data('adminChatApp', adminChatComponent);
        };

        if (window.Alpine) {
            registerAlpine();
        } else {
            document.addEventListener('alpine:init', registerAlpine);
        }
    </script>
</x-filament-panels::page>