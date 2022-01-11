<template>
  <v-navigation-drawer
    permanent
    right
    v-model="drawer"
    width="500"
    mini-variant-width="0"
    :mini-variant.sync="mini"
    app
  >
    <template v-slot:prepend>
      <v-list-item class="px-2 text-center">
        <v-list-item-title>応答パターンを編集する</v-list-item-title>
        <v-btn icon @click.stop="closeRightNav">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-list-item>
    </template>

    <v-divider></v-divider>

    <v-form ref="form" v-model="valid" lazy-validation>
      <v-card elevation="0" class="pa-6">
        <v-text-field
          label="パターン名"
          v-model="reply_pattern.data.title"
          required
        ></v-text-field>
        <v-textarea
          label="メモ"
          v-model="reply_pattern.data.description"
          required
        ></v-textarea>
        <v-select
          :items="pattern_types"
          v-model="pattern_type"
          label="検知パターン"
          @change="getPatternTypeText"
        ></v-select>
        <v-text-field
          v-if="!hideMessageInput"
          label="検知するメッセージ"
          :rules="rulesReplyPattern"
          v-model="reply_pattern.data.pattern"
          required
        ></v-text-field>
        <v-checkbox
          v-model="stateless"
          :rules="rulesStateless"
          label="直前（親）のやり取りを繰り返す"
          @change="toggleRepeatParent"
        ></v-checkbox>
      </v-card>

      <v-card elevation="0" class="px-6 pb-6">
        <v-tabs v-model="tab">
          <v-tab>返信メッセージ</v-tab>
          <v-tab v-if="reply_pattern.data.children.length > 0"
            >子パターン</v-tab
          >
        </v-tabs>
        <v-tabs-items v-model="tab">
          <v-tab-item class="mt-2">
            <v-card-subtitle
              >※返信メッセージは5つまで追加できます</v-card-subtitle
            >
            <v-card
              elevation="0"
              v-for="(reply_pattern_message, index) in reply_pattern.data
                .reply_pattern_messages"
              :key="reply_pattern_message.rank"
            >
              <v-row>
                <v-col v-if="message_sortable_mode" cols="2">
                  <v-btn @click="upMessage(index)" v-if="index !== 0" icon>
                    <v-icon>mdi-chevron-up</v-icon>
                  </v-btn>
                  <v-btn
                    @click="downMessage(index)"
                    v-if="
                      index !==
                      reply_pattern.data.reply_pattern_messages.length - 1
                    "
                    icon
                  >
                    <v-icon>mdi-chevron-down</v-icon>
                  </v-btn>
                </v-col>
                <v-col v-else>
                  <v-btn icon>
                    <v-icon @click="removeReplyPatternMessage(index)"
                      >mdi-close</v-icon
                    >
                  </v-btn>
                </v-col>
                <v-col cols="10">
                  <v-checkbox
                    v-model="reply_pattern_message.repeat_flg"
                    label="LINEユーザーからのメッセージを反復する"
                  ></v-checkbox>
                  <v-textarea
                    v-if="
                      reply_pattern_message.message_id !== null &&
                      (reply_pattern_message.repeat_flg != 1 ||
                        !reply_pattern_message.repeat_flg)
                    "
                    label="送信メッセージ"
                    v-model="reply_pattern_message.message.text"
                    :rules="rulesReplyPatternMessage"
                  ></v-textarea>
                  <v-text-field
                    v-if="
                      reply_pattern_message.repeat_flg == 1 ||
                      reply_pattern_message.repeat_flg
                    "
                    readonly
                    label="送信メッセージ"
                    value="（直前の受信メッセージを反復）"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-card>
            <v-btn
              class="my-2"
              block
              depressed
              @click="toggleMessageSortableMode"
              v-if="this.reply_pattern.data.reply_pattern_messages.length > 1"
              ><span v-if="!message_sortable_mode">並べ替える</span
              ><span v-else>並べ替えを終了</span></v-btn
            >
            <v-btn
              class="my-2"
              v-if="reply_pattern.data.reply_pattern_messages.length < 5"
              block
              depressed
              @click="addReplyPatternMessage"
              >メッセージを追加</v-btn
            >
            <v-btn class="mt-6 mb-2" color="success" block @click="save"
              >保存する</v-btn
            >
            <v-btn class="my-2" color="error" block @click="deletePattern"
              >削除する</v-btn
            >
          </v-tab-item>
          <v-tab-item
            class="mt-2"
            v-if="reply_pattern.data.children.length > 0"
          >
            <v-card-text>
              ※メッセージを送信した後、LINEユーザーからの返信が以下のパターンにマッチするかを探索して、次の返信内容を決定します。<br />
              ※パターンが複数存在する場合、以下<strong>上から順に</strong>探索する仕様となっておりますので、順序に誤りがないようにご注意ください。
            </v-card-text>
            <v-btn
              class="mb-2"
              block
              depressed
              @click="toggleChildrenSortableMode"
              v-if="reply_pattern.data.children.length > 1"
            >
              <span v-if="!children_sortable_mode">並べ替える</span
              ><span v-else>並べ替えを終了</span>
            </v-btn>
            <v-timeline dense v-if="!children_sortable_mode">
              <v-timeline-item
                v-for="child_reply_pattern in reply_pattern.data.children"
                :key="child_reply_pattern.id"
              >
                <template v-slot:icon>
                  <span class="white--text">{{
                    child_reply_pattern.rank
                  }}</span>
                </template>
                <div class="text-h6">
                  {{ child_reply_pattern.name }}
                </div>
                <p class="text-caption">
                  <span v-if="child_reply_pattern.pattern != ''"
                    >「{{ child_reply_pattern.pattern }}」に</span
                  ><span>{{ child_reply_pattern.pattern_type.name }}</span>
                </p>
              </v-timeline-item>
            </v-timeline>
            <div v-if="children_sortable_mode" class="pt-4">
              <div
                v-for="(child_reply_pattern, index) in reply_pattern.data
                  .children"
                :key="child_reply_pattern.id"
              >
                <v-row class="px-1 pt-1">
                  <v-col cols="2" class="text-center">
                    <v-btn @click="upChild(index)" v-if="index !== 0" icon>
                      <v-icon>mdi-chevron-up</v-icon>
                    </v-btn>
                    <v-btn
                      @click="downChild(index)"
                      v-if="index !== reply_pattern.data.children.length - 1"
                      icon
                    >
                      <v-icon>mdi-chevron-down</v-icon>
                    </v-btn>
                  </v-col>

                  <v-col cols="10">
                    <div class="text-h6">
                      {{ child_reply_pattern.name }}
                    </div>
                    <p class="text-caption">
                      <span v-if="child_reply_pattern.pattern != ''"
                        >「{{ child_reply_pattern.pattern }}」に</span
                      ><span>{{ child_reply_pattern.pattern_type.name }}</span>
                    </p>
                  </v-col>
                </v-row>
              </div>
            </div>
          </v-tab-item>
        </v-tabs-items>
      </v-card>
    </v-form>
  </v-navigation-drawer>
