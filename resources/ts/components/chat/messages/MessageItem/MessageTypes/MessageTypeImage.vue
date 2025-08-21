<template>
    <div class="mb-3">
        <div class="d-block text-center">
            <viewer :images="[message.fileUrl]">
                <img class="preview-image" :src="message.fileUrl" alt="">
            </viewer>
        </div>
        <div class="mt-1" v-if="message.text != null">
            <MessageTypeText :message="message"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import {Message} from '@/models/chat/Message';
import {useStore} from 'vuex';
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";

const store = useStore();
defineOptions({
    name: 'MessageTypeImage',
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
.preview-image{
    border-radius: 8px;
    width: 100%;
    max-height: 300px;
}
</style>
