import {id} from "vuetify/locale";

export class Client {
    constructor(
        id: number | null,
        phones: Record<string, string> | null,
        username: string | null
    ) {
        this.id = id ?? null;
        this.phones = phones ?? {};
        this.username = username ?? null;
    }

    id: number | null;
    username: string | null;
    phones: Record<string, string>; // { viber: "+380...", telegram: "+380..." }

    getPhoneByChannel(channel: string): string | null {
        return this.phones[channel] ?? null;
    }

    setPhone(channel: string, phone: string): void {
        this.phones[channel] = phone;
    }
}
