<template>
    <div class="mb-3 file-container">
        <div class="d-flex flex-column align-items-center">
            <a :href="message.fileUrl"
               :download="fileName"
               class="file-link text-decoration-none">
                <i class="file-icon fa-solid fa-file"></i>
                <div class="file-name mt-1 text-center">{{ fileName }}</div>
            </a>
        </div>
        <div class="mt-2" v-if="message.text">
            <MessageTypeText :message="message" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";
import { Message } from "@/models/chat/Message";

defineOptions({
    name: 'MessageTypeFile',
});

const props = defineProps<{ message: Message }>();

const fileName = computed(() => {
  const cleanUrl = props.message.fileUrl.split('?')[0]; // Удаляем query-параметры
  const decodedUrl = decodeURIComponent(cleanUrl); // Декодируем URL
  return decodedUrl.split('/').pop() || 'file';
});
</script>

<style scoped>
.file-container {
    max-width: 200px;
}

.file-link {
    color: #6c757d;
    transition: all 0.3s ease;
    text-align: center;
}

.file-link:hover {
    color: #007bff;
    transform: scale(1.05);
}

.file-icon {
    font-size: 2.5rem;
    display: block;
    margin: 0 auto;
}

.file-name {
    font-size: 0.8rem;
    word-break: break-word;
    max-width: 100%;
    color: currentColor;
}
</style>
