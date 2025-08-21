<template>
    <div class="settings-block">
        <button @click="openSettings()" class="settings-button"><i class="fa-solid fa-gear"></i></button>
    </div>
</template>

<script setup lang="ts">

import {useStore} from "vuex";

defineOptions({
    name: 'SettingsBlock',
});
const store = useStore();
function openSettings() {
    store.commit('inbox/setCloseChat');
    store.commit('inbox/setOpenChatId',null);
    store.commit('settings/setOpen');
}
</script>

<style scoped>
.settings-block {
    border: 1px solid #f0f0f0;
    display: flex;
    justify-content: center;
    padding: 8px 5px 8px 5px;
    background-color: #ffffff;
    text-align: center;
    box-shadow: 0 -1px 2px #e6e2e2cc;
    flex-direction: column-reverse;
}

.settings-button {
    background-color: #007bff;
    color: rgb(255, 255, 255);
    border: none;
    padding: 5px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

.settings-button:hover {
    background-color: #0056b3;
}
</style>
