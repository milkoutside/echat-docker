<template>
    <div
        :class="[
            'message',
            message.send_type === 'outgoing' ? 'outgoing-message' : 'incoming-message',
            message.status === 'error' && 'error-message'
        ]"
        :title="message.status === 'error' ? message.error : ''"
    >
        <div class="message-content">
            <div class="text-container">
                <div v-if="getFileType(message.fileUrl) == null">
                    <MessageTypeText :message="message"></MessageTypeText>
                </div>
                <div v-else-if="getFileType(message.fileUrl) === 'image'">
                    <MessageTypeImage :message="message"></MessageTypeImage>
                </div>
                <div v-else-if="getFileType(message.fileUrl) === 'video'">
                    <MessageTypeVideo :message="message"></MessageTypeVideo>
                </div>
                <div v-else-if="getFileType(message.fileUrl) === 'audio'">
                    <MessageTypeAudio :message="message"></MessageTypeAudio>
                </div>
                <div v-else-if="getFileType(message.fileUrl) === 'file'">
                    <MessageTypeFile :message="message"></MessageTypeFile>
                </div>
                <span class="message-time">{{ formatTime(message.send_time) }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Message } from '@/models/chat/Message';
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";
import MessageTypeImage from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeImage.vue";
import MessageTypeVideo from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeVideo.vue";
import MessageTypeAudio from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeAudio.vue";
import MessageTypeFile from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeFile.vue";

defineOptions({
    name: 'MessageItem',
});

interface Props {
    message: Message;
}

function formatTime(sendTime: string): string {
    const date = new Date(sendTime);
    return date.toLocaleTimeString('uk-UA', {
        hour: '2-digit',
        minute: '2-digit'
    });
}
function getFileType(url) {
    // Проверяем, является ли URL Data URL
    if(url == null) return null;
    if (url.startsWith('data:')) {
        const mimeType = url.split(';')[0].split(':')[1];

        if (mimeType.startsWith('image/')) return 'image';
        if (mimeType.startsWith('audio/')) return 'audio';
        if (mimeType.startsWith('video/')) return 'video';

        return null;
    }

    // Для обычных URL
    const extension = url
        .split('/').pop()
        .split('?')[0]
        .split('#')[0]
        .split('.').pop()
        .toLowerCase();

    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    const audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'];
    const videoExtensions = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'm4v'];

    if (imageExtensions.includes(extension)) return 'image';
    if (audioExtensions.includes(extension)) return 'audio';
    if (videoExtensions.includes(extension)) return 'video';
    return "file";
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
