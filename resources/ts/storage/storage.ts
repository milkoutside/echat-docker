import {createStore} from "vuex";
import {ModalsModule} from "./modules/modals/modalsModule";
import {MessagesModule} from "./modules/messages/messagesModule";
import {UiModule} from "./modules/ui/uiModule";
import {ClientsModule} from "@/storage/modules/clients/clientsModule";
import {EchatModule} from "@/storage/modules/echat/echatModule";
import {InboxModule} from "@/storage/modules/inbox/inboxModule.ts";
import {SettingsModule} from "@/storage/modules/inbox/settingsModule.ts";


export default createStore({
    modules: {
        clients:ClientsModule,
        messages:MessagesModule,
        modals:ModalsModule,
        ui:UiModule,
        echat:EchatModule,
        inbox:InboxModule,
        settings: SettingsModule
    }
})
