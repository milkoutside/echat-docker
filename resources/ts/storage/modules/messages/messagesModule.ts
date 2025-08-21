import {SendMessagesModule} from "./sendMessages/sendMessagesModule";
import {MessagesRepository} from "./newMessages/messagesRepository";


export const MessagesModule = {
    namespaced: true,
    modules: {
        sendMessages:SendMessagesModule,
        messageRepository:MessagesRepository
    }
};
