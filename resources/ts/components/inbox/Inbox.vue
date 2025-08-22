<template>
    <div class="inbox-panel">
        <div class="inbox-header-block">
            <InboxHeader/>
            <ChatSearch @update:searchResults="updateSearchResults" @update:resetSearch="resetSearch"/>
            <div class="module-block">
                <ModuleFilter @update:selectedModule="updateSelectedModule"/>
                <ChatFilter @update:source="updateSourceFilter"/>
            </div>
        </div>
        <div class="separator"></div>
        <LocalSpinner v-if="loadingSpinner"></LocalSpinner>
        <ChatList v-else :loading-records="loadingRecords" :chats="chats" @select-chat="selectChat"
                  @load-more="fetchClients"/>
        <SettingsBlock/>
    </div>
</template>

<script setup lang="ts">
import {ref, onMounted, onBeforeUnmount, reactive} from "vue";
import {useStore} from "vuex";
import {Client} from "@/models/chat/Client.ts";
import {LastMessageWithClient} from "@/models/inbox/LastMessageWithClient.ts";
import {LastMessageWithClientDTO} from "@/models/inbox/dto/LastMessageWithClientDTO.ts";
import LocalSpinner from "@/components/spinner/LocalSpinner.vue";
import {Message} from "@/models/chat/Message.ts";
import {PhoneHelpers} from "@/helpers/phones/PhoneHelpers.ts";

defineOptions({name: "Inbox"});

const store = useStore();
let chats = ref<any[]>([]);
const selectedSource = ref("all");
const selectedModule = ref("Contacts");
let loadingSpinner = ref(false);
let loadingRecords = ref(false);
let searchMode = ref(false);
let currentPage = reactive(1);

async function updateSelectedModule(module: string) {
    loadingSpinner.value = true;
    selectedModule.value = module;
    currentPage = 1;
    chats.value = [];
    await fetchClients();
    loadingSpinner.value = false;
}

async function resetSearch() {
    searchMode.value = false;
    loadingSpinner.value = true;
    currentPage = 1;
    chats.value = [];
    await fetchClients();
    loadingSpinner.value = false;
}

async function processClient(client: Client) {
    try {
        // Ищем клиента по всем доступным телефонам и username
        const searchParams = {
            whatsapp_phone:  client.phones?.whatsapp ?? null,
            viber_phone: client.phones?.viber ?? null,
            username: client.username,
            module: "Contacts"
        };

        let searchByContacts = await store.dispatch('clients/crm/findClientByPhoneOrUsername', searchParams);

        if (searchByContacts?.length) {
            return {data: searchByContacts[0], module: "Contacts"}
        }

        let searchByLeads = await store.dispatch('clients/crm/findClientByPhoneOrUsername', {
            ...searchParams,
            module: "Leads"
        });

        if (searchByLeads?.length) {
            return {data: searchByLeads[0], module: "Leads"};
        }
        return null;
    } catch (e) {
        console.error("Error processing client:", e);
        return null;
    }
}

async function fetchClients() {
    loadingRecords.value = true;
    if (!searchMode.value) {
        if (currentPage === 1) {
            loadingSpinner.value = true;
        }
        let lastMessageWithClients: Array<LastMessageWithClient> = await store.dispatch(
            "inbox/getLastMessageWithClients",
            new LastMessageWithClientDTO(currentPage, 12, selectedSource.value)
        );

        for (const lastMessageWithClient of lastMessageWithClients) {
            const crmClient = await processClient(lastMessageWithClient.client);
            const clientExists = chats.value.some(chat => chat.crmClient?.id === crmClient?.data?.id);

            if (!clientExists || crmClient == null) {
                chats.value.push({
                    lastMessageWithClient: lastMessageWithClient,
                    crmClient: crmClient?.data,
                    module: crmClient?.module
                });
            }
        }

        currentPage++;
        loadingSpinner.value = false;
        loadingRecords.value = false;
    }
}

const handleNewMessage = async (e) => {
    const message = new Message(
        e.message.sender,
        e.message.text,
        e.message.channel,
        e.message.clientId,
        e.message.sendType,
        e.message.messageRead,
        e.message.fileUrl,
        e.message.error,
        e.message.messageId,
        e.message.status,
        e.message.sentAt,
        e.message.id
    );
    const clientId = message.client_id;

    let repositoryClient: Client = await store.dispatch(
        'clients/repository/getClientById',
        new Client(clientId, null, null)
    );

    const existingChatIndex = chats.value.findIndex(
        chat => chat.lastMessageWithClient.client?.id == clientId
    );

    if (existingChatIndex !== -1) {
        const existingChat = chats.value[existingChatIndex];
        existingChat.lastMessageWithClient.lastMessage = message.text;
        existingChat.lastMessageWithClient.sendTime = message.send_time;
        existingChat.lastMessageWithClient.fileUrl = message.fileUrl;

        if (!message.message_read && store.state.inbox.openChatId != message.client_id) {
            existingChat.lastMessageWithClient.unreadMessagesCount += 1;
        }
    } else {
        const newLastMessageWithClient = new LastMessageWithClient(
            repositoryClient,
            message.text,
            message.channel,
            message.sender,
            message.fileUrl,
            message.send_time,
            message.message_read ? 0 : 1
        );

        chats.value.push({
            lastMessageWithClient: newLastMessageWithClient,
            crmClient: null
        });
    }

    chats.value.sort((a, b) =>
        b.lastMessageWithClient.unreadMessagesCount - a.lastMessageWithClient.unreadMessagesCount
    );
};