</template>

<script>
import DemoBlock from "../dashboard/DemoBlock";
import DemoNode from "../dashboard/DemoNode";
import { CREATED, UNPROCESSABLE_ENTITY } from '../../util';

export default {
  name: "RightNav",

  components: {
    DemoBlock,
    DemoNode,
  },

  props: ["pattern_types"],

  data: () => ({
    drawer: true,
    mini: true,
    tab: null,
    message_sortable_mode: false,
    children_sortable_mode: false,
    stateless: false,

    reply_pattern: {
      data: {
        id: null,
        title: null,
        description: null,
        state_reply_pattern_id: null,
        pattern: null,
        pattern_type: null,
        pattern_: null,
        parent_id: null,
        reply_pattern_messages: [],
        children: [],
      },
    },
    pattern_type: null,
    valid: true,
    hideMessageInput: false,
  }),

  methods: {
    upChild(index) {
      [
        this.reply_pattern.data.children[index - 1].rank,
        this.reply_pattern.data.children[index].rank,
      ] = [
        this.reply_pattern.data.children[index].rank,
        this.reply_pattern.data.children[index - 1].rank,
      ];
      this.reply_pattern.data.children.splice(
        index - 1,
        2,
        this.reply_pattern.data.children[index],
        this.reply_pattern.data.children[index - 1]
      );
    },
    downChild(index) {
      [
        this.reply_pattern.data.children[index].rank,
        this.reply_pattern.data.children[index + 1].rank,
      ] = [
        this.reply_pattern.data.children[index + 1].rank,
        this.reply_pattern.data.children[index].rank,
      ];
      this.reply_pattern.data.children.splice(
        index,
        2,
        this.reply_pattern.data.children[index + 1],
        this.reply_pattern.data.children[index]
      );
    },
    upMessage(index) {
      [
        this.reply_pattern.data.reply_pattern_messages[index - 1].rank,
        this.reply_pattern.data.reply_pattern_messages[index].rank,
      ] = [
        this.reply_pattern.data.reply_pattern_messages[index].rank,
        this.reply_pattern.data.reply_pattern_messages[index - 1].rank,
      ];
      this.reply_pattern.data.reply_pattern_messages.splice(
        index - 1,
        2,
        this.reply_pattern.data.reply_pattern_messages[index],
        this.reply_pattern.data.reply_pattern_messages[index - 1]
      );
    },
    downMessage(index) {
      [
        this.reply_pattern.data.reply_pattern_messages[index].rank,
        this.reply_pattern.data.reply_pattern_messages[index + 1].rank,
      ] = [
        this.reply_pattern.data.reply_pattern_messages[index + 1].rank,
        this.reply_pattern.data.reply_pattern_messages[index].rank,
      ];
      this.reply_pattern.data.reply_pattern_messages.splice(
        index,
        2,
        this.reply_pattern.data.reply_pattern_messages[index + 1],
        this.reply_pattern.data.reply_pattern_messages[index]
      );
    },
    async save() {
      if(this.$refs.form.validate()){
				const response = await axios.post('/api/reply_patterns/store', this.reply_pattern)

				// if (response.status === UNPROCESSABLE_ENTITY) {
				// 	return false
				// }

				// if (response.status !== CREATED) {
				// 	this.$store.commit('error/setCode', response.status)
				// 	return false
				// }
				this.mini = !this.mini
				this.$store.commit('node/setSelectedNodeId', null)
			} else {
				alert('入力事項に不備があります。修正して保存を実行してください。');
			}
    },
    deletePattern() {
      let confirmed = window.confirm(
        "応答パターンを削除する場合、紐づいている子・孫パターンも同時に削除されますがよろしいですか？"
      );
      // TODO: Ajaxの削除処理を追加する
    },
		closeRightNav() {
			if(this.$refs.form.validate()){
				this.mini = !this.mini
				this.$store.commit('node/setSelectedNodeId', null)
			} else {
				alert('入力事項に不備があります。修正して保存を実行してください。');
			}
		},
    toggleMessageSortableMode() {
      this.message_sortable_mode = !this.message_sortable_mode;
    },
    toggleChildrenSortableMode() {
      this.children_sortable_mode = !this.children_sortable_mode;
    },
    addReplyPatternMessage() {
      let rank = this.reply_pattern.data.reply_pattern_messages.length + 1;
      this.reply_pattern.data.reply_pattern_messages.push({
        message: {
          message_type_id: 1,
          text: null,
          rank: rank,
          repeat_flg: 0,
        },
      });
    },
    removeReplyPatternMessage(index) {
      this.reply_pattern.data.reply_pattern_messages.splice(index, 1);
    },
    getPatternTypeText(val) {
      if (val == "全てのメッセージ") {
        this.hideMessageInput = true;
      } else {
        this.hideMessageInput = false;
      }
    },
    showNav(selected_pattern) {
      // Dashboard.vueから呼び出す
      console.log(selected_pattern);
      this.reply_pattern = selected_pattern;
      this.pattern_type = this.pattern_types.find(
        (obj) => obj.text == selected_pattern.data.pattern_type
      );

      if(this.reply_pattern.id == '0'){
        console.log(selected_pattern);
        return false;
      }

      if (this.pattern_type.text == "全てのメッセージ") {
        this.hideMessageInput = true;
      } else {
        this.hideMessageInput = false;
      }
      this.mini = false;
    },
    onDragStartNewBlock(event) {
      console.log("onDragStartNewBlock", event);
      // contains all the props and attributes passed to demo-node
      const { props } = event;
      this.newDraggingBlock = props;
    },
    onDragStopNewBlock(event) {
      console.log("onDragStopNewBlock", event);
      this.newDraggingBlock = null;
    },
    toggleRepeatParent(bool){
      this.reply_pattern.data.state_reply_pattern_id = bool ? this.reply_pattern.data.parent_id : this.reply_pattern.data.id;
    }
  },

  computed: {
    rulesStateless() {
      const rules = [];
      const replyPatternMessageEmptyRule = () => {
        return (
          (this.stateless === true &&
            this.reply_pattern.data.reply_pattern_messages.length == 0) ||
          this.stateless === false ||
          "親のやり取りを繰り返す場合は、返信メッセージを空にしてください。"
        );
      };
      rules.push(replyPatternMessageEmptyRule);

      const replyPatternMessageNotEmptyRule = () => {
        return (
          (this.stateless === false &&
            this.reply_pattern.data.reply_pattern_messages.length > 0) ||
						this.stateless === true ||
          "1つ以上の返信メッセージを設定してください。"
        );
      };
      rules.push(replyPatternMessageNotEmptyRule);

      return rules;
    },
    rulesReplyPattern() {
      const rules = [];
      const rule = (v) => {
        return (
          this.pattern_type === "全てのメッセージ" ||
          (this.pattern_type !== "全てのメッセージ" &&
            !this.reply_pattern.data.pattern === false) ||
          "必須項目です。"
        );
      };
      rules.push(rule);

      return rules;
    },
    rulesReplyPatternMessage() {
      const rules = [];
      const rule = (v) => (v || "").length != 0 || "入力してください。";
      rules.push(rule);

      return rules;
    },
  },

  watch: {
    reply_pattern: function (newValue, oldValue) {
      this.stateless =
        newValue.data.state_reply_pattern_id == newValue.data.parent_id;
    },
  },
};
</script>

<style>
</style>