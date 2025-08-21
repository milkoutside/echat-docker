<template>
    <div class="mb-3">
        <div class="d-block text-center">
            <audio controls :src="message.fileUrl"></audio>
        </div>
        <div class="mt-1" v-if="message.text != null">
            <MessageTypeText :message="message"/>
        </div>
    </div>
</template>

<script setup lang="ts">

import type { Message } from '@/models/chat/Message';
import {useStore} from 'vuex';
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";

const store = useStore();
defineOptions({
    name: 'MessageTypeAudio',
});
interface Props {
    message: Message;
}

const props = defineProps<Props>();

</script>

<style scoped>
@media (max-width: 600px) {
    audio{
        width: 50vw;
    }
}

</style>
