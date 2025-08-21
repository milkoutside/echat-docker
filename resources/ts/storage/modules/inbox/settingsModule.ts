import axios from "axios";

export const SettingsModule = {
    state: {
        isOpen:false
    },
    getters: {},
    mutations: {
        setOpen(state) {

            state.isOpen = true;
        },
        setClose(state) {

            state.isOpen = false;
        }
    },
    actions: {

    },
    namespaced: true
}
