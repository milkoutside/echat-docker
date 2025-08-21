<template>
    <div>
        <p class="message-text">{{ message.text }}</p>
    </div>
</template>

<script setup lang="ts">
import {Message} from '@/models/chat/Message';

defineOptions({
    name: 'MessageTypeText',
});

interface Props {
    message: Message;
}

function formatTime(sendTime: string): string {
    const date = new Date(sendTime);
    return date.toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

const props = defineProps<Props>();
</script>

<style scoped>
.message {
    display: flex;
    margin-bottom: 1.25rem;
    transition: transform 0.2s ease;
}

.message-content {
    max-width: 85%;
    position: relative;
}

.text-container {
    padding: 14px 20px;
    border-radius: 14px;
    position: relative;
    display: flex;
    flex-direction: column;
    word-break: break-word;
    line-height: 1.4;
    width: fit-content;
    max-width: 100%;
}

.message-text {
    margin: 0;
    color: white;
    text-align: left;
    font-size: 0.95rem;
    letter-spacing: 0.02em;
    width: 100%;
    padding-right: 40px;
}

.message-time {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.65);
    margin-top: 4px;
    align-self: flex-end;
    position: absolute;
    right: 12px;
    bottom: 8px;
}


.incoming-message .text-container {
    background: #4A90E2;
    border-radius: 14px 14px 14px 4px;
    align-items: flex-start;
}


.outgoing-message {
    justify-content: flex-end;
}

.outgoing-message .text-container {
    background: #00B884;
    border-radius: 14px 14px 4px 14px;
    align-items: flex-end;
}


.error-message .text-container {
    background: #FF4757;
}

@media (min-width: 480px) {
    .text-container::after {
        display: none;
    }
}
</style>
