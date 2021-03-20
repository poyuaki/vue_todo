<template>
　<div class="view-todo">
    <b-container fluid>
      <ul class="pl-0">
        <li v-for="content in userText" :key="content.keys" class="text-list">
          <b-row>
            <b-col md="9">
              <div class="text-contents">
                <p>{{content.contents | isNull}}</p>
              </div>
            </b-col>
            <b-col md>
                <div>
                  <b-button
                    size="sm"
                    block
                    variant="outline-info"
                    :aria-controls="'sidebar-details-' + content.keys"
                    aria-expanded="true"
                    @click="content.viewDetails = true"
                  >
                    詳細
                  </b-button>
                  <b-sidebar
                    :id="'sidebar-details-' + content.keys"
                    :aria-labelledby="'sidebar-details-' + content.keys"
                    :visible="content.viewDetails"
                    no-header
                    shadow
                    backdrop-variant="dark"
                    backdrop
                  >
                    <template #default="{ hide }">
                      <div class="p-2">
                        <h3 :id="'sidebar-details-' + content.keys" class="sidebar-title">{{content.contents}}</h3>
                      </div>
                      <div class="importance-star">
                        <div v-if="content.importance == 5">
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                        </div>
                        <div v-else-if="content.importance == 4">
                          <b-icon-star></b-icon-star>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                        </div>
                        <div v-else-if="content.importance == 3">
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                        </div>
                        <div v-else-if="content.importance == 2">
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star-fill></b-icon-star-fill>
                          <b-icon-star-fill></b-icon-star-fill>
                        </div>
                        <div v-else-if="content.importance == 1">
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star-fill></b-icon-star-fill>
                        </div>
                        <div v-else-if="content.importance == 0">
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                          <b-icon-star></b-icon-star>
                        </div>
                      </div>
                      <div class="mb-1 viewDate">
                        <p class="mb-0">{{content.viewDate | isViewDateNaN}}</p>
                      </div>
                      <div class="diff">
                        <p>{{content.diff | isDiffNaN}}</p>
                      </div>
                      <div class="comment">
                        <p>{{content.comment}}</p>
                      </div>
                      <div class="mb-3">
                        <b-container class="mb-3">
                          <b-row>
                            <b-col cols="6">
                              <b-button
                                variant="outline-primary"
                                @click="changeText(content.keys)"
                                class="pl-2 pr-2"
                                block
                              >
                                変更
                              </b-button>
                            </b-col>
                            <b-col cols="6">
                              <b-button
                                variant="success"
                                @click="deleteText(content.keys)"
                                class="pl-2 pr-2"
                                block
                              >
                                完了
                                <b-icon-clipboard-check/>
                              </b-button>
                            </b-col>
                          </b-row>
                        </b-container>
                        <b-button
                          variant="outline-info"
                          @click="hide;content.viewDetails = false"
                          block
                          class="mt-2"
                        >
                          閉じる
                        </b-button>
                      </div>
                    </template>
                  </b-sidebar>
                </div>
              </b-col>
            </b-row>
          </li>
        </ul>
      </b-container>
  </div>
</template>

<script>
export default {
    props:{
        userTodo:{
            type: Array
        }
    },
    data(){
        return{
            userText: this.userTodo,
            isView: false,
            starList: []
        }
    },
    filters:{
        isNull: function(value){
          if(value === "") return "No Title"
          else return value
        },
        isViewDateNaN: function(value){
          if(value === "NaN年aN月aN日") return ''
          else return value
        },
        isDiffNaN: function(value){
          if(value === "残り：NaN日") return ''
          else return value
        }
    },
    methods:{
        deleteText(value){
            this.$emit('user-delete-todo',value)
        },
        changeText(value){
            this.$emit('user-change-todo',value)
        }
    }
}
</script>

<style>

.text-list{
    list-style: none;
    margin: 0;
    padding: 0;
}

.text-contents{
    text-align: center;
    font-size: 1.2em;
    margin-top: 10px;
    margin-bottom: 10px;
}

.sidebar-title{
  color: #444
}

.importance-star{
  text-align: center;
  font-size: 1.5em;
  color: rgb(255, 208, 0)
}

.viewDate{
  text-align: center;
  font-size: 1.7em;
  color: #333
}

.diff{
  text-align: center;
  font-size: 1.5em;
  color: rgb(97, 51, 51)
}

.comment{
  font-size: 0.9em;
  color: #555
}

</style>