<template>
  <div @click="hideEmojiBlock" class="chat-footer-block px-3">
    <div class="chat-footer-input">
      <input @keyup.enter="sendMessage()"  v-model="newMessageText" type="text" placeholder="Aa">
      <div @click.stop="toggleEmojiBlock" class="chat-footer-input-emoji">
        <i class="fa-solid fa-face-smile"></i>
      </div>
      <EmojiPicker @select="addEmoji" :native="true" @click.stop v-if="showEmoji"/>
    </div>
    <div class="chat-footer-send-block">
      <i @click="store.commit('modals/sendFileModal/setOpen')"
         class="chat-footer-send-block-file fa-solid fa-paperclip"></i>
      <div v-if="validSendButton">
        <i @click="sendMessage()"
           class="chat-footer-send-block-message fa-regular fa-paper-plane"></i>
      </div>
      <div v-else>
        <i :title="sendButtonTooltip" class="chat-footer-send-block-message-disabled fa-regular fa-paper-plane"></i>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, reactive, ref} from 'vue';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

defineOptions({
  name: 'ChatFooter',
});
import {useStore} from 'vuex';
import {PhoneHelpers} from "../../../helpers/phones/PhoneHelpers.ts";

const store = useStore();
let newMessageText = ref('');
let showEmoji = ref(false);

function addEmoji(emoji) {
  console.log(emoji);
  newMessageText.value += emoji.i;
}

function toggleEmojiBlock() {
  showEmoji.value = !showEmoji.value;
}

function hideEmojiBlock() {
  showEmoji.value = false;
}

const validSendButton = computed(() => {
    const hasSender = !!store.state.echat.selectedSender;
    const hasValidChannel = store.state.clients.repository.clientSource !== 'all';
    const hasMessageText = !!newMessageText.value.trim();

    if (!hasSender || !hasValidChannel || !hasMessageText) {
        return false;
    }

    const client = store.state.clients.repository.client;
    const source = store.state.clients.repository.clientSource;

    // Для Telegram проверяем только username
    if (source === 'telegram') {
        return !!client?.username;
    }

    // Для Viber/WhatsApp проверяем наличие телефона
    if (['viber', 'whatsapp'].includes(source)) {
        const phone = client?.phones?.[source] || client?.phone;
        return !!phone;
    }

    return true;
});

let sendButtonTooltip = computed(() => {
    const client = store.state.clients.repository.client;
    const source = store.state.clients.repository.clientSource;

    if (!store.state.echat.selectedSender) {
        return 'Будь ласка, Оберіть відправника!';
    }
    if (source === 'all') {
        return 'Будь ласка, оберіть конкретний канал відправки!';
    }

    // Обработка Telegram
    if (source === 'telegram') {
        if (!client?.username) {
            return 'Для відправки в Telegram потрібен username!';
        }
        return '';
    }

    // Обработка Viber/WhatsApp
    const phone = client?.phones?.[source] || client?.phone;
    if (['viber', 'whatsapp'].includes(source)) {
        if (!phone) {
            return `Для відправки на ${source} потрібен номер телефону!`;
        }
        if (PhoneHelpers.validateAndFormatPhoneToGlobal(phone) === 'invalid') {
            return 'Пустий або невірний формат номеру телефону!';
        }
    }

    if (!newMessageText.value.trim()) {
        return 'Повідомлення не може бути пустим!';
    }

    return '';
});


async function sendMessage() {
  let message = reactive(store.state.messages.sendMessages.newMessage);

  message.client_id = store.state.clients.repository.client.id;
  message.channel = store.state.clients.repository.clientSource;
  message.send_type = 'outgoing';
  message.text = newMessageText.value;
  message.sender = store.state.echat.selectedSender;
  message.message_read = true;
  await store.dispatch('messages/sendMessages/sendMessage', message);
  newMessageText.value = '';
}
</script>


<style scoped>

.chat-footer-block {
  position: relative;
  width: 100%;
  height: auto;
  padding: 5px 0;
  box-shadow: 0 -1px 2px rgba(230, 226, 226, 0.8);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}


.chat-footer-send-block {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}


.chat-footer-send-block-file {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  cursor: pointer;
  color: #0d47a1;
  background: #f5f5f5;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.chat-footer-send-block-file:hover {
  transform: scale(1.1);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}


.chat-footer-send-block-message {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  padding: 7px;
  cursor: pointer;
  color: #0d47a1;
  background: #e3f2fd;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.chat-footer-send-block-message:hover {
  transform: scale(1.1);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}


.chat-footer-input {
  position: relative;
  flex-grow: 1;
  display: flex;
  align-items: center;
  height: 40px;
  border-radius: 20px;
  background: #e2cbcb1a;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}


.chat-footer-input input {
  width: 100%;
  height: 100%;
  border: none;
  outline: none;
  border-radius: 20px;
  padding: 7px 50px 7px 15px;
  box-sizing: border-box;
  font-size: 14px;
  background: transparent;
  font-family: inherit;
  color: #1c1b1b;
}

/* Иконка добавления смайлов */
.chat-footer-input-emoji {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  position: absolute;
  right: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  cursor: pointer;
  color: #0d47a1;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.chat-footer-input-emoji:hover {
  transform: scale(1.1);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
}


.v3-emoji-picker {
  position: fixed;
  left: auto;
  right: 10px;
  bottom: 8%;
  margin-bottom: 5px;
  z-index: 10;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  border-radius: 12px;
  overflow: hidden;
}


@media (max-width: 768px) {
  .chat-footer-input {
    flex-grow: 1;
    height: auto;
  }

  .chat-footer-send-block {
    gap: 8px;
  }

  .chat-footer-send-block-file,
  .chat-footer-send-block-message,
  .chat-footer-send-block-message-disabled {
    width: 24px;
    height: 24px;
  }

  .chat-footer-input input {
    font-size: 12px;
  }
}

.chat-footer-send-block-message-disabled {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  padding: 7px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}
</style>
