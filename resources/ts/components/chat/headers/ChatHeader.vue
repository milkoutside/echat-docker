<template>
    <div class="chat-header">
        <div class="row align-items-center">
            <div class="col-6 d-flex justify-content-end align-items-center">
            <span class="edit-client-event fs-5 mb-0 ms-2">
                <template v-if="store?.state?.clients?.crm?.crmClient?.id">
                    <a :href="'https://crm.zoho.eu/crm/org20104135465/tab/'+store.state.clients.crm.module+'/' + store.state.clients.crm.crmClient.id"
                       target="_blank"
                       class="client-link">
                        {{ store.state.clients.crm.crmClient.Full_Name }}
                    </a>
                </template>
                <template v-else>
                    {{
                        store?.state?.clients?.crm?.crmClient?.Full_Name
                            ? store?.state?.clients?.crm?.crmClient?.Full_Name
                            : (store?.state?.clients?.repository?.client?.phone
                                ? store?.state?.clients?.repository?.client?.phone
                                : store?.state?.clients?.repository?.client?.username)
                    }}
                </template>
            </span>
            </div>
            <div class="col-4 d-flex justify-content-center align-items-center">
                <div class="row align-items-center">
                    <div class="col-12 d-flex justify-content-end">
                        <div class="w-75" style="position:relative; top:12px">
                            <v-select
                                label="Відправник"
                                :items="sendersByService"
                                item-title="sender"
                                item-value="sender"
                                v-model="selectedSender"
                                variant="outlined"
                                density="compact"
                            ></v-select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2 d-flex justify-content-end align-items-center">
                <ChatSources @click="changeHeaderEmit('ChatSourcesPickerHeader')"></ChatSources>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import ChatSources from "@/components/chat/headers/sources/ChatSources.vue";
import {useRoute} from 'vue-router';
import {useStore} from "vuex";
import {computed} from "vue";

defineOptions({
    name: 'ChatHeader',
});
const store = useStore();
const route = useRoute();
console.log("route", route.fullPath)
const emit = defineEmits(['change-header']);
const changeHeaderEmit = (headerName) => {
    emit('change-header', headerName);
};

let sendersByService = computed(() => {
    return store.getters['echat/getSendersByService']; // Убираем Proxy
});

// Используем вычисляемое свойство для v-model
let selectedSender = computed({
    get: () => store.state.echat.selectedSender,
    set: (value) => store.commit('echat/setSelectedSender', value),
});
</script>

<style scoped>
.edit-client-event {
    cursor: pointer;
}

.edit-client-event:hover {
    color: #0088cc;
}

.fab {
    cursor: pointer;
}

.fab:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}

.chat-header {
    height: 100%;
    border-bottom: 1px solid #d6d6d6;
    background-color: #fbfbfb;
    display: flex;
    align-items: center;
}

.row {
    width: 100%;
    margin: 0;
}

.text-center {
    text-align: center;
}

.d-flex {
    display: flex;
}

.gap-3 {
    gap: 1rem;
}

.align-items-center {
    align-items: center;
}

.justify-content-center {
    justify-content: center;
}

.ms-2 {
    margin-left: 0.5rem;
}
.client-link {
    color: #007bff; /* Синий цвет */
    text-decoration: none; /* Убираем подчеркивание */
    font-weight: bold;
}

.client-link:hover {
    text-decoration: underline; /* Подчеркивание при наведении */
}
</style>
