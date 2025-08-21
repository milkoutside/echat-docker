import axios from "axios";
import {EchatSender} from "@/models/echat/EchatSender";
import {useStore} from "vuex";

export const EchatModule = {
    state: {
        senders: [],
        selectedSender: null,
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
        setSenders(state,senders:[]) {
            state.senders = senders;
        },
        setSelectedSender(state,sender) {
            state.selectedSender = sender;
        }
    },
    actions: {
        async getSenders() {
            let response = await axios.get('/api/echat/get-senders');
            return response?.data
                ? response.data.map((sender: any) => new EchatSender(sender.id ?? null, sender.sender, sender.service, sender.apiKey))
                : [];

        },
        async deleteSender({state},senderId) {
            console.log("senderId",senderId)
            return await axios.post('/api/echat/delete-sender', {id:senderId});

        },
        async createSender({state},echatSender:EchatSender) {
            return await axios.post('/api/echat/create-sender', echatSender);
        }
    },
    namespaced: true
}
