<script setup lang="ts">
import {useStore} from "vuex";
import Settings from "@/components/settings/Settings.vue";

const store = useStore();
defineOptions({
    name: 'InboxPage',
});
</script>

<template>
    <div class="row">
        <div class="col-3" style="padding-right: 0px">
            <Inbox></Inbox>
        </div>
        <div class="col-9" style="padding-left: 0px">
            <Chat v-if="store.state.inbox.openChat"></Chat>
            <Settings v-if="store.state.settings.isOpen"></Settings>
        </div>
    </div>
</template>

<style scoped>

</style>
