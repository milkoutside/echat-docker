<template>
  <div>
    <div class="chat-container">
      <div class="chat-header-block">
        <Transition name="fade" mode="out-in">
          <component
              :is="headerComponentName"
              @change-header="changeHeaderHandle"
              @change-source="changeSourceHandle"
              :key="headerComponentName"
          ></component>
        </Transition>
      </div>
      <div class="chat-messages-block" @scroll="onScroll" ref="messagesBlock">
        <!-- Показываем спиннер при первом загрузке -->
        <LocalSpinner v-if="isFirstLoad"></LocalSpinner>
        <div v-else>
          <div v-if="isLoading" class="loading">Загрузка...</div>

          <!-- Показываем сообщение об ошибке, если оно есть -->
          <ErrorBox v-if="errorMessage" :errorMessage="errorMessage"></ErrorBox>
          <div v-else>
            <!-- Показываем сообщения, если они есть -->
            <MessageList v-if="sortedMessages.length > 0" :messages="sortedMessages"/>

            <!-- Показываем сообщение о том, что сообщений нет, если они отсутствуют -->
            <div v-else>
              <div v-if="route.fullPath.toLowerCase().includes('inbox')"
                   class="d-flex justify-content-center align-items-center h-75 w-75 position-absolute">
                <p>В цьому чаті ще немає повідомлень</p>
              </div>
              <div class="d-flex justify-content-center align-items-center h-75 w-100 position-absolute" v-else>
                <p>В цьому чаті ще немає повідомлень</p>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="chat-footer-block">
        <ChatFooter></ChatFooter>
      </div>
      <SendFileModal v-if="store.state.modals.sendFileModal.isOpen"></SendFileModal>
    </div>
  </div>
</template>

<script setup lang="ts">
import {nextTick, onMounted, reactive, ref, computed, onBeforeMount, onBeforeUnmount} from 'vue';
import {useStore} from 'vuex';
import ErrorBox from "@/components/chat/errorBox/ErrorBox.vue";
import {Message} from "@/models/chat/Message";
import {useRoute} from "vue-router";
import LocalSpinner from "@/components/spinner/LocalSpinner.vue";

defineOptions({
  name: 'Chat',
});
const route = useRoute();
const changeHeaderHandle = (headerName: string | null) => {
  headerComponentName.value = headerName;
};
const changeSourceHandle = (source: string | null) => {
  headerComponentName.value = source;
  //changeMessagesSource(source)
};
// function changeMessagesSource(source: string){
//     console.log("source", source)
//     messages = messages.filter(message => message.channel === source);
// }
let store = useStore();
let errorMessage = ref('');
let headerComponentName = ref('ChatHeader');
let isLoading = ref(false);
let page = ref(1);
let totalPages = ref(1);
let messagesBlock = ref<HTMLElement | null>(null);
let messages = reactive<Message[]>([]);
let isFirstLoad = ref(false);

// Сортировка сообщений от старых к новым для отображения
const sortedMessages = computed(() => {
  return [...messages].sort((a, b) =>
      new Date(a.send_time).getTime() - new Date(b.send_time).getTime()
  );
});

const scrollToBottom = () => {
  if (messagesBlock.value) {
    messagesBlock.value.scrollTop = messagesBlock.value.scrollHeight;
  }
};
const delayScrollToBottom = () => {
  setTimeout(() => {
    scrollToBottom();
  }, 100)
};

function isNotNearBottom(element: HTMLElement, threshold: number = 0.8, offset: number = 0): boolean {
  if (!element) return false;
  const scrollTop = element.scrollTop;
  const scrollHeight = element.scrollHeight;
  const clientHeight = element.clientHeight;
  const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
  const thresholdPixels = clientHeight * (1 - threshold) + offset; // Добавляем отступ
  return distanceFromBottom > thresholdPixels;
}

onBeforeMount(async () => {
  await nextTick(() => {
    scrollToBottom();
  });
})

