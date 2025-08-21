import {CrmClientsModule} from "@/storage/modules/clients/crm/crmClientsModule";
import {RepositoryClientsModule} from "@/storage/modules/clients/repository/repositoryClientsModule";

export const ClientsModule = {
    namespaced: true,
    modules: {
        crm:CrmClientsModule,
        repository:RepositoryClientsModule
    }
};
