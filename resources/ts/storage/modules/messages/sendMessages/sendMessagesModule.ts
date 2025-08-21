import {Message} from "../../../../models/chat/Message";
import axios from "axios";

export const SendMessagesModule = {
    state: {
        newMessage: new Message()
    },
    getters: {

    },
    mutations: {
        setNewMessage(state, newMessage:Message) {
            state.newMessage = newMessage;
        }
    },
    actions:{
        async sendMessage({commit}, messageData:Message) {
            let response = await axios.post('/api/echat/messages', messageData);
            commit('setNewMessage',new Message());
            console.log("resp",response)
        }
    },
    namespaced: true
}
