<template>
    <div>
        <div v-if="store.state.clients.repository.clientSource === 'telegram'">
            <i class="fab fa-telegram fa-xl" style="color: #0088cc;"></i>
        </div>
        <div v-else-if="store.state.clients.repository.clientSource  === 'viber'">
            <i class="fab fa-viber fa-xl" style="color: #7360f2;"></i>
        </div>
      <div v-else-if="store.state.clients.repository.clientSource  === 'whatsapp'">
        <i class="fab fa-whatsapp fa-xl" style="color: #63E6BE;"></i>
      </div>
<!--        <div v-if="store.state.clients.repository.clientSource === 'all'">-->
<!--            <span-->
<!--                style="cursor: pointer;"-->
<!--                  class="fs-5 mb-0 fw-bold icon-zoom-anim">ALL</span>-->
<!--        </div>-->
    </div>
</template>

<script setup>
import {useStore} from 'vuex';

const store = useStore();
defineOptions({
    name: 'ChatSources',
});


</script>

<style scoped>
.fab{
    cursor: pointer;
}
.fab:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}
</style>
