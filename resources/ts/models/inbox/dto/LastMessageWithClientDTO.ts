import {id} from "vuetify/locale";
import {Client} from "@/models/chat/Client.ts";

export class LastMessageWithClientDTO {
    page: number;
    perPage: number;
    channel: string;

    constructor(page: number = 1, perPage: number = 1, channel: string = 'all') {
        this.page = page;
        this.perPage = perPage;
        this.channel = channel;
    }
}

