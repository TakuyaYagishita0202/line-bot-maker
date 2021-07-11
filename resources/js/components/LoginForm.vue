<template>
  <v-form ref="form" v-model="valid">
    <v-text-field
      v-model="user.email"
      label="メールアドレス"
      required
    ></v-text-field>

    <v-btn
      color="success"
      class="mr-4"
      @click="console.log('送信をクリック')"
    >
      送信
    </v-btn>
  </v-form>
</template>

<script>
export default {
  computed: {
    user: {
      get() {
        return this.$store.state.currentUser.user;
      },
    },
  },
};
</script>

<style>
</style>