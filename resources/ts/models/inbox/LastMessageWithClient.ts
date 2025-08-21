import {Client} from "@/models/chat/Client.ts";

export class LastMessageWithClient {
    constructor(client: Client | null, lastMessage: string | null, lastChannel: string | null, lastSender: string | null,fileUrl: string | null, sendTime: string | null = null, unreadMessagesCount: number = 0) {
        this.client = client;
        this.lastMessage = lastMessage;
        this.lastChannel = lastChannel;
        this.lastSender = lastSender;
        this.fileUrl = fileUrl;
        this.sendTime = sendTime;
        this.unreadMessagesCount = unreadMessagesCount;
    }


    client: Client|null;
    lastMessage: string|null;
    lastChannel: string|null;
    lastSender: string|null;
    fileUrl: string|null;
    sendTime: string|null;
    unreadMessagesCount: number;
}
