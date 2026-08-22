<x-base-layout title="Message Pratice">
    <main x-data="{
        message: '',
        editingIndex: null,
    
        messages: Alpine.$persist([
            { text: 'Hii bro', edited: false },
            { text: 'Hey, how are you?', edited: false },
            { text: 'I seen you!', edited: false }
        ]),
    
        bubbleClass(index) {
            if (this.messages.length === 1) {
                return '';
            }
    
            if (index === 0) {
                return 'rounded-br-none';
            }
    
            if (index === this.messages.length - 1) {
                return 'rounded-tr-none';
            }
    
            return 'rounded-r-none';
        },
    
        send() {
            if (!this.message.trim()) return;
    
            if (this.editingIndex !== null) {
                this.messages[this.editingIndex].text = this.message;
                this.messages[this.editingIndex].edited = true;
                this.editingIndex = null;
            } else {
                this.messages.push({
                    text: this.message,
                    edited: false
                });
            }
    
            this.message = '';
    
            this.$nextTick(() => {
                this.$refs.messageInput.style.height = 'auto';
            });
        },
    
        edit(index) {
            this.editingIndex = index;
            this.message = this.messages[index].text;
    
            this.$nextTick(() => {
                this.$refs.messageInput.focus();
                this.autoResize();
            });
        },
    
        unsend(index) {
            if (this.editingIndex === index) {
                this.editingIndex = null;
                this.message = '';
            }
    
            this.messages.splice(index, 1);
        },
    
        autoResize() {
            let el = this.$refs.messageInput;
    
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }
    }" class="min-h-screen bg-[#0C1014] flex flex-col p-5 gap-5">

        <div class="flex-1 flex flex-col justify-end items-end gap-0.5">

            <template x-for="(msg, index) in messages" :key="index">

                <div class="group flex flex-col items-end">

                    <div x-show="msg.edited" class="text-indigo-400 font-medium text-xs my-1 pr-2">
                        Edited
                    </div>

                    <div class="flex items-center gap-1">

                        <button @click="unsend(index)"
                            class="opacity-0 group-hover:opacity-100 transition text-red-400 text-sm hover:bg-neutral-800 px-2 py-0.5 rounded-full">
                            Unsend
                        </button>

                        <button @click="edit(index)"
                            class="opacity-0 group-hover:opacity-100 transition text-white text-sm hover:bg-neutral-800 px-2 py-0.5 rounded-full">
                            Edit
                        </button>

                        <div class="text-white bg-[#4A5DF9] w-fit max-w-xl px-3 py-1.5 rounded-2xl whitespace-pre-wrap"
                            :class="bubbleClass(index)" x-text="msg.text">
                        </div>
                    </div>

                </div>

            </template>

        </div>

        <div class="border border-zinc-600 rounded-3xl px-3 py-2 flex items-end gap-2">

            <textarea x-ref="messageInput" x-model="message" rows="1" @input="autoResize"
                @keydown.enter="if (!$event.ctrlKey && !$event.metaKey) return" @keydown.ctrl.enter.prevent="send"
                @keydown.meta.enter.prevent="send" :placeholder="editingIndex !== null ? 'Edit message...' : 'Message...'"
                class="w-full bg-transparent border-0 focus:ring-0 text-zinc-100 placeholder:text-zinc-400 resize-none overflow-hidden min-h-[24px] max-h-40"></textarea>

            <button @click="send" class="text-zinc-300 px-2 pb-1 whitespace-nowrap"
                x-text="editingIndex !== null ? 'Save' : 'Send'">
            </button>

        </div>

    </main>
</x-base-layout>
