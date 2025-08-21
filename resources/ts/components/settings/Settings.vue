<template>
    <v-container>
        <v-row justify="space-between" class="mb-4">
            <v-col>
                <h2 class="text-h5">Відправники</h2>
            </v-col>
            <v-col class="text-right">
                <v-btn color="primary" @click="openDialog">Додати</v-btn>
            </v-col>
        </v-row>

        <v-data-table
            :items="senders"
            :headers="headers"
            items-per-page-text="За сторінку"
            item-value="id"
            class="elevation-1"
        >
            <template v-slot:item.actions="{ item }">
                <v-btn  color="red" @click="deleteSender(item)">
                    Видалити
                </v-btn>
            </template>
        </v-data-table>

        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>Додати відправника</v-card-title>
                <v-card-text>
                    <v-form ref="form">
                        <!-- Валидация для выбора сервиса -->
                        <v-select
                            v-model="newSender.service"
                            :items="services"
                            label="Сервіс"
                            :rules="[v => !!v || 'Оберіть сервіс']"
                            required
                        ></v-select>

                        <!-- Валидация для номера отправителя -->
                        <v-text-field
                            v-model="newSender.sender"
                            label="Номер відправника"
                            :rules="[validateSender]"
                            required
                        ></v-text-field>

                        <!-- Валидация для API-ключа -->
                        <v-text-field
                            v-model="newSender.apiKey"
                            label="API Key"
                            :rules="[v => !!v || 'API Key обов\'язковий']"
                            required
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="red" @click="dialog = false">Вийти</v-btn>
                    <v-btn color="green" @click="validateAndAddSender">Додати</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { EchatSender } from "@/models/echat/EchatSender";

const store = useStore();
const dialog = ref(false);
const newSender = ref(new EchatSender(null, "", "", ""));
const form = ref(null);

const services = ["telegram", "viber","whatsapp"];

const headers = ref([
    { title: "Відправник", key: "sender", sortable: true },
    { title: "Сервіс", key: "service", sortable: true },
    { title: "", key: "actions", sortable: false }
]);

const senders = computed(() => store.state.echat.senders??[]);

onMounted(() => {
    store.dispatch("echat/getSenders");
    console.log(store.state.echat.senders)
});

const openDialog = () => {
    newSender.value = new EchatSender(null, "", "", "");
    dialog.value = true;
};
const deleteSender = async (sender) => {
    console.log("sender",sender)
    await store.dispatch("echat/deleteSender", sender.id);
    console.log("start delete 2")
    await refreshSenders();
};

// Валидация номера отправителя
const validateSender = (value) => {
    if (!value) return "Номер відправника обов'язковий";
    return true;
};

// Валидация и добавление отправителя
const validateAndAddSender = async () => {
    const { valid } = await form.value.validate(); // Проверяем валидность формы
    if (valid) {
        addSender();
    }
};

const addSender = async () => {
    console.log("start add")
    await store.dispatch("echat/createSender", {id:null,sender:newSender.value.sender, service:newSender.value.service, apikey:newSender.value.apiKey});
    console.log("start add 2")
    await refreshSenders();
    dialog.value = false;
};
const refreshSenders = async () => {
    let senders = await store.dispatch("echat/getSenders");
    console.log("refresh senders",senders)
    store.commit("echat/setSenders", senders);
}
</script>
