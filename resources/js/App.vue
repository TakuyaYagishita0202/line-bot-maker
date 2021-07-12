<template>
  <v-app>
    <left-nav v-if="isLogin" />
    <v-main>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-main>
    <right-nav v-if="isLogin" />
  </v-app>
</template>

<script>
import LeftNav from './components/LeftNav.vue'
import RightNav from './components/dashboard/RightNav.vue'
import { INTERNAL_SERVER_ERROR } from './util'

export default {
  components: {
    LeftNav,
    RightNav,
  },

  computed: {
    isLogin () {
      return this.$store.getters['auth/check']
    },
    errorCode () {
      return this.$store.state.error.code
    }
  },

  watch: {
    errorCode: {
      handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        }
      },
      immediate: true
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>

<style>

</style>