<template>
    <div class="spinner-block">
        <div class="content">
            <v-progress-circular
                color="primary"
                indeterminate
            ></v-progress-circular>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useStore} from "vuex";

const store = useStore();
defineOptions({
    name: 'LocalSpinner',
});
</script>

<style scoped>
.spinner-block {
    position: relative;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 99999999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.content {
    text-align: center;
    max-width: 80%;
    padding: 20px;
}

</style>
