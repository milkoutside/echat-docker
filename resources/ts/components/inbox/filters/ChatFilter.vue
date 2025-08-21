<template>

    <div class="dropdown" ref="dropdown">
        <button @click="toggleDropdown" class="dropdown-button">
            Фільтри
        </button>
        <div v-if="isOpen" class="dropdown-content">
            <div
                v-for="option in options"
                :key="option.value"
                @click="selectOption(option.value)"
                class="dropdown-item"
            >
                <i :class="option.icon"></i>
                <span>{{ option.label }}</span>
                <span v-if="selectedSource === option.value" class="checkmark">✓</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, defineEmits } from "vue";

defineOptions({
    name: 'ChatFilter',
});

const emit = defineEmits(["update:source"]);
const isOpen = ref(false);
const selectedSource = ref("all");


const options = [
    { value: 'all', label: 'Всі', icon: null },
    { value: 'telegram', label: 'Telegram', icon: 'fa-brands fa-telegram' },
    { value: 'viber', label: 'Viber', icon: 'fa-brands fa-viber' },
    { value: 'whatsapp', label: 'Whatsapp', icon: 'fa-brands fa-whatsapp' }
];

function toggleDropdown() {
    isOpen.value = !isOpen.value;
}

function selectOption(source: string) {
    selectedSource.value = source;
    emit("update:source", source);
}
</script>

<style scoped>
.dropdown {
    padding-top: 15px;
    position: relative;
    display: inline-block;
}

.dropdown-button {
    background-color: #ffffff;
    border: none;
    padding: 5px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.dropdown-content {
    display: block;
    position: absolute;
    background-color: #fff;
    min-width: 130px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 999999999;
    border-radius: 5px;
    margin-top: 5px;
}

.dropdown-item {
    padding: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.dropdown-item i {
    margin-right: 10px;
}

.dropdown-item:hover {
    background-color: #f1f1f1;
}

.checkmark {
    padding-left: 10px;
    color: #888 !important;
    font-size: 0.8rem;
    font-weight: normal;
}
</style>
