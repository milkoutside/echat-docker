<template>
  <div class="row">
    <div class="col-11" style="padding-right: 0px">
      <div class="chat-search px-2">
        <input type="text" v-model="searchQuery" @input="onInput" placeholder="Пошук">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {ref, defineEmits, onMounted, onUnmounted} from "vue";
import {useStore} from "vuex";

defineOptions({
  name: 'ChatSearch',
});
const store = useStore();
const searchQuery = ref("");
const emit = defineEmits(["update:searchResults", "update:resetSearch"]);

// Функция debounce
function debounce(func: Function, wait: number) {
  let timeout: ReturnType<typeof setTimeout> | null = null;
  return function (...args: any[]) {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

// Обернем searchClients в debounce
const debouncedSearchClients = debounce(searchClients, 500);

function onInput() {
  debouncedSearchClients();
}

async function searchClients() {
  const query = searchQuery.value.trim();

  if (!query) {
    emit("update:resetSearch", []);
    return;
  }
  if (
      query === "3" || // Проверка на строку "3"
      /^(\+38|38)?\d{0,2}$/.test(query) || // Проверка на +38, 38 и до 2 цифр
      /^(\+380|380)?\d{0,2}$/.test(query) // Проверка на +380, 380 и до 2 цифр
  ) {
    emit("update:searchResults", [
      //{ data: [], module: "Leads" },
      { data: [], module: "Contacts" }
    ]);
    return;
  }
  try {
    const searchByContacts = await store.dispatch('clients/crm/findClientByQuery', {query: query, module: 'Contacts'});
    //const searchByLeads = await store.dispatch('clients/crm/findClientByQuery', {query: query, module: 'Leads'});
    emit("update:searchResults", [
      //{data: searchByLeads ?? [], module: "Leads"},
      {data: searchByContacts ?? [], module: "Contacts"}
    ]);

  } catch (error) {
    console.error("Помилка пошуку клієнтів:", error);
    emit("update:searchResults", []);
  }
}
</script>

<style scoped>
.chat-search {
  position: relative;
  padding-top: 10px;
}

.chat-search input {
  width: 100%;
  padding: 5px 10px 5px 35px;
  border-radius: 5px;
  border: 1px solid #ccc;
  transition: all 0.3s ease;
}

.chat-search i {
  position: absolute;
  left: 15px;
  top: 60%;
  transform: translateY(-50%);
  color: #888;
}
</style>
