<template>
    <div>
        <div class="chat-window px-3 py-3">
            <div v-for="(messagesByDate, date) in groupedMessages" :key="date" class="date-group">
                <!-- Отображение даты в центре -->
                <div class="date-header">{{ date }}</div>

                <!-- Отображение сообщений за текущую дату -->
                <div v-for="message in messagesByDate" :key="message.id" class="message-item">
                    <MessageItem :message="message" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {Message} from '@/models/chat/Message.js';
import {computed} from "vue";

defineOptions({
    name: 'MessageList',
});


interface Props {
    messages: Message[];
}
const props = defineProps<Props>();

const groupedMessages = computed(() => {
    const groups: Record<string, Message[]> = {};

    for (const message of props.messages) {
        if (message?.fileUrl?.includes('.tgs')) {
            continue;
        }
        const date = formatDate(message.send_time || "");
        if (!groups[date]) {
            groups[date] = [];
        }
        groups[date].push(message);
    }

    return groups;
});

function formatDate(sendTime: string): string {
    const dateObj = new Date(sendTime);
    return dateObj.toLocaleDateString('uk-UA', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<style scoped>
.chat-window {
    height: 100%;
}

.date-group {
    margin-bottom: 20px;
}

.date-header {
    text-align: center;
    font-weight: bold;
    margin: 10px 0;
    color: #555;
}

.message-item {
    margin-bottom: 10px;
}
</style>
