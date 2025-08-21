import axios from "axios";

export const LoadingModule = {
    state: {
        isShow:false
    },
    getters: {},
    mutations: {
        setOpen(state) {
            state.isShow = true;
        },
        setClose(state) {

            state.isShow = false;
        },
    },
    actions: {
    },
    namespaced: true
}
