<template>
  <div>
    <v-speed-dial
      v-model="fab"
      top
      right
      fixed
      x-large
      direction="left"
    >
      <template v-slot:activator>
        <v-btn
          v-model="fab"
          color="blue darken-2"
          dark
          fab
        >
          <v-icon v-if="fab">
            mdi-close
          </v-icon>
          <v-icon v-else>
            mdi-swap-horizontal-bold
          </v-icon>
        </v-btn>
      </template>
      <v-btn
        fab
        dark
        small
        color="blue darken-2"
        @click.stop="scrollRight(600)"
      >
        <v-icon>mdi-arrow-right-bold</v-icon>
      </v-btn>
      <v-btn
        fab
        dark
        small
        color="blue darken-2"
        @click.stop="scrollRight(200)"
      >
        <v-icon>mdi-arrow-right</v-icon>
      </v-btn>
      <v-btn
        fab
        dark
        small
        color="blue darken-2"
        @click.stop="scrollLeft(200)"
      >
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>
      <v-btn
        fab
        dark
        small
        color="blue darken-2"
        @click.stop="scrollLeft(600)"
      >
        <v-icon>mdi-arrow-left-bold</v-icon>
      </v-btn>
    </v-speed-dial>
    <flowy
      class="mx-auto flowy-area"
      :nodes="reply_patterns"
      :beforeMove="beforeMove"
      :beforeAdd="beforeAdd"
      @add="add"
      @move="move"
      @remove="remove"
      @drag-start="onDragStart"
    ></flowy>
    <right-nav ref="rightNav" v-bind:pattern_types="pattern_types" />
  </div>
</template>

<script>
import { OK } from '../util'
import RightNav from '../components/dashboard/RightNav.vue'
export default {
  components: {
    RightNav,
  },

	data: () => ({
    reply_patterns: [
      {
				id: '0',
				parentId: -1,
				nodeComponent: 'demo-node',
				data: {
					text: 'Parent block',
					title: '初期状態',
					description: 'LINEのお友達とチャットが進行していない状態です。ここに応答パターンを追加してください。',
          deletable: false,
          movable: false,
          reply_pattern_messages: [],
				},
			},
    ],
    pattern_types: [],
		holder: [],
		dragging: false,
		newDraggingBlock: null,
    fab: false,
	}),

	methods: {
    scrollLeft(val) {
      let content = document.querySelector(".flowy-area");
      content.scrollLeft -= val;
    },
    scrollRight(val) {
      let content = document.querySelector(".flowy-area");
      content.scrollLeft += val;
    },
    async fetchReplyPatterns () {
      const response = await axios.get('/api/reply_patterns')

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      Object.keys(response.data.pattern_types).forEach((key) => {
        this.pattern_types.push({
          val: key,
          text: response.data.pattern_types[key]
        })
      });

      console.log(response.data.reply_patterns)
      
      response.data.reply_patterns.forEach(el => {
        this.reply_patterns.push({
          id: String(el.id),
          parentId: String(el.parent_id),
          nodeComponent: 'demo-node',
          data: {
            id: el.id,
            title: el.name,
            description: el.description,
            pattern: el.pattern,
            state_reply_pattern_id: el.state_reply_pattern_id,
            pattern_type: response.data.pattern_types[String(el.pattern_type_id)],
            parent_id: el.parent_id,
            deletable: true,
            movable: true,
            reply_pattern_messages: el.reply_pattern_messages,
            children: el.reply_patterns,
          }
        })
      });
    },

		// REQUIRED
    beforeMove ({ to, from }) {
      // called before moving node (during drag and after drag)
      // indicator will turn red when we return false
      // from is null when we're not dragging from the current node tree
      console.log('beforeMove', to, from);

      // we cannot drag upper parent nodes in this demo
      if (from && from.parentId === -1) {
        return false;
      }
      // we're adding a new node (not moving an existing one)
      if (from === null) {
        // we've passed this attribute to the demo-node
        if (this.newDraggingBlock['custom-attribute'] === false) {
          return false
        }
      }

      return true;
    },
    // REQUIRED
    beforeAdd ({ to, from }) {
      // called before moving node (during drag and after drag)
      // indicator will turn red when we return false
      // from is null when we're not dragging from the current node tree
      console.log('beforeAdd', to, from);

      // we've passed this attribute to the demo-node
      if (this.newDraggingBlock['custom-attribute'] === false) {
        return false
      }

      return true;
    },
    randomInteger () {
      return Math.floor(Math.random() * 10000) + 1;
    },
    generateId (nodes) {
      let id = this.randomInteger();
      // _.find is a lodash function
      while (_.find(nodes, { id }) !== undefined) {
        id = this.randomInteger();
      }
      return id;
    },
    addNode (_event) {
      console.log('add', _event);

      const id = this.generateId();
      this.reply_patterns.push({
        ..._event.node,
        id,
      });
    },
    remove (event) {
      console.log('remove', event)

      // node we're dragging to
      const { node } = event

      // we use lodash in this demo to remove node from the array
      const nodeIndex = _.findIndex(this.reply_patterns, { id: node.id });
      this.reply_patterns.splice(nodeIndex, 1);
    },
    move (event) {
      console.log('move', event);

      // node we're dragging to and node we've just dragged
      const { dragged, to } = event;

      // change panentId to id of node we're dragging to
      dragged.parentId = to.id;
    },
    add (event) {
      console.log('add', event);

      // every node needs an ID
      const id = this.generateId();

      // add to array of nodes
      this.reply_patterns.push({
        id,
        ...event.node,
      });
    },
    onDragStart (event) {
      console.log('onDragStart', event);
      this.dragging = true;
    },
	},

  computed: {
    selectedNodeId(){
      return this.$store.state.node.selectedNodeId
    },
    newNodeParentId(){
      return this.$store.state.node.newNodeParentId
    },
  },

  watch: {
    $route: {
      async handler () {
        await this.fetchReplyPatterns()
      },
      immediate: true
    },
    selectedNodeId(id){
      // Vuexの値を監視して、変更があればナビゲーションを表示（reply_patterを引数にして子に渡す）
      if(id !== null){
        if(typeof id === 'undefined'){
          this.$refs.rightNav.showNav(this.reply_patterns.find(el => el.id == '0'))
        } else {
          this.$refs.rightNav.showNav(this.reply_patterns.find(el => el.id == id))
        }
      }
    },
    newNodeParentId(parent_id){
      if(typeof parent_id !== 'undefined'){
        const id = this.generateId(); // 任意のIDを付与する
        this.reply_patterns.push({
          id: String(id),
          parentId: String(parent_id),
          nodeComponent: 'demo-node',
          data: {
            id: id,
            title: '新たに追加されました',
            description: '説明文を追加してください',
            pattern: null,
            pattern_type: '全文一致',
            deletable: true,
            movable: true,
            reply_pattern_messages: [],
            children: [],
          }
        })

        this.$refs.rightNav.showNav(this.reply_patterns.find(el => el.id == id))
      }
    }
  }
};
</script>

<style scoped>
.flowy-area {
  scroll-behavior: smooth;
}
</style>