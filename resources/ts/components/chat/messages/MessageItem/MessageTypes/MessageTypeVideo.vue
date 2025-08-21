<template>
    <div class="mb-3">
        <div class="d-block text-center">
            <VideoPlayer
                class="video-js"
                :options="{
                controls: true,
                controlBar: {
        children: ['playToggle','VolumeBar']
    },
                sources: [{ src: message.fileUrl, type: 'video/mp4' }]
            }"
                @mounted="handleMounted"
                @event="handleEvent"
            />
        </div>
        <div class="mt-1" v-if="message.text != null">
            <MessageTypeText :message="message"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Message } from '@/models/chat/Message';
import { VideoPlayer } from '@videojs-player/vue';
import 'video.js/dist/video-js.css';
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";

defineOptions({
    name: 'MessageTypeVideo',
});

defineProps<{
    message: Message;
}>();

const player = ref(null);

const handleMounted = (payload: any) => {
    player.value = payload.player;
    console.log('Basic player mounted', payload);
};

const handleEvent = (log: any) => {
    console.log('Basic player event', log);
};
</script>

<style>
.video-js{
    border-radius: 8px;
}
@media (max-width: 600px) {
    .vjs_video_3-dimensions {
        width: 50vw !important;
        height: 25vh !important;
    }
    .vjs-big-play-button {

        top: 40%!important;
        left: 40%!important;
    }
    .vjs-big-play-button {

        top: 10%!important;
        left: 11%!important;
    }
}


@media (min-width: 601px) {
    .vjs_video_3-dimensions {
        width: 40vw !important;
        height: 38vh !important;
    }
    .vjs-big-play-button {

        top: 40%!important;
        left: 40%!important;
    }
}


</style>