async function updateSourceFilter(newSource: string) {
    if (!searchMode.value) {
        loadingSpinner.value = true;
        selectedSource.value = newSource;
        currentPage = 1;
        chats.value = [];
        await fetchClients();
        loadingSpinner.value = false;
    }
}

async function updateSearchResults(results: any) {
    loadingSpinner.value = true;
    searchMode.value = true;
    let searchedChats = [];

    if (!results || results.length === 0) {
        loadingSpinner.value = false;
        return;
    }

    chats.value = [];

    for (const clientsModulesData of results) {
        if (!clientsModulesData?.data || clientsModulesData.data.length === 0) {
            continue;
        }

        for (const client of clientsModulesData.data) {
            if (!client) continue;
            let dbClient: Client | null = null;
            const clientData = client;

            // Подготовка данных для поиска
            const phone = PhoneHelpers.validateAndFormatPhoneToGlobal(clientData?.Phone);
          const telegramNicknameRaw = clientData?.Telegram_nickname;
          const telegramNickname = telegramNicknameRaw?.replace(/^@/, '') ?? null;
            const viberPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(clientData?.Phone ?? '');
            const whatsappPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(clientData?.WhatsApp ?? '');

            const phones = {};
            if (viberPhone !== 'invalid') phones['viber'] = viberPhone;
            if (whatsappPhone !== 'invalid') phones['whatsapp'] = whatsappPhone;

            // Ищем клиента по любому из доступных контактов
            let findClient: Client | null = await store.dispatch(
                'clients/repository/getClientByPhonesOrUsername',
                {
                    phones: Object.keys(phones).length > 0 ? phones : null,
                    username: telegramNickname
                }
            );
          console.log(findClient)
            if (!findClient) {
                // Создаем нового клиента с доступными контактами
                const phones = {};
                if (viberPhone !== 'invalid') phones['viber'] = viberPhone;
                if (whatsappPhone !== 'invalid') phones['whatsapp'] = whatsappPhone;

                if (Object.keys(phones).length > 0 || telegramNickname) {
                    let createdClient: Client | null = await store.dispatch(
                        'clients/repository/upsertClient',
                        new Client(null, phones, telegramNickname)
                    );
                    dbClient = createdClient;
                }
            } else {
                dbClient = findClient;

                // Обновляем данные клиента при необходимости
                const currentPhones = findClient.phones || {};
                const updatedPhones = {...currentPhones};
                let needsUpdate = false;

                // Добавляем недостающие телефоны
                if (phone !== 'invalid' && !currentPhones.telegram) {
                    updatedPhones.telegram = phone;
                    needsUpdate = true;
                }
                if (viberPhone !== 'invalid' && !currentPhones.viber) {
                    updatedPhones.viber = viberPhone;
                    needsUpdate = true;
                }
                if (whatsappPhone !== 'invalid' && !currentPhones.whatsapp) {
                    updatedPhones.whatsapp = whatsappPhone;
                    needsUpdate = true;
                }

                // Проверяем нужно ли обновить username
                const shouldUpdateUsername = telegramNickname && findClient.username !== telegramNickname;

                if (needsUpdate || shouldUpdateUsername) {
                    let updateClientData = new Client(
                        findClient.id,
                        needsUpdate ? updatedPhones : currentPhones,
                        shouldUpdateUsername ? telegramNickname : findClient.username
                    );

                    let updateClient: Client | null = await store.dispatch(
                        'clients/repository/updateClientById',
                        updateClientData
                    );
                    dbClient = updateClient;
                }
            }

            if (dbClient) {
                let lastMessageWithClient = await store.dispatch('inbox/searchClientWithLastMessage', {
                    phone: dbClient.phones?.telegram ?? dbClient.phones?.viber ?? dbClient.phones?.whatsapp ?? null,
                    username: dbClient.username
                });

                if (searchMode.value) {
                    searchedChats.push({
                        lastMessageWithClient: lastMessageWithClient,
                        crmClient: clientData,
                        module: clientsModulesData?.module
                    });
                } else {
                    break;
                }
            }
        }
    }

    if (searchMode.value) {
        chats.value = searchedChats;
    }
    loadingSpinner.value = false;
}

async function selectChat(data: { lastMessageWithClient: LastMessageWithClient, crmClient: any }) {
    store.commit("inbox/setOpenChat");
  console.log("open chat ",store.state.inbox.openChatId )
    let repositoryClient: Client = await store.dispatch(
        'clients/repository/getClientById',
        new Client(data.lastMessageWithClient.client.id, null, null)
    );
    store.commit("clients/repository/setClient", repositoryClient);
    store.commit("clients/repository/setClientSource", data.lastMessageWithClient.lastChannel);
    store.commit("clients/crm/setCrmClient", data.crmClient ?? null);
}

onMounted(async () => {
    console.log("start inbox")
    window.Echo.channel('inbox-messages')
        .listen('.new_message', handleNewMessage);
    loadingSpinner.value = true;
    await fetchClients();
    loadingSpinner.value = false;
});

onBeforeUnmount(async () => {
    window.Echo.leave(`inbox-messages`);
});
</script>

<style scoped>
.inbox-panel {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
    background-color: #f8f9fa;
    z-index: 1000;
}

.separator {
    height: 1px;
    background-color: #e1e1e1;
    margin: 20px;
}

.module-block {
    padding: 0 0 0 20px;
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
}
</style>