async function fetchMessages() {
  isLoading.value = true;
  try {
    const response = await store.dispatch('messages/messageRepository/getMessagesByClient', {
      'page': page.value,
      'limit': 10,
      'client_id': store.state.clients.repository.client.id
    });

    if (response?.data) {
      if (page.value === 1) {
        messages.splice(0, messages.length, ...response.data);
        await nextTick(() => {
          delayScrollToBottom();
        });
      } else {
        const beforeHeight = messagesBlock.value?.scrollHeight || 0;
        messages.unshift(...response.data);
        await nextTick(() => {
          if (beforeHeight) {
            messagesBlock.value.scrollTop = messagesBlock.value.scrollHeight - beforeHeight;
          }
        });
      }
      totalPages.value = response.last_page;
    }
  } catch (error) {
    console.error('Ошибка загрузки сообщений:', error);
  } finally {
    isLoading.value = false;

    if (isFirstLoad.value) {
      await nextTick(scrollToBottom);
      isFirstLoad.value = false;
    }
  }
}

onMounted(async () => {
  console.log("route", route.fullPath.toLowerCase().includes("inbox"))
  try {
    store.commit('ui/spinner/setOpen');
    if (store.state.clients.repository?.client?.id == null) {
      errorMessage.value = "Відсутній коректний номер телефону або username";
      store.commit('ui/spinner/setClose');
      return;
    }
    if (!route.fullPath.toLowerCase().includes("inbox")) {
      let client = store.state.clients.crm.crmClient;
      console.log("client", client)
      await fetchMessages();
    } else if (route.fullPath.toLowerCase().includes("inbox")) {
      await fetchMessages();
    }
    if (store.state.clients.repository?.client?.id != null) {
      newMessagesEvents()
    }
  } catch (e) {
    store.commit('ui/spinner/setClose');
  } finally {
    store.commit('ui/spinner/setClose');
  }

});

function newMessagesEvents() {
    console.log("newMessagesEvents start")
    console.log("newMessagesEvents start2")
  window.Echo.channel(`laravel_database_client_${store.state.clients.repository.client.id}`)
      .listen('.new_message', (e) => {
        let message = e.message;
        let existingMessage = messages.find(msg => msg.message_id === message.message_id);
        console.log("socket message", message)
        if (!existingMessage) {
          messages.push(new Message(
              message.sender,
              message.text,
              message.channel,
              message.clientId,
              message.sendType,
              message.messageRead,
              message.fileUrl,
              message.error,
              message.messageId,
              message.status,
              message.sentAt,
              message.id
          ));
          nextTick(
              () => {
                if (!isNotNearBottom(messagesBlock.value, 0.8)) {
                  scrollToBottom()
                }
              })

        }
      })
      .subscribed(() => {
        console.log('✅ Успешно подписался на канал клиента:', store.state.clients.repository.client.id);
      })
      .error((error) => {
        console.log('❌ Ошибка подписки на канал:', error);
      });
}

onBeforeUnmount(async () => {
  window.Echo.leave(`client-messages-${store.state.clients.repository.client.id}`);

})

const onScroll = (event: Event) => {
  if (errorMessage.value != '') return;
  const target = event.target as HTMLElement;
  const top = target.scrollTop === 0;
  if (top && !isLoading.value && page.value < totalPages.value) {
    page.value += 1;
    fetchMessages();
  }
};

</script>

<style scoped>
.loading {
  text-align: center;
  padding: 10px;
  color: #666;
}

.chat-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  margin: 0;
}

.chat-header-block {
  flex: 0 0 11%;
  background-color: #f5f5f5;
}

.chat-messages-block {
  flex: 1 1 84%;
  overflow-y: scroll;
}

.chat-messages-block::-webkit-scrollbar {
  width: 3px;
}

.chat-messages-block::-webkit-scrollbar-track {
  background: transparent;
}

.chat-messages-block::-webkit-scrollbar-thumb {
  background-color: rgba(38, 65, 228, 0.3);
  border-radius: 10px;
  border: 1px solid transparent;
}

.chat-messages-block {
  scrollbar-width: thin;
  scrollbar-color: rgba(38, 65, 228, 0.3) transparent;
}

.chat-footer-block {
  flex: 0 0 5%;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
