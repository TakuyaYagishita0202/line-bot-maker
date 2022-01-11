<template>
  <v-card class="mx-auto" max-width="344" outlined>
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline mb-1">{{ title }}</v-list-item-title>
        <v-list-item-subtitle>
          <span v-if="pattern">「{{ pattern }}」に</span>
          <span v-if="pattern_type">{{ pattern_type }}</span>
        </v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
    <v-card-text>{{ description }}</v-card-text>

    <v-card-actions>
      <v-btn icon @click="addNewNode">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
      <v-spacer></v-spacer>
      <v-btn icon @click="getNodeId">
        <v-icon>mdi-square-edit-outline</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
<script>
export default {
  props: ["remove", "add", "test", "node", "id", "title", "description", "pattern", "pattern_type", "deletable", "movable"],

  data() {
    return {
      node_id: this.id,
    };
  },

  methods: {
    getNodeId(){
      this.$store.commit('node/setSelectedNodeId', this.node_id)
    },
    addNewNode(){
      this.$store.commit('node/setNewNodeParentId', this.id)
    }
  }
};
</script>