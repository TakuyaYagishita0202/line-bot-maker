const state = {
    selectedNodeId: null,
    newNodeParentId: null,
}
const getters = {}
const mutations = {
    setSelectedNodeId(state, node_id) {
		state.selectedNodeId = node_id
	},
	setNewNodeParentId(state, parent_id) {
		state.newNodeParentId = parent_id
	}
}
const actions = {}

export default {
	namespaced: true,
	state,
	getters,
	mutations,
	actions
}