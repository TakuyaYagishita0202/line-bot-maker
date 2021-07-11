<template>
  <flowy
    class="mx-auto"
    :nodes="nodes"
    :beforeMove="beforeMove"
    :beforeAdd="beforeAdd"
    @add="add"
    @move="move"
    @remove="remove"
    @drag-start="onDragStart"
  ></flowy>
</template>

<script>
export default {
	data: () => ({
		holder: [],
		dragging: false,
		newDraggingBlock: null,
		nodes: [
			{
				id: '1',
				parentId: -1,
				nodeComponent: 'demo-node',
				data: {
					text: 'Parent block',
					title: 'New Visitor',
					description: '<span>When a <b>new visitor</b> goes to <i>Site 1</i></span>',
				},
			},
			{
				id: '2',
				parentId: '1',
				nodeComponent: 'demo-node',
				data: {
					text: 'Parent block',
					title: 'New Visitor',
					description: '<span>When a <b>new visitor</b> goes to <i>Site 1</i></span>',
				},
			},
			{
				id: '3',
				parentId: '1',
				nodeComponent: 'demo-node',
				data: {
					text: 'Parent block',
					title: 'New Visitor',
					description: '<span>When a <b>new visitor</b> goes to <i>Site 1</i></span>',
				},
			},
		],
	}),

	methods: {
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
      const id = this.generateId();
      this.nodes.push({
        ..._event.node,
        id,
      });
    },
    remove (event) {
      console.log('remove', event)

      // node we're dragging to
      const { node } = event

      // we use lodash in this demo to remove node from the array
      const nodeIndex = _.findIndex(this.nodes, { id: node.id });
      this.nodes.splice(nodeIndex, 1);
    },
    move (event) {
      console.log('move', event);

      // node we're dragging to and node we've just dragged
      const { dragged, to } = event;

      // change panentId to id of node we're dragging to
      dragged.parentId = to.id;
    },
    add (event) {
      // every node needs an ID
      const id = this.generateId();

      // add to array of nodes
      this.nodes.push({
        id,
        ...event.node,
      });
    },
    onDragStart (event) {
      console.log('onDragStart', event);
      this.dragging = true;
    },
	}
};
</script>

<style>
</style>