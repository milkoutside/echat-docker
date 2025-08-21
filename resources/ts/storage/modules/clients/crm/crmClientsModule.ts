import axios from "axios";
import {Client} from "@/models/chat/Client";

export const CrmClientsModule = {
    state: {
        crmClient:{},
        module:""
    },
    getters: {},
    mutations: {
        setCrmClient(state, client) {
            console.log(`set crm client - ${client}`,client)
            state.crmClient = client;
        },
        setModule(state, module) {
            state.module = module;
        }
    },
    actions: {
        async findClientByQuery({state},searchData:{ query:string, module:string}){
            const config = {
                select_query: `
                    select id, Full_Name, Phone, Telegram_nickname,WhatsApp
                    from ${searchData.module}
                    where (((Full_Name like '%${searchData.query}%') or (Phone like '%${searchData.query}%')) or (WhatsApp like '%${searchData.query}%' or Telegram_nickname like '%${searchData.query}%'))
                `
            };

            const searchClients= await ZOHO.CRM.API.coql(config);
            if(searchClients?.data){
                return searchClients.data;
            }
            return [];

        },
        async findClientByPhoneOrUsername({state},searchData:{ viber_phone:string|null,whatsapp_phone:string|null, username:string|null, module:string}){
            let config = {
                select_query: `
                    select id, Full_Name, Phone, Telegram_nickname,WhatsApp
                    from ${searchData.module}
                    where (Phone like '%${searchData.viber_phone}%'
                        or Telegram_nickname like '%${searchData.username}%') or (WhatsApp like '%${searchData.whatsapp_phone}%')
                `
            };

            const searchClients = await ZOHO.CRM.API.coql(config);
            if(searchClients?.data?.length > 0){
                return searchClients.data;
            }
            return [];

        }
    },
    namespaced: true
}
