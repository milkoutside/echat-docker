<template>
    <div class="chat-header">
        <div class="row px-2 align-items-center">
            <div class="col-2">
                <i
                    class="fas fa-arrow-left fa-xl"
                    style="color: rgb(170 155 155);cursor: pointer;"
                    @click="changeHeaderEmit('ChatHeader')"
                ></i>
            </div>
            <div class="col-8 text-center">
                <div class="d-flex align-items-center justify-content-center gap-5">
                    <v-text-field
                        label="Номер телефона (380...)"
                        v-model="form.phone"
                        :rules="phoneRules"
                        required
                        outlined
                        placeholder="380xxxxxxxxx"
                        :error="!validPhone()"
                        persistent-hint
                        variant="outlined"
                        density="compact"
                        class="client-info-field"
                    ></v-text-field>
                    <v-text-field
                        label="Имя пользователя"
                        v-model="form.username"
                        :rules="usernameRules"
                        outlined
                        :error="!validUsername() && form.username.length > 0"
                        variant="outlined"
                        density="compact"
                        class="client-info-field"
                    ></v-text-field>
                    <i class="fa-solid fa-check fa-xl" style="color: #11df29;"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import {useStore} from 'vuex';

const store = useStore();
defineOptions({
    name: 'ChatClientInfoHeader',
});
const emit = defineEmits(['change-header','change-source']);
const changeHeaderEmit = (headerName) => {
    emit('change-header', headerName);
};

const form = reactive({
    phone: '',
    username: ''
});


const validPhone = () => /^380\d{9}$/.test(form.phone);

const validUsername = () => form.username.length >= 3;

const phoneRules = [
    v => !!v || 'Это поле обязательно',
    v => validPhone() || 'Телефон должен быть в формате: 380xxxxxxxxx (9 цифр после 380)'
];

const usernameRules = [
    v => !!v || 'Это поле обязательно',
    v => validUsername() || 'Имя пользователя должно содержать не менее 3 символов'
];


const valid = ref(false);


const saveForm = () => {
    if (validPhone() && validUsername()) {
        console.log('Форма отправлена:', {
            phone: form.phone,
            username: form.username
        });
        alert(`Форма успешно сохранена:
- Номер телефона: ${form.phone}
- Имя пользователя: ${form.username}`);
    } else {
        alert('Некорректные данные в форме. Пожалуйста, проверьте введенные значения.');
    }
};
</script>

<style scoped>
.client-info-field {
    position: relative;
    top: 10px;
}
</style>
