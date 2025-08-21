
export class EchatSender {
    constructor(id: number | null, sender: string | null, service: string | null,apiKey: string | null) {
        this.id = id;
        this.sender = sender;
        this.service = service;
        this.apiKey = apiKey;
    }


    id: number|null;
    sender: string|null;
    service: string|null;
    apiKey: string|null;
}
