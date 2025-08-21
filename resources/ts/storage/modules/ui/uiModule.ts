import {SpinnerModule} from "@/storage/modules/ui/spinner/spinnerModule";
import {LoadingModule} from "@/storage/modules/ui/loading/loadingModule";



export const UiModule = {
    namespaced: true,
    modules: {
        spinner:SpinnerModule,
        loading:LoadingModule
    }
};
