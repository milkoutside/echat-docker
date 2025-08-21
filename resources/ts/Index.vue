<template>
  <v-app>
    <Loading v-if="store.state.ui.loading.isShow"></Loading>
    <router-view v-else></router-view>
    <Spinner></Spinner>
  </v-app>
</template>


<script setup lang="ts">
//<Loading v-if="store.state.ui.loading.isShow"></Loading>
import {onBeforeMount, onBeforeUnmount, onMounted} from 'vue';
import {PhoneHelpers} from "@/helpers/phones/PhoneHelpers.ts";
import Echo from "laravel-echo";
import socketio from 'socket.io-client';
import {useRoute} from "vue-router";
import {useStore} from "vuex";
import Loading from "@/components/loading/Loading.vue";
import {Client} from "@/models/chat/Client";
import Spinner from "@/components/spinner/Spinner.vue";
import {LastMessageWithClient} from "@/models/inbox/LastMessageWithClient.ts";
//<Loading v-if="store.state.ui.loading.isShow"></Loading>
//  <router-view v-else></router-view>
const route = useRoute();
const store = useStore();
console.log("route", route.fullPath)

const init = async () => {
      try {
        store.commit('ui/loading/setOpen');
        let senders = await store.dispatch('echat/getSenders');
        console.log('senders', senders)
        store.commit('echat/setSenders', senders);
          ZOHO.embeddedApp.on("PageLoad", async (data) => {
              console.log("init data", data);
              if (!route.fullPath.toLowerCase().includes("inbox")) {
                  try {
                      await ZOHO.CRM.UI.Resize({height: "100%", width: "100%"});
                      let module = data.Entity;
                      store.commit('clients/crm/setModule', module);
                      let recordId = data.EntityId[0];
                      let record = await ZOHO.CRM.API.getRecord({Entity: module, RecordID: recordId});

                      if (module === "Deals") {
                          if (record?.data[0] && record?.data[0]?.Contact_Name.id) {
                              record = await ZOHO.CRM.API.getRecord({
                                  Entity: "Contacts",
                                  RecordID: record?.data[0]?.Contact_Name.id
                              });
                          }
                      }

                      console.log("record", record);
                      if (record?.data[0]) {
                          store.commit('clients/crm/setCrmClient', record.data[0]);

                          // Подготовка данных из CRM
                          const crmData = record.data[0];
                          const phone = PhoneHelpers.validateAndFormatPhoneToGlobal(crmData?.Phone);
                          const telegramNicknameRaw = crmData?.Telegram_nickname ?? null;
                          const telegramNickname = telegramNicknameRaw?.replace(/^@/, '') ?? null;
                          const viberPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(crmData?.Phone ?? '');
                          const whatsappPhone = PhoneHelpers.validateAndFormatPhoneToGlobal(crmData?.WhatsApp ?? '');
                          // Ищем клиента по любому из доступных контактов
                          const phones = {};
                          if (phone !== 'invalid') phones['telegram'] = phone;
                          if (viberPhone !== 'invalid') phones['viber'] = viberPhone;
                          if (whatsappPhone !== 'invalid') phones['whatsapp'] = whatsappPhone;

// Ищем клиента по любому из доступных контактов
                          let findClient: Client | null = await store.dispatch(
                              'clients/repository/getClientByPhonesOrUsername',
                              {
                                  phones: Object.keys(phones).length > 0 ? phones : null,
                                  username: telegramNickname
                              }
                          );

                          store.commit('clients/repository/setClient', findClient);
                          console.log("findClient", findClient);

                          if (!findClient) {
                              console.log("Client not found, creating new one");

                              // Создаем нового клиента с доступными контактами
                              const phones = {};

                              if (phone !== 'invalid') phones['telegram'] = phone;
                              if (viberPhone !== 'invalid') phones['viber'] = viberPhone;
                              if (whatsappPhone !== 'invalid') phones['whatsapp'] = whatsappPhone;

                              if (Object.keys(phones).length > 0 || telegramNickname) {
                                  let createdClient: Client | null = await store.dispatch(
                                      'clients/repository/upsertClient',
                                      new Client(
                                          null,
                                          Object.keys(phones).length > 0 ? phones : null,
                                          telegramNickname
                                      )
                                  );

                                  if (createdClient?.id) {
                                      store.commit('clients/repository/setClient', createdClient);
                                  }
                              }
                          } else {
                              // Получаем последнее сообщение для клиента
                              let lastMessageWithClient: LastMessageWithClient | null = await store.dispatch(
                                  'inbox/searchClientWithLastMessage',
                                  {
                                      phone: findClient.phones?.telegram ?? findClient.phones?.viber ?? findClient.phones?.whatsapp ?? null,
                                      username: findClient.username ?? null
                                  }
                              );

                              if (lastMessageWithClient) {
                                  store.commit('clients/repository/setClientSource', lastMessageWithClient.lastChannel ?? 'telegram');
                                  store.commit('echat/setSelectedSender', lastMessageWithClient.lastSender ?? null);
                              }

                              // Подготовка данных для обновления
                              const currentPhones = findClient.phones || {};
                              const updatedPhones = {...currentPhones};
                              let needsUpdate = false;
                              // Добавляем недостающие телефоны
                              if (viberPhone !== 'invalid' && !currentPhones.viber) {
                                  updatedPhones.viber = viberPhone;
                                  needsUpdate = true;
                              }
                              if (whatsappPhone !== 'invalid' && !currentPhones.whatsapp) {
                                  updatedPhones.whatsapp = whatsappPhone;
                                  needsUpdate = true;
                              }

                              // Проверяем нужно ли обновить username
                              const shouldUpdateUsername = telegramNickname && findClient.username !== telegramNickname;

                              if (needsUpdate || shouldUpdateUsername) {
                                  console.log("Updating client data",updatedPhones,currentPhones);
                                  let updateClientData = new Client(
                                      findClient.id,
                                      needsUpdate ? updatedPhones : currentPhones,
                                      shouldUpdateUsername ? telegramNickname : findClient.username
                                  );
                                  let updateClient: Client | null = await store.dispatch(
                                      'clients/repository/updateClientById',
                                      updateClientData
                                  );

                                  if (updateClient?.id) {
                                      store.commit('clients/repository/setClient', updateClient);
                                  }
                              }
                          }
                      }
                      store.commit('ui/loading/setClose');
                  } catch (error) {
                      store.commit('ui/loading/setClose');
                      console.error("Initialization error:", error);
                  }
              }
              store.commit('ui/loading/setClose');
          });
        await ZOHO.embeddedApp.init();
      } catch
          (error) {
        store.commit('ui/loading/setClose');
        console.error("Ошибка при инициализации:", error);
      }
    }
;

onMounted(async () => {
  await init();
});
onMounted(() => {
  window.Echo = new Echo({
    client: socketio,
    broadcaster: 'socket.io',
    //host: window.location.hostname + ':6001'
    host: 'https://host.aguaqui.pt:6001',
    allowEIO3: true,
    cors: {
      origin: true,
      credentials: true
    },
    transports: ['websocket', 'polling', 'flashsocket']
  });
})
</script>

<style scoped>
* {
  overflow-y: hidden;
  overflow-x: hidden;
}
</style>

