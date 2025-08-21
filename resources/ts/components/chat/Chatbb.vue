<template>
    <div>
        <div v-if="errorMessage == ''" class="chat-container">
            <div class="chat-header-block">
                <Transition name="fade" mode="out-in">
                    <component
                        :is="headerComponentName"
                        @change-header="changeHeaderHandle"
                        @change-source="changeSourceHandle"
                        :key="headerComponentName"
                    ></component>
                </Transition>
            </div>
            <div class="chat-messages-block" ref="messagesBlock" @scroll="handleScroll">
                <LocalSpinner v-if="isFirstLoad"></LocalSpinner>
                <div v-else v-if="isLoading" class="loading">Загрузка...</div>
                <MessageList v-if="sortedMessages.length > 0" :messages="sortedMessages"/>
                <div class="d-flex justify-content-center align-items-center h-100" v-else-if="!isLoading && sortedMessages.length <= 0">
                    <p>В цьому чаті ще немає повідомлень</p>
                </div>
            </div>
            <div class="chat-footer-block">
                <ChatFooter></ChatFooter>
            </div>
            <SendFileModal v-if="store.state.modals.sendFileModal.isOpen"></SendFileModal>
        </div>
        <div v-else>
            <ErrorBox :errorMessage="errorMessage"></ErrorBox>
        </div>
    </div>
</template>

<script setup lang="ts">
import { throttle } from 'lodash-es';
import {nextTick, onMounted, reactive, ref, computed, onBeforeMount, onBeforeUnmount} from 'vue';
import { useStore } from 'vuex';
import ErrorBox from "@/components/chat/errorBox/ErrorBox.vue";
import { Message } from "@/models/chat/Message";
import {useRoute} from "vue-router";
import LocalSpinner from "@/components/spinner/LocalSpinner.vue";
defineOptions({
    name: 'Chatbb',
});
const changeHeaderHandle = (headerName: string | null) => {
    headerComponentName.value = headerName;
};
const changeSourceHandle = (source: string | null) => {
    headerComponentName.value = source;
    changeMessagesSource(source)
};function changeMessagesSource(source: string){
    console.log("source", source)
    messages = messages.filter(message => message.channel === source);
}
let store = useStore();
let errorMessage = ref('');
let headerComponentName = ref('ChatHeader');
let isLoading = ref(false);
let page = ref(1);
let totalPages = ref(1);
let messagesBlock = ref<HTMLElement | null>(null);
let messages = reactive<Message[]>([]);
let isFirstLoad = ref(true);

// Сортировка сообщений от старых к новым для отображения
const sortedMessages = computed(() => {
    return [...messages].sort((a, b) =>
        new Date(a.send_time).getTime() - new Date(b.send_time).getTime()
    );
});

const scrollToBottom = () => {
    if (messagesBlock.value) {
        setTimeout(() => {
            messagesBlock.value.scrollTop = messagesBlock.value.scrollHeight;
        },100)
    }
};
const isNotNearBottom = (container: HTMLElement, threshold = 0.3) => {
    if (container.scrollHeight === 0) return false;

    const { scrollTop, clientHeight, scrollHeight } = container;
    const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
    const thresholdPixels = scrollHeight * threshold;

    return distanceFromBottom > thresholdPixels;
};
onBeforeMount(async () => {
   scrollToBottom()
})
async function fetchMessages() {
    isLoading.value = true;
    try {
        const response = await store.dispatch('messages/messageRepository/getMessagesByClient', {
            'page': page.value,
            'limit': 10,
            'client_id': store.state.clients.repository.client.id
        });

        if (response?.data) {
            // Для первой страницы заменяем все сообщения
            if (page.value === 1) {
                messages.splice(0, messages.length, ...response.data);
                scrollToBottom()
            }
            // Для последующих страниц добавляем в начало
            else {
                const beforeHeight = messagesBlock.value?.scrollHeight || 0;
                messages.unshift(...response.data);
                await nextTick(() => {
                    if (messagesBlock.value) {
                        messagesBlock.value.scrollTop = messagesBlock.value.scrollHeight - beforeHeight;
                    }
                });
            }

            totalPages.value = response.last_page;
        }
    } catch (error) {
        console.error('Ошибка загрузки сообщений:', error);
    } finally {
        isLoading.value = false;

        if (isFirstLoad.value) {
            await nextTick(scrollToBottom);
            isFirstLoad.value = false;
        }
    }
}


const route = useRoute();
onMounted(async () => {
    console.log("route", route.fullPath.toLowerCase().includes("inbox"))
    if (!route.fullPath.toLowerCase().includes("inbox")) {
        store.commit('ui/spinner/setOpen');
        const client = store.state.clients.crm.crmClient;
        if (!client.Phone && !client.Username) {
            errorMessage.value = "Відсутній номер телефону або username";
            store.commit('ui/spinner/setClose');
            return;
        }

        await fetchMessages();
        store.commit('ui/spinner/setClose');
        window.Echo.channel(`laravel_database_client_${store.state.clients.repository.client.id}`)
            .listen('.new_message', (e) => {
                let message = e.message;

                // Проверяем, есть ли уже сообщение с таким message_id
                let existingMessage = messages.find(msg => msg.message_id === message.message_id);
                console.log("socket message", message)
                if (!existingMessage) {
                    messages.push(new Message(
                        message.sender,
                        message.text,
                        message.channel,
                        message.clientId,
                        message.sendType,
                        message.messageRead,
                        message.fileUrl,
                        message.messageId,
                        message.status,
                        message.sentAt,
                        message.id
                    ));
                    const container = e.target as HTMLElement;
                    if (isNotNearBottom(container, 0.6)) {
                        scrollToBottom()
                    }
                }
            });
        console.log("start")
    }else if(route.fullPath.toLowerCase().includes("inbox")) {
        console.log("clinasdadsadet ",store.state.clients.repository.client.id)
        if(store.state.clients.repository.client.id != null){
            await fetchMessages();
        }
    }
});
onBeforeUnmount(async () =>{
    window.Echo.leave(`laravel_database_client_${store.state.clients.repository.client.id}`);

})
const handleScroll = throttle((e: Event) => {
    const container = e.target as HTMLElement;
    const scrollPosition = container.scrollTop;

    if (scrollPosition < 100 && !isLoading.value && page.value < totalPages.value) {
        page.value += 1;
        fetchMessages(); // Загружаем старые сообщения
    }
}, 200);
// Остальные методы остаются без изменений
</script>

<style scoped>
.loading {
    text-align: center;
    padding: 10px;
    color: #666;
}

.chat-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
    margin: 0;
}

.chat-header-block {
    flex: 0 0 11%;
    background-color: #f5f5f5;
}

.chat-messages-block {
    flex: 1 1 84%;
    overflow-y: scroll;
}

.chat-messages-block::-webkit-scrollbar {
    width: 3px;
}

.chat-messages-block::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages-block::-webkit-scrollbar-thumb {
    background-color: rgba(38, 65, 228, 0.3);
    border-radius: 10px;
    border: 1px solid transparent;
}

.chat-messages-block {
    scrollbar-width: thin;
    scrollbar-color: rgba(38, 65, 228, 0.3) transparent;
}

.chat-footer-block {
    flex: 0 0 5%;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
