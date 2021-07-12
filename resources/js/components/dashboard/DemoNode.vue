<template>
  <v-card class="mx-auto" max-width="344" outlined>
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline mb-1">{{ title }}</v-list-item-title>
        <v-list-item-subtitle>{{ description }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>

    <v-card-actions>
      <v-btn v-if="deletable" text @click="remove()">削除</v-btn>
      <v-spacer></v-spacer>
      <flowy-drag-handle v-if="movable">
        <v-btn icon>
          <v-icon>mdi-drag-horizontal-variant</v-icon>
        </v-btn>
      </flowy-drag-handle>
    </v-card-actions>
  </v-card>
</template>
<script>
export default {
  props: ["remove", "node", "title", "description", "deletable", "movable"],

  data() {
    return {
      text: "This is component A",
    };
  },
};
</script>