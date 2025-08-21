import Index from "@/Index.vue";
import Chat from "@/components/chat/Chat.vue";
import MessageList from "@/components/chat/messages/MessageList.vue";
import MessageItem from "@/components/chat/messages/MessageItem/MessageItem.vue";
import ChatHeader from "@/components/chat/headers/ChatHeader.vue";
import ChatFooter from "@/components/chat/footers/ChatFooter.vue";
import InboxHeader from "@/components/inbox/headers/InboxHeader.vue";
import Inbox from "@/components/inbox/Inbox.vue";
import ChatSearch from "@/components/inbox/filters/ChatSearch.vue";
import ChatList from "@/components/inbox/chats/ChatList.vue"
import ChatFilter from "@/components/inbox/filters/ChatFilter.vue";
import ChatSources from "@/components/chat/headers/sources/ChatSources.vue";
import ChatSourcesPickerHeader from "@/components/chat/headers/ChatSourcesPickerHeader.vue";
import SendFileModal from "@/components/chat/modals/files/SendFileModal.vue";
import MessageTypeText from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeText.vue";
import MessageTypeImage from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeImage.vue";
import MessageTypeVideo from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeVideo.vue";
import MessageTypeAudio from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeAudio.vue";
import ErrorBox from "@/components/chat/errorBox/ErrorBox.vue";
import Loading from "@/components/loading/Loading.vue";
import ChatClientInfoHeader from "@/components/chat/headers/ChatClientInfoHeader.vue";
import SettingsBlock from "@/components/inbox/footers/SettingsBlock.vue";
import Settings from "@/components/settings/Settings.vue";
import LocalSpinner from "@/components/spinner/LocalSpinner.vue";
import MessageTypeFile from "@/components/chat/messages/MessageItem/MessageTypes/MessageTypeFile.vue";

export default [
    Index,
    Chat,
    MessageList,
    MessageItem,
    ChatHeader,
    ChatFooter,
    InboxHeader,
    Inbox,
    ChatSearch,
    ChatList,
    ChatFilter,
    ChatFooter,
    ChatSources,
    ChatSourcesPickerHeader,
    SendFileModal,
    MessageTypeText,
    MessageTypeImage,
    MessageTypeVideo,
    MessageTypeAudio,
    MessageTypeFile,
    ErrorBox,
    Loading,
    ChatClientInfoHeader,
    SettingsBlock,
    Settings,
    LocalSpinner,
];
