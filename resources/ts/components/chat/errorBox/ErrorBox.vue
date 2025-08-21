<template>
    <div class="error-box-block d-flex justify-content-center align-items-center">
        <p class="fs-2">{{errorMessage}}</p>
    </div>
</template>

<script setup lang="ts">
defineOptions({
    name: 'ErrorBox',
});
interface Props {
    errorMessage: string;
}
const props = defineProps<Props>();
</script>

<style scoped>
.error-box-block{
    height: 100vh;
}
</style>
