<template>
  <div class="container">
    <v-card class="pa-4" outlined>
      <v-form v-model="valid">
        <!-- Номер телефона -->
        <v-text-field
          label="Номер телефона (380...)"
          v-model="form.phone"
          :rules="phoneRules"
          required
          outlined
          prepend-icon="mdi-phone"
          placeholder="380xxxxxxxxx"
          :error="!validPhone()"
          persistent-hint
        ></v-text-field>

        <!-- Username -->
        <v-text-field
          label="Имя пользователя"
          v-model="form.username"
          :rules="usernameRules"
          required
          outlined
          prepend-icon="mdi-account"
          :error="!validUsername() && form.username.length > 0"
        ></v-text-field>

        <!-- Кнопка сохранить -->
        <v-btn
          :disabled="!valid"
          color="success"
          block
          @click="saveForm"
        >
          <v-icon left>mdi-content-save</v-icon>
          Сохранить
        </v-btn>
      </v-form>
    </v-card>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';


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

// === Общее состояние валидности формы ===
const valid = ref(false);

// === Метод для обработки сохранения ===
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
.container {
  max-width: 400px;
  margin: 20px auto;
}
</style>
