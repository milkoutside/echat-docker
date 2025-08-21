<template>
    <div>
        <button @click="toggleDropdown" class="module-filter">
            Модуль
        </button>
        <div v-if="isOpen" class="dropdown-content">
            <div
                v-for="option in options"
                :key="option.value"
                @click="selectOption(option.value)"
                class="dropdown-item"
            >
                <span>{{ option.label }}</span>
                <span v-if="selectedModule === option.value" class="checkmark">✓</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, defineEmits } from "vue";

defineOptions({
    name: 'ModuleFilter',
});

const emit = defineEmits(["update:selectedModule"]);
const isOpen = ref(false);
const selectedModule = ref("Contacts");

const options = [
    { value: 'Contacts', label: 'Клієнти'},
    { value: 'Leads', label: 'Ліди'},
];

function toggleDropdown() {
    isOpen.value = !isOpen.value;
}

function selectOption(source: string) {
    selectedModule.value = source;
    emit("update:selectedModule", source);
    isOpen.value = false;
}
</script>

<style scoped>
.module-filter {
    margin-right: 10px;
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
    min-width: 100px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    border-radius: 5px;
    margin-top: 5px;
}

.dropdown-item {
    padding: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
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