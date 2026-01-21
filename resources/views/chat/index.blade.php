<x-app-layout>
    <div id="chat-root" class="h-screen flex flex-col bg-[#efeae2] relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none z-0"
            style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat;">
        </div>

        <div class="px-4 py-2 bg-[#008069] text-white flex items-center justify-between shadow-md z-30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-300 overflow-hidden border border-white/20">
                    <img src="https://ui-avatars.com/api/?name=Vidcash+Community&background=00a884&color=fff"
                        alt="Avatar">
                </div>
                <div>
                    <h1 class="font-semibold text-base leading-tight">Vidcash Community</h1>
                    <div class="text-[11px] opacity-90" x-text="onlineCount + ' peserta online'"></div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3 z-10 custom-scrollbar" x-ref="messagesContainer">
            <template x-for="(msg, index) in messages" :key="msg.key">
                <div class="flex flex-col w-full">
                    <template x-if="shouldShowDate(index)">
                        <div class="flex justify-center my-4">
                            <span
                                class="bg-white px-3 py-1 rounded-md text-[11px] text-gray-500 shadow-sm uppercase tracking-wide"
                                x-text="getDateLabel(msg.timestamp)"></span>
                        </div>
                    </template>

                    <div class="flex w-full mb-1" :class="msg.sender_id == userId ? 'justify-end' : 'justify-start'">
                        <div :id="'msg-' + msg.key"
                            class="max-w-[85%] min-w-[120px] rounded-lg px-2 py-1 shadow-sm relative group transition-all duration-500 z-20"
                            :class="msg.sender_id == userId ? '!bg-[#d9fdd3] rounded-tr-none' : '!bg-white rounded-tl-none'">

                            <template x-if="msg.sender_id != userId">
                                <div class="text-[11px] font-bold mb-0.5 pr-12"
                                    :style="'color:' + getSenderColor(msg.sender_name)" x-text="msg.sender_name"></div>
                            </template>

                            <template x-if="msg.reply_to">
                                <div class="mb-1 p-2 rounded bg-black/5 border-l-[4px] border-[#06cf9c] text-[12px] cursor-pointer"
                                    @click="scrollToMessage(msg.reply_to.key)">
                                    <div class="font-bold text-[#06cf9c]" x-text="msg.reply_to.sender_name"></div>
                                    <div class="text-gray-600 truncate" x-text="msg.reply_to.text"></div>
                                </div>
                            </template>

                            <div class="text-[14.2px] text-[#111b21] leading-normal pr-12 break-words"
                                x-text="msg.text"></div>

                            <div class="flex items-center justify-end gap-1 -mt-1 ml-2">
                                <span class="text-[10px] text-gray-500 uppercase"
                                    x-text="formatTime(msg.timestamp)"></span>
                            </div>

                            <div
                                class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-30">
                                <template x-if="msg.sender_id == userId">
                                    <button @click.stop="deleteMessage(msg.key)"
                                        class="p-1 text-red-400 hover:text-red-600 bg-white/80 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                        </svg>
                                    </button>
                                </template>
                                <button type="button" @click.stop="setReply(msg)"
                                    class="p-1 text-gray-400 hover:text-gray-600 bg-white/80 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 17 4 12 9 7"></polyline>
                                        <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-2 bg-[#f0f2f5] z-40 relative">
            <div x-show="replyingTo"
                class="bg-white p-2 border-l-4 border-[#06cf9c] rounded-t-lg mb-1 flex justify-between shadow-md relative z-50"
                x-transition>
                <div class="overflow-hidden">
                    <div class="text-[12px] font-bold text-[#06cf9c]" x-text="replyingTo?.sender_name"></div>
                    <div class="text-[12px] text-gray-500 truncate" x-text="replyingTo?.text"></div>
                </div>
                <button @click="replyingTo = null" class="text-gray-400 text-xl px-2">&times;</button>
            </div>

            <form @submit.prevent="sendMessage" class="flex items-center gap-2 relative z-50">
                <div
                    class="flex-1 bg-white rounded-full flex items-center px-4 py-1.5 shadow-sm border border-gray-200">
                    <input type="text" x-model="newMessage" x-ref="chatInput"
                        class="flex-1 border-none focus:ring-0 text-sm py-1 bg-transparent" placeholder="Ketik pesan"
                        required>
                </div>
                <button type="submit" class="bg-[#00a884] p-3 rounded-full text-white shadow-md disabled:opacity-50"
                    :disabled="!newMessage.trim()">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z">
                        </path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase, ref, push, onChildAdded, onChildRemoved, remove, serverTimestamp, query, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

        const firebaseConfig = @json($firebaseConfig);
        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);
        const chatRef = ref(db, 'global_chat_messages');

        const chatComponent = () => ({
            userId: {{ auth()->id() }},
            userName: @json(auth()->user()->name),
            newMessage: '',
            messages: [],
            replyingTo: null,
            onlineCount: 18,

            init() {
                this.loadMessages();
                this.$watch('messages', () => this.$nextTick(() => this.scrollToBottom()));
            },

            loadMessages() {
                const q = query(chatRef, limitToLast(100));
                onChildAdded(q, (snap) => {
                    const data = snap.val();
                    data.key = snap.key;
                    if (!this.messages.find(m => m.key === data.key)) this.messages.push(data);
                });
                onChildRemoved(q, (snap) => {
                    this.messages = this.messages.filter(m => m.key !== snap.key);
                });
            },

            scrollToBottom() {
                const el = this.$refs.messagesContainer;
                if (el) el.scrollTop = el.scrollHeight;
            },

            scrollToMessage(key) {
                const msgEl = document.getElementById('msg-' + key);
                if (msgEl) {
                    msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    msgEl.classList.add('!bg-blue-100');
                    setTimeout(() => msgEl.classList.remove('!bg-blue-100'), 1000);
                }
            },

            setReply(msg) {
                this.replyingTo = msg;
                this.$nextTick(() => {
                    if (this.$refs.chatInput) this.$refs.chatInput.focus();
                });
            },

            async deleteMessage(key) {
                if (!confirm('Hapus pesan ini?')) return;
                try {
                    await remove(ref(db, 'global_chat_messages/' + key));
                } catch (e) { console.error(e); }
            },

            formatTime(ts) {
                if (!ts) return '';
                return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            },

            getSenderColor(name) {
                const colors = ['#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#03a9f4', '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#ffc107', '#ff9800'];
                let hash = 0;
                for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
                return colors[Math.abs(hash) % colors.length];
            },

            shouldShowDate(index) {
                if (index === 0) return true;
                const curr = new Date(this.messages[index].timestamp).toDateString();
                const prev = new Date(this.messages[index - 1].timestamp).toDateString();
                return curr !== prev;
            },

            getDateLabel(ts) {
                const d = new Date(ts);
                const today = new Date();
                const yesterday = new Date(); yesterday.setDate(today.getDate() - 1);
                if (d.toDateString() === today.toDateString()) return 'Hari Ini';
                if (d.toDateString() === yesterday.toDateString()) return 'Kemarin';
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            },

            async sendMessage() {
                if (!this.newMessage.trim()) return;

                const payload = {
                    text: this.newMessage,
                    sender_id: this.userId,
                    sender_name: this.userName,
                    timestamp: serverTimestamp(),
                };

                // SOLUSI: Pastikan objek reply_to ikut dikirim
                if (this.replyingTo) {
                    payload.reply_to = {
                        key: this.replyingTo.key,
                        text: this.replyingTo.text,
                        sender_name: this.replyingTo.sender_name
                    };
                }

                await push(chatRef, payload);
                this.newMessage = '';
                this.replyingTo = null;
            }
        });

        const start = () => {
            if (window.Alpine) {
                window.Alpine.data('chatApp', chatComponent);
                const root = document.getElementById('chat-root');
                root.setAttribute('x-data', 'chatApp');
                if (window.Alpine.initTree) window.Alpine.initTree(root);
            }
        };

        if (document.readyState === 'complete') start();
        else window.addEventListener('load', start);
    </script>
</x-app-layout>