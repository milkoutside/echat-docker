import axios from "axios";
import { Client } from "@/models/chat/Client";

export const RepositoryClientsModule = {
    state: {
        clientSource: "telegram",
        client: null as Client | null
    },
    getters: {
        getClient: (state) => state.client,
        getClientSource: (state) => state.clientSource
    },
    mutations: {
        setClientSource(state, clientSource: string) {
            console.log(`set source - ${clientSource}`);
            state.clientSource = clientSource;
        },
        setClient(state, client: Client) {
            state.client = client;
        }
    },
    actions: {
        async getClientByPhonesOrUsername({ commit }, clientSearchData: { phones?: Record<string, string>, username?: string }) {
            const response = await axios.post('/api/clients/by-phones-or-username', {
                phones: clientSearchData.phones,
                username: clientSearchData.username,
            });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        },
        async getClientByPhoneOrUsername({ commit }, clientSearchData: { phones?: Record<string, string>, username?: string }) {
            const response = await axios.post('/api/clients/by-phone-or-username', {
                phones: clientSearchData.phones,
                username: clientSearchData.username,
            });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        },
        async mergeClientsByUsernameOrConfig({ commit }, clientSearchData: { phones?: Record<string, string>, username?: string }) {
            const response = await axios.post('/api/clients/merge-by-data', {
                phones: clientSearchData.phones,
                username: clientSearchData.username,
            });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        },

        async upsertClient({ commit }, clientData: Client) {
            const response = await axios.post('/api/clients/create-or-update', {
                phones: clientData.phones,
                username: clientData.username,
            });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        },

        async updateClientById({ commit }, clientData: Client) {
            const response = await axios.post('/api/clients/update-by-id', {
                phones: clientData.phones,
                username: clientData.username,
                id: clientData.id
            });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        },

        async getClientById({ commit }, id: number) {
            const response = await axios.post('/api/clients/by-id', { id });

            if (response.data?.id) {
                const client = new Client(
                    response.data.id,
                    response.data.phones ? JSON.parse(response.data.phones) : {},
                    response.data.username
                );
                commit('setClient', client);
                return client;
            }
            return null;
        }
    },
    namespaced: true
};
