import axios from "axios";
import {Message} from "@/models/chat/Message";
import {EchatSender} from "@/models/echat/EchatSender";
import {useStore} from "vuex";
import {LastMessageWithClient} from "@/models/inbox/LastMessageWithClient.ts";
import {Client} from "@/models/chat/Client.ts";
import {LastMessageWithClientDTO} from "@/models/inbox/dto/LastMessageWithClientDTO.ts";


export const InboxModule = {
    state: {
        openChat:false,
        selectedChat:{},
        openChatId:null
    },
    getters: {
        getSendersByService(state){
            const store = useStore();
            let service = store.state.clients.repository.clientSource;
            console.log("client source", service)
            if(service === 'all') return state.senders;
            return state.senders.filter(sender => sender.service === service);
        },

        // Фильтрация senders по phone
        getSendersByPhone(state,phone){
            return state.senders.filter(sender => sender.sender === phone);
        }
    },
    mutations: {
        setOpenChatId(state,id){
          state.openChatId = id;
        },
        setSelectedChat(state,chat) {
            state.selectedChat  = chat;
        },
        setOpenChat(state){
            state.openChat = true;
        },
        setCloseChat(state){
            state.openChat = false;
        },
        setSenders(state,senders:[]) {
            state.senders = senders;
        },
        setSelectedSender(state,sender) {
            state.selectedSender = sender;
        }
    },
    actions: {
        async getLastMessageWithClients({commit}, lassMessageWithClientDTO: LastMessageWithClientDTO): Promise<LastMessageWithClient[]|[]> {
            let response = await axios.post('/api/inbox/get-last-message-with-clients', lassMessageWithClientDTO);
            console.log("pzda",response?.data)
            return response?.data?.data
                ? response?.data?.data.map((lastMessagesWithClient: any) =>
                    new LastMessageWithClient(
                        new Client(
                            lastMessagesWithClient.id,
                            lastMessagesWithClient.phones,
                            lastMessagesWithClient.username,
                        ),
                        lastMessagesWithClient.last_message_text,
                        lastMessagesWithClient.last_message_channel,
                        lastMessagesWithClient.last_sender,
                        lastMessagesWithClient.last_message_fileUrl,
                        lastMessagesWithClient.last_message_time,
                        lastMessagesWithClient.unread_messages_count
                    )
                )
                : [];

        },
        async searchClientWithLastMessage({commit}, data): Promise<LastMessageWithClient> {
            let response = await axios.post('/api/inbox/find-client-last-message-with-clients', data);
            console.log("pzda",response?.data)
            return response?.data
                ?
                    new LastMessageWithClient(
                        new Client(
                            response.data.id,
                            response.data.phones,
                            response.data.username,
                        ),
                        response?.data.last_message_text,
                        response?.data.last_message_channel,
                        response?.data.last_sender,
                        response?.data.last_message_fileUrl,
                        response?.data.last_message_time,
                        response?.data.unread_messages_count

                )
                : null;

        }
    },
    namespaced: true
}
