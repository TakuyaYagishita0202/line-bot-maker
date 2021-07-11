<template>
  <v-list-item>
    <v-list-item-avatar>
      <v-icon class="grey lighten-1" dark> mdi-folder </v-icon>
    </v-list-item-avatar>

    <v-list-item-content>
      <v-list-item-title v-text="title"></v-list-item-title>

      <v-list-item-subtitle v-text="subtitle"></v-list-item-subtitle>
    </v-list-item-content>

    <v-list-item-action>
      <flowy-drag-handle>
        <v-btn icon>
          <v-icon color="grey lighten-1">mdi-drag-horizontal-variant</v-icon>
        </v-btn>
      </flowy-drag-handle>
    </v-list-item-action>
  </v-list-item>
</template>
<script>
export default {
  props: ["remove", "node", "title", "subtitle"],

  data() {
    return {
      text: "This is component A",
    };
  },
};
</script>