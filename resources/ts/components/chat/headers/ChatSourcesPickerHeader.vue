<template>
    <div class="chat-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <i
                    class="fas fa-arrow-left fa-xl"
                    style="color: rgb(170 155 155);cursor: pointer;"
                    @click="changeSourceEmit('ChatHeader')"
                ></i>
            </div>
            <div class="col text-center">
                <div class="d-flex align-items-center justify-content-center gap-5">
                    <i @click="changeSource('telegram')"
                        class="fab fa-telegram fa-xl" style="color: #0088cc;"></i>
                    <i @click="changeSource('viber')"
                        class="fab fa-viber fa-xl" style="color: #7360f2;"></i>
<!--                    <span @click="changeSource('all')"-->
<!--                          style="cursor: pointer;"   class="fs-5 mb-0 fw-bold icon-zoom-anim">ALL</span>-->
                  <i @click="changeSource('whatsapp')"
                     class="fab fa-brands fa-whatsapp fa-xl" style="color: #63E6BE;"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {useStore} from 'vuex';

const store = useStore();
defineOptions({
    name: 'ChatSourcesPickerHeader',
});
const emit = defineEmits(['change-header','change-source']);
const changeSourceEmit = (headerName) => {
    emit('change-source', headerName);
};
const changeSource = (source) => {
    if(store.state.clients.repository.clientSource !== source) {
        store.commit('clients/repository/setClientSource', source);
        store.commit('echat/setSelectedSender', null);
        console.log("change source", source)
    }
    changeSourceEmit('ChatHeader');
};
</script>

<style scoped>
.fab, .fas {
    cursor: pointer;
}

.fab:hover, .fas:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}
.icon-zoom-anim:hover{
    transform: scale(1.1);
    cursor: pointer;
}
.chat-header {
    height: 100%;
    border-bottom: 1px solid #d6d6d6;
    background-color: #fbfbfb;
    display: flex;
    align-items: center;
}

.row {
    width: 100%;
    margin: 0;
}

.text-center {
    text-align: center;
}

.d-flex {
    display: flex;
}

.gap-3 {
    gap: 1rem;
}

.align-items-center {
    align-items: center;
}

.justify-content-center {
    justify-content: center;
}

.ms-2 {
    margin-left: 0.5rem;
}
</style>
