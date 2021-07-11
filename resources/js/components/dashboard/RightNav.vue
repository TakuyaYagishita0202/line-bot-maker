<template>
  <v-navigation-drawer
    absolute
    permanent
    right
    v-model="drawer"
    :mini-variant.sync="mini"
    app
  >
    <template v-slot:prepend>
      <v-list-item class="px-2">
        <v-list-item-title>イベント一覧</v-list-item-title>
        <v-btn icon @click.stop="mini = !mini">
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>
      </v-list-item>
    </template>

    <v-divider></v-divider>

    <v-list subheader two-line>
			<flowy-new-block
				v-for="(block, index) in blocks"
				:key="index"
				@drag-start="onDragStartNewBlock"
				@drag-stop="onDragStopNewBlock"
			>
				<template v-slot:preview="{}">
					<demo-block
						:title="block.preview.title"
						:subtitle="block.preview.subtitle"
					/>
				</template>

				<template v-slot:node="{}">
					<demo-node
						:title="block.node.title"
						:description="block.node.description"
						:custom-attribute="block.node.canBeAdded"
					/>
				</template>
			</flowy-new-block>
		</v-list>
  </v-navigation-drawer>
</template>

<script>
import DemoBlock from '../dashboard/DemoBlock'
import DemoNode from '../dashboard/DemoNode'

export default {
	name: 'RightNav',

	components: {
		DemoBlock,
		DemoNode,
	},

	data: () => ({
		drawer: true,
    mini: true,

		blocks: [
			{
				preview: {
					title: '応答する',
					subtitle: '入力値を検知して、特定の文言を返信します。',
				},
				node: {
					title: '応答する',
					description: '以下からイベントの発火条件を指定してください。',
				},
			},
			{
				preview: {
					title: 'Update database',
					subtitle: '入力値を検知して、特定の文言を返信します。',
					icon: 'error',
				},
				node: {
					title: 'Update database',
					description: '<span>Triggers when somebody performs a <b>specified action</b></span>',
				},
			},
			{
				preview: {
					title: 'Time has passed',
					subtitle: '入力値を検知して、特定の文言を返信します。',
				},
				node: {
					title: 'Time has passed',
					description: 'Triggers after a specified <b>amount</b> of time',
				},
			},
			{
				preview: {
					title: 'Cannot be added',
					subtitle: '入力値を検知して、特定の文言を返信します。',
				},
				node: {
					title: 'Time has passed',
					description: 'Triggers after a specified <b>amount</b> of time',
					canBeAdded: false,
				},
			},
		],
	}),

	methods: {
		onDragStartNewBlock (event) {
			console.log('onDragStartNewBlock', event);
			// contains all the props and attributes passed to demo-node
			const { props } = event
			this.newDraggingBlock = props;
		},
		onDragStopNewBlock (event) {
			console.log('onDragStopNewBlock', event);
			this.newDraggingBlock = null;
		},
	}
};
</script>

<style>
</style>