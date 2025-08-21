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
            <div class="chat-messages-block">
                <MessageList :messages="messages"/>
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


import ErrorBox from "@/components/chat/errorBox/ErrorBox.vue";

defineOptions({
    name: 'ChaФЫt',
});
import {onMounted, reactive, ref} from 'vue';
import {Message} from "@/models/chat/Message";
import {useStore} from 'vuex';
import {Client} from "@/models/chat/Client";
import {fi} from "vuetify/locale";

let errorMessage = ref('');
const store = useStore();
const headerComponentName = ref('ChatHeader');
const changeHeaderHandle = (headerName: string | null) => {
    headerComponentName.value = headerName;
};
const changeSourceHandle = (source: string | null) => {
    headerComponentName.value = source;
    changeMessagesSource(source)
};
let messages = reactive([]);
function changeMessagesSource(source: string){
    console.log("source", source)
    messages = messages.filter(message => message.channel === source);
}
onMounted(async () => {
    store.commit('ui/spinner/setOpen');
    console.log(store.state.clients.crm.crmClient.Phone)
    let crmClientPhone = store.state.clients.crm.crmClient.Phone;
    let crmClientUsername = store.state.clients.crm.crmClient.Username;
    if (crmClientUsername == null && crmClientPhone == null) {
        errorMessage.value = "Відсутній номер телефону або username"
        store.commit('ui/spinner/setClose');
    } else {
        console.log(store.state.clients.repository.client);
        let response = await store.dispatch('messages/messageRepository/getMessagesByClient', {
            'page': 1,
            'limit': 100,
            'client_id': store.state.clients.repository.client.id
        });
        messages.splice(0, messages.length, ...response);
        console.log("messages", messages);

        console.log("messages", messages)
        store.commit('ui/spinner/setClose');
    }
})
onMounted(() => {

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
            }
        });
    console.log("start")

})
</script>

<style scoped>
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
