<template>
  <div v-if="store.state.ui.spinner.isShow" class="blur-background">
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
    name: 'Spinner',
});
</script>

<style scoped>
.blur-background {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  background-color: rgba(255, 255, 255, 0.5);
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

.gradient-text {
  font-size: 24px;
  background: linear-gradient(45deg, #ff00ff, #00ffff, #ffff00, #ff00ff);
  background-size: 400% 400%;
  text-align: center;
  -webkit-background-clip: text;
  color: transparent;
  animation: gradientAnimation 9s linear infinite;
}

@keyframes gradientAnimation {
  0% {
    background-position: 0% 50%;
    color: #ff00ff;
  }
  25% {
    background-position: 25% 50%;
    color: #00ffff;
  }
  50% {
    background-position: 50% 50%;
    color: #ffff00;
  }
  75% {
    background-position: 75% 50%;
    color: #ff00ff;
  }
  100% {
    background-position: 100% 50%;
    color: #ff00ff;
    animation-timing-function: ease-out;
    animation-duration: 1s;
  }
}
</style>
