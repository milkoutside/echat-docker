<template>
  <v-dialog v-model="showModal" persistent max-width="500">
    <template #default>
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span>Надіслати файл</span>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <label
              class="file-upload d-flex flex-column align-center justify-center"
              for="fileInput"
              @dragover.prevent
              @drop.prevent="handleFileDrop"
          >
            <input type="file" id="fileInput" @change="handleFileChange" hidden/>
            <v-icon v-if="!fileName" color="primary" size="40">mdi-file-upload</v-icon>
            <div class="mt-3 word-break-block">
              <span v-if="!fileName">Перетягніть файл сюди або натисніть для вибору</span>
              <span v-else class="text-success fw-bold">Файл: {{ fileName }}</span>
            </div>
          </label>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="d-flex justify-end">
          <v-btn color="error" text @click="closeModal">Вийти</v-btn>
          <div v-if="(!fileName || !validSendButton)">
            <v-btn class="text-primary-disabled" :title="sendButtonTooltip" color="primary">Надіслати</v-btn>

          </div>
          <div v-else>
            <v-btn color="primary" @click="sendFile">Надіслати</v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </template>
  </v-dialog>
</template>

<script setup lang="ts">
import {computed, reactive, ref} from 'vue';
import {useStore} from 'vuex';
import axios from 'axios';
import {Message} from "@/models/chat/Message";

defineOptions({
  name: 'SendFileModal',
});


const store = useStore();
const showModal = ref(true);
const fileName = ref<string | null>(null);
const file = ref<File | null>(null);
const validSendButton = computed(() => {
    const hasSender = !!store.state.echat.selectedSender;
    const source = store.state.clients.repository.clientSource;

    if (!hasSender || source === 'all') {
        return false;
    }

    const client = store.state.clients.repository.client;

    // Для Telegram проверяем только username
    if (source === 'telegram') {
        return !!client?.username;
    }

    // Для WhatsApp/Viber проверяем телефон
    if (['whatsapp', 'viber'].includes(source)) {
        const phone = client?.phones?.[source] || client?.phone; // Поддержка старой структуры
        return !!phone;
    }

    return true;
});

let sendButtonTooltip = computed(() => {
    const hasSender = !!store.state.echat.selectedSender;
    const source = store.state.clients.repository.clientSource;
    const client = store.state.clients.repository.client;

    if (!hasSender) {
        return 'Будь ласка, Оберіть відправника!';
    }

    if (source === 'all') {
        return 'Будь ласка, оберіть конкретний канал відправки!';
    }

    // Проверки для Telegram
    if (source === 'telegram' && !client?.username) {
        return 'Для відправки в Telegram потрібен username!';
    }

    // Проверки для WhatsApp
    if (source === 'whatsapp') {
        const phone = client?.phones?.[source] || client?.phone;
        if (!phone) {
            return 'Для відправки на WhatsApp потрібен номер телефону!';
        }
        if (PhoneHelpers.validateAndFormatPhoneToGlobal(phone) === 'invalid') {
            return 'Пустий або невірний формат номеру телефону!';
        }
    }

    // Проверки для Viber
    if (source === 'viber') {
        const phone = client?.phones?.[source] || client?.phone;
        if (!phone) {
            return 'Для відправки на Viber потрібен номер телефону!';
        }
        if (PhoneHelpers.validateAndFormatPhoneToGlobal(phone) === 'invalid') {
            return 'Пустий або невірний формат номеру телефону!';
        }
    }

    return '';
});

const closeModal = () => {
  store.commit('modals/sendFileModal/setClose');
};


let handleFileChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const selectedFile = input.files ? input.files[0] : null;

  if (selectedFile) {
    file.value = selectedFile;
    fileName.value = selectedFile.name;
  } else {
    file.value = null;
    fileName.value = null;
  }
};


let handleFileDrop = (event: DragEvent) => {
  event.preventDefault();
  const droppedFile = event.dataTransfer?.files[0];

  if (droppedFile) {
    file.value = droppedFile;
    fileName.value = droppedFile.name;
  } else {
    file.value = null;
    fileName.value = null;
  }
};

let sendFile = async () => {
  if (file.value) {
    // Транслитерируем имя файла
    const originalName = file.value.name;
    const decodedName = decodeURIComponent(originalName);
    const transliteratedName = transliterate(decodedName);

    // Создаем новый File объект с транслитерированным именем
    const renamedFile = new File([file.value], transliteratedName, {
      type: file.value.type,
      lastModified: file.value.lastModified
    });

    let message = reactive(store.state.messages.sendMessages.newMessage);

    message.client_id = store.state.clients.repository.client.id;
    message.channel = store.state.clients.repository.clientSource;
    message.send_type = 'outgoing';
    message.sender = store.state.echat.selectedSender;
    message.text = "";
    console.log('sender echat', message.sender)
    message.message_read = true;

    const formData = new FormData();
    formData.append('file', renamedFile); // Используем файл с новым именем
    formData.append('client_id', store.state.clients.repository.client.id);
    try {
      const response = await axios.post('api/client-files/upload-file', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      console.log('Файл успешно отправлен:', response.data);
      const [first, ...rest] = response.data.path.split('/');
      const second = rest.join('/');
      message.fileUrl = `https://host.aguaqui.pt/storage/${first}/${second}`;
      await store.dispatch('messages/sendMessages/sendMessage', message);
      console.log(`file - `, message);
      closeModal();
    } catch (error) {
      console.error('Ошибка отправки файла:', error);
    }
  } else {
    console.error('Файл отсутствует');
  }
};
function transliterate(str) {
  const map = {
    'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh', 'З': 'Z', 'И': 'I', 'Й': 'Y',
    'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F',
    'Х': 'Kh', 'Ц': 'Ts', 'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Shch', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu', 'Я': 'Ya',
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y',
    'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f',
    'х': 'kh', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
    'Ґ': 'G', 'Є': 'Ye', 'І': 'I', 'Ї': 'Yi',
    'ґ': 'g', 'є': 'ye', 'і': 'i', 'ї': 'yi',
    ' ': '_', // Заменяем пробелы на подчеркивания
  };

  return str.replace(/[А-Яа-яЁёҐЄІЇґєії\s]/g, letter => map[letter] || '');
}
</script>

<style scoped>
.word-break-block{
  word-wrap: break-word;
  width: 100%;
}
.file-upload {
  cursor: pointer;
  width: 100%;
  height: 150px;
  border: 2px dashed var(--v-primary-lighten2, #ccc);
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  transition: border-color 0.3s ease;
  background-color: var(--v-primary-lighten5, #f9f9f9);
}

.file-upload:hover {
  border-color: var(--v-primary, #1976d2);
}

.text-success {
  color: #4caf50 !important;
}

.text-primary-disabled {
  color: rgb(172 172 172) !important;
  cursor: default !important;
}
</style>
