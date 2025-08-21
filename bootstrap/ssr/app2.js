import { resolveComponent, withCtx, createVNode, useSSRContext, defineComponent, ref, mergeProps, createApp } from "vue";
import { ssrRenderComponent, ssrRenderAttrs, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$4 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_v_app = resolveComponent("v-app");
  const _component_Chat = resolveComponent("Chat");
  _push(ssrRenderComponent(_component_v_app, _attrs, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Chat, null, null, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Chat)
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/ts/Index.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender]]);
class Message {
  constructor(text, time, isUser) {
    this.text = text;
    this.time = time;
    this.isUser = isUser;
  }
}
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  ...{
    name: "Chat"
  },
  __name: "Chat",
  __ssrInlineRender: true,
  setup(__props) {
    const messages = ref([
      new Message("asddstest tesast test", "23", true)
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_MessageList = resolveComponent("MessageList");
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "chat-container" }, _attrs))} data-v-9af1794b><div class="chat-header" data-v-9af1794b> Header Content </div><div class="chat-messages" data-v-9af1794b>`);
      _push(ssrRenderComponent(_component_MessageList, { messages: messages.value }, null, _parent));
      _push(`</div><div class="chat-footer" data-v-9af1794b></div></div>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/ts/components/chat/Chat.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const Chat = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-9af1794b"]]);
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  ...{
    name: "MessageList"
  },
  __name: "MessageList",
  __ssrInlineRender: true,
  props: {
    messages: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_MessageItem = resolveComponent("MessageItem");
      _push(`<div${ssrRenderAttrs(_attrs)} data-v-59cc98dc><div class="chat-window px-3 py-3" data-v-59cc98dc><!--[-->`);
      ssrRenderList(_ctx.messages, (message, index) => {
        _push(`<div data-v-59cc98dc>`);
        _push(ssrRenderComponent(_component_MessageItem, { message }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/ts/components/chat/messages/MessageList.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const MessageList = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-59cc98dc"]]);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  ...{
    name: "MessageItem"
  },
  __name: "MessageItem",
  __ssrInlineRender: true,
  props: {
    message: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: ["message", _ctx.message.isUser ? "user-message" : "my-message"]
      }, _attrs))} data-v-4ca2a74d><div class="message-content" data-v-4ca2a74d><span data-v-4ca2a74d>${ssrInterpolate(_ctx.message.text)}</span><span class="message-time" data-v-4ca2a74d>${ssrInterpolate(_ctx.message.time)}</span></div></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/ts/components/chat/messages/MessageItem/MessageItem.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const MessageItem = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-4ca2a74d"]]);
const _sfc_main = /* @__PURE__ */ Object.assign({
  name: "ChatHeader"
}, {
  __name: "ChatHeader",
  __ssrInlineRender: true,
  setup(__props) {
    const client = {
      name: "Имя клиента"
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "chat-header" }, _attrs))} data-v-1da4b2f3><h1 data-v-1da4b2f3>Чат с ${ssrInterpolate(client.name)}</h1></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/ts/components/chat/headers/ChatHeader.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ChatHeader = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-1da4b2f3"]]);
const importer = [
  Index,
  Chat,
  MessageList,
  MessageItem,
  ChatHeader
];
const app = createApp(Index);
importer.forEach((component) => {
  app.component(component.name, component);
});
const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: "light"
  }
});
app.use(vuetify);
app.mount("#app");
