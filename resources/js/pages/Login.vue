<template>
  <div class="login-wrapper">
    <v-snackbar v-model="snackbar" multi-line color="red" top>
      <div v-if="loginErrors">
        <ul v-if="loginErrors.email">
          <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
        </ul>
        <ul v-if="loginErrors.password">
          <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
        </ul>
      </div>

      <div v-if="registerErrors">
        <ul v-if="registerErrors.name">
          <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
        </ul>
        <ul v-if="registerErrors.email">
          <li v-for="msg in registerErrors.email" :key="msg">{{ msg }}</li>
        </ul>
        <ul v-if="registerErrors.password">
          <li v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
        </ul>
      </div>

      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar = false">
          閉じる
        </v-btn>
      </template>
    </v-snackbar>
    <v-card class="mx-auto card-login" color="basil" max-width="600" elevation="0">
      <v-card-title class="text-center justify-center py-6">
        <h1 class="font-weight-bold text-h2 basil--text">
          LINEBOT MAKERへ<br />ようこそ！
        </h1>
      </v-card-title>

      <v-tabs v-model="tab" background-color="transparent" color="basil" grow>
        <v-tab> ログイン </v-tab>
        <v-tab> 会員登録 </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab" class="pa-10">
        <v-tab-item>
          <v-form ref="loginForm">
            <v-text-field
              v-model="loginForm.email"
              label="メールアドレス"
              required
            ></v-text-field>

            <v-text-field
              v-model="loginForm.password"
              label="パスワード"
              :append-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
              :type="show_pass ? 'text' : 'password'"
              required
              counter
              @click:append="show_pass = !show_pass"
            ></v-text-field>

            <v-btn color="success" class="mr-4" @click="login">
              ログイン
            </v-btn>
          </v-form>
        </v-tab-item>
        <v-tab-item>
          <v-form ref="registerForm">
            <v-text-field
              v-model="registerForm.name"
              label="氏名または企業名"
              required
            ></v-text-field>

            <v-text-field
              v-model="registerForm.email"
              label="メールアドレス"
              required
            ></v-text-field>

            <v-text-field
              v-model="registerForm.password"
              label="パスワード"
              required
            ></v-text-field>

            <v-text-field
              v-model="registerForm.password_confirmation"
              label="パスワード（確認用）"
              required
            ></v-text-field>

            <v-btn color="success" class="mr-4" @click="register">
              登録する
            </v-btn>
          </v-form>
        </v-tab-item>
      </v-tabs-items>
    </v-card>
  </div>
</template>

<script>
export default {
  created() {
    this.clearError();
  },

  data: () => ({
    snackbar: false,
    tab: null,
    loginForm: {
      email: "",
      password: "",
    },
    registerForm: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    show_pass: false,
  }),

  methods: {
    async login() {
      await this.$store.dispatch("auth/login", this.loginForm);
      if (this.apiStatus) {
        this.$router.push("/");
      } else {
        this.snackbar = true;
      }
    },
    async register() {
      await this.$store.dispatch("auth/register", this.registerForm);
      if (this.apiStatus) {
        this.$router.push("/");
      } else {
        this.snackbar = true;
      }
    },
    clearError() {
      this.$store.commit("auth/setLoginErrorMessages", null);
      this.$store.commit("auth/setRegisterErrorMessages", null);
    },
  },

  computed: {
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
    loginErrors() {
      return this.$store.state.auth.loginErrorMessages;
    },
    registerErrors() {
      return this.$store.state.auth.registerErrorMessages;
    },
  },
};
</script>

<style scoped>
.login-wrapper {
	margin:0;
	padding:0;
	background-image:url(/img/illustration.svg);
	background-position:center bottom;
	background-size:contain;
	width:100%;
	height:100vh;
}
.card-login {
	background-color: rgba(255,255,255,0.9);
}
.v-item-group {
	background-color: rgba(255,255,255,0);
}
</style>