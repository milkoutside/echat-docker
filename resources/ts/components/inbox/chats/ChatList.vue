<template>
    <div class="chat-list" @scroll="onScroll">
        <div class="row">
            <div class="col-11" style="padding-right: 0px">
                <div
                    v-for="chat in props.chats"
                    :key="chat.lastMessageWithClient.client.id"
                    class="chat-item"
                    :class="{ 'active-chat': store.state.inbox.openChatId === chat.lastMessageWithClient.client.id }"
                    @click="openChat(chat)"
                >
                    <div class="chat-info">
                        <span class="chat-name">
{{chat?.crmClient?.Full_Name || chat?.lastMessageWithClient?.client?.username || chat?.lastMessageWithClient?.client?.phones?.telegram || chat?.lastMessageWithClient?.client?.phones?.viber || chat?.lastMessageWithClient?.client?.phones?.whatsapp || 'Неизвестный пользователь' }}


                        </span>
                        <span class="chat-last-message"> {{
                                chat.lastMessageWithClient.fileUrl != null
                                    ? 'Було надіслано файл'
                                    : chat.lastMessageWithClient.lastMessage
                                        ? (chat.lastMessageWithClient.lastMessage.length > 20
                                            ? chat.lastMessageWithClient.lastMessage.slice(0, 20) + '...'
                                            : chat.lastMessageWithClient.lastMessage)
                                        : ''
                            }}</span>



                    </div>
                    <div class="chat-time">{{ chat.lastMessageWithClient.sendTime }}</div>
                    <div class="notification-badge" v-if="chat.lastMessageWithClient.unreadMessagesCount > 0">
                        {{ chat.lastMessageWithClient.unreadMessagesCount }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {Client} from "@/models/chat/Client.ts";
import {LastMessageWithClient} from "@/models/inbox/LastMessageWithClient.ts";
import {useStore} from "vuex";
import {ref,onMounted, onBeforeUnmount, reactive} from "vue";
import {PhoneHelpers} from "@/helpers/phones/PhoneHelpers.ts";

defineOptions({
    name: "ChatList",
});

const props = defineProps<{
  chats: {
    lastMessageWithClient: LastMessageWithClient,
    crmClient: { Full_Name: string | null, Phone: string | null, Telegram_nickname: string | null },
    module:""
  }[],
  loadingRecords: boolean
}>();

const emit = defineEmits(["selectChat", "load-more"]);
const store = useStore();

const onScroll = (event: Event) => {
  const target = event.target as HTMLElement;

  const scrollTop = target.scrollTop;
  const scrollHeight = target.scrollHeight;
  const clientHeight = target.clientHeight;

  // Небольшой допуск в 5 пикселей на случай округления
  const bottom = scrollHeight - scrollTop - clientHeight <= 5;

  console.log("scrollTop:", scrollTop);
  console.log("scrollHeight:", scrollHeight);
  console.log("clientHeight:", clientHeight);
  console.log("bottom detected:", bottom);

  if (bottom && !props.loadingRecords) {
    console.log("Near bottom, loading more...");
    emit("load-more");
  }
};
async function openChat(chat) {
  console.log("open chat",chat)
    if(store.state.inbox.openChatId != chat.lastMessageWithClient.client.id) {
        store.commit('ui/spinner/setOpen');
        store.commit("inbox/setOpenChatId",chat.lastMessageWithClient.client.id);
        store.commit("inbox/setCloseChat");
        store.commit("settings/setClose");
        const phone = PhoneHelpers.validateAndFormatPhoneToGlobal(chat.crmClient?.Phone);
        const telegramNickname = chat.crmClient?.Telegram_nickname ?? null;
        const viberPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(chat.crmClient?.Phone ?? '');
        const whatsappPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(chat.crmClient?.WhatsApp  ?? '');

        let repositoryClient = await store.dispatch('clients/repository/getClientById',new Client(chat.lastMessageWithClient.client.id,null,null));
        console.log(repositoryClient,"resporepositoryClient")
        const currentPhones = repositoryClient.phones || {};
        const updatedPhones = {...currentPhones};
        let needsUpdate = false;
        console.log(updatedPhones,"papa")
        // Добавляем недостающие телефоны
        if (phone !== 'invalid' && !currentPhones.viber) {
            updatedPhones.viber = viberPhone;
            needsUpdate = true;
        }
        if (chat.crmClient.WhatsApp !== 'invalid' && !currentPhones.whatsapp) {
            updatedPhones.whatsapp = whatsappPhone;
            needsUpdate = true;
        }

        // Проверяем нужно ли обновить username
        console.log("UPAASD",updatedPhones)
        const shouldUpdateUsername = telegramNickname && repositoryClient.username !== telegramNickname;

        if (needsUpdate || shouldUpdateUsername) {
            console.log("Updating client data", updatedPhones, currentPhones);
            let updateClientData = new Client(
                repositoryClient.id,
                needsUpdate ? updatedPhones : currentPhones,
                shouldUpdateUsername ? telegramNickname : repositoryClient.username
            );
            console.log("updas", updateClientData)
            let updateClient: Client | null = await store.dispatch(
                'clients/repository/updateClientById',
                updateClientData
            );

            if (updateClient?.id) {
                store.commit('clients/repository/setClient', updateClient);
            }
        }

        store.commit("clients/repository/setClient",repositoryClient);
        store.commit("clients/repository/setClientSource",chat.lastMessageWithClient.lastChannel??'telegram');
        store.commit("clients/crm/setCrmClient",chat.crmClient??null);
        store.commit("clients/crm/setModule",chat.module);
        await store.dispatch('messages/messageRepository/readAllMessagesByClient',chat.lastMessageWithClient.client.id);
        console.log("senderss",store.state.echat.senders,chat.lastMessageWithClient)
        store.state.echat.senders.forEach(sender => {
            if(sender.sender == chat.lastMessageWithClient.lastSender){
                store.commit("echat/setSelectedSender",sender.sender);
            }
        });
        store.commit("inbox/setOpenChat");
        chat.lastMessageWithClient.unreadMessagesCount = 0;
    }
}
onMounted(async () => {
  console.log("start bobo",props.chats)
  console.log("start bobo1",store.state.inbox.openChatId)
 

});
</script>

<style scoped>
.chat-list {
    overflow-y: auto;
    flex-grow: 1;
    height: 100vh;
    overflow-x: hidden;
    scrollbar-gutter: stable;
}

.chat-list::-webkit-scrollbar {
    width: 7px;
}

.chat-list::-webkit-scrollbar-thumb {
    background: rgba(38, 65, 228, 0.03);
    border-radius: 10px;
}

.chat-item {
    width: 103%;
    display: flex;
    align-items: center;
    padding: 12px;
    margin-bottom: 10px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}

.chat-item:hover {
    background-color: #f1f1f1;
    transform: translateX(5px);
}

.chat-avatar {
    margin-right: 15px;
}

.chat-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.chat-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.chat-name {
    font-weight: 600;
    font-size: 1rem;
    color: #333;
}

.chat-last-message {
    color: #666;
    font-size: 0.875rem;
    margin-top: 4px;
    white-space: nowrap; /* Отключить перенос текста */
    overflow: hidden; /* Спрятать текст, выходящий за границы */
    text-overflow: ellipsis; /* Добавить "..." вместо обрезанного текста */
    max-width: 200px; /* Ограничить ширину блока (подберите точный размер под ваш дизайн) */
}


.chat-time {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.75rem;
    color: #aaa;
}

.notification-badge {
    background-color: #bcbcbc; /* Красный цвет */
    color: white; /* Белый цвет текста */
    font-size: 12px; /* Размер текста */
    font-weight: bold; /* Жирный шрифт */
    text-align: center; /* Текст по центру */
    border-radius: 50%; /* Округлить до круга */
    width: 24px; /* Ширина 24px */
    height: 24px; /* Высота 24px */
    display: inline-flex; /* Включаем flexbox */
    justify-content: center; /* Горизонтально по центру */
    align-items: center; /* Вертикально по центру */
    position: absolute; /* Абсолютное позиционирование */
    top: 35px; /* Сместить сверху */
    right: 10px; /* Сместить справа */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); /* Добавляем небольшую тень */
}
.active-chat {
    background-color: #dbeafe !important; /* Голубой цвет фона */
    border-left: 4px solid #3b82f6; /* Синяя полоса слева */
    transform: none; /* Убираем смещение при выборе */
}
</style>
