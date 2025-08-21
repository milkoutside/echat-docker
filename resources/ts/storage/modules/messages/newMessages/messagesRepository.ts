import axios from "axios";
import {Message} from "../../../../models/chat/Message";

export const MessagesRepository = {
    state: () => ({}),
    getters: {},
    mutations: {},
    actions: {
        async getMessagesByClient({commit}, data): Promise<{ data: Message[]; last_page: number }> {
            try {
                // Отправляем POST-запрос с данными
                const response = await axios.post('/api/messages/get-messages', data);

                // Проверяем, есть ли данные в ответе
                const responseData = response.data?.data; // data — массив объектов сообщений

                if (responseData && responseData.length > 0) {
                    // Преобразуем данные в массив объектов Message
                    const messages = responseData.map((item: any) => {
                        return new Message(
                            item.sender,
                            item.text,
                            item.channel,
                            item.client_id,
                            item.send_type,
                            item.message_read,
                            item.fileUrl,
                            item.error,
                            item.message_id,
                            item.status,
                            item.send_time,
                            item.id
                        );
                    });

                    // Возвращаем объект с сообщениями и данными о пагинации
                    return {
                        data: messages,
                        last_page: response.data.last_page // Добавляем last_page из ответа
                    };
                }

                // Если массив пустой, возвращаем пустой массив и last_page = 1
                return {
                    data: [],
                    last_page: 1
                };
            } catch (error) {
                console.error('Ошибка при получении сообщений:', error);
                // Возвращаем пустой массив и last_page = 1 в случае ошибки
                return {
                    data: [],
                    last_page: 1
                };
            }
        },
        async readAllMessagesByClient({commit}, clientId): Promise<void> {
             await axios.post('/api/echat/read-all-messages-by-client', {id:clientId});
        }
    },
    namespaced: true
}
