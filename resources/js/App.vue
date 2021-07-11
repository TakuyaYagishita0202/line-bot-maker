<template>
  <v-app>
    <left-nav />
    <v-main>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-main>
    <right-nav />
  </v-app>
</template>

<script>
import LeftNav from './components/LeftNav.vue'
import RightNav from './components/dashboard/RightNav.vue'
export default {
  components: {
    LeftNav,
    RightNav,
  }
}
</script>

<style>

</style>