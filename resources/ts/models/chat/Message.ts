export class Message {
    id: number | null;
    sender: string | null;
    text: string | null;
    channel: string | null;
    fileUrl: string | null;
    message_id: string | null;
    status: string | null;
    send_time: string | null;
    client_id: number | null;
    send_type: string | null;
    message_read: boolean | null;
    error: string | null;

    constructor(
        sender: string | null = null,
        text: string | null = null,
        channel: string | null = null,
        client_id: number | null = null,
        send_type: string | null = null,
        message_read: boolean | null = null,
        fileUrl: string | null = null,
        error: string | null = null,
        message_id: string | null = null,
        status: string | null = null,
        send_time: string | null = null,
        id: number | null = null
    ) {
        this.id = id;
        this.sender = sender;
        this.text = text;
        this.channel = channel;
        this.fileUrl = fileUrl;
        this.message_id = message_id;
        this.status = status;
        this.send_time = send_time;
        this.client_id = client_id;
        this.send_type = send_type;
        this.message_read = message_read;
        this.error = error;
    }
}
