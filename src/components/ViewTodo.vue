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
                    aria-controls="sidebar-details"
                    v-b-toggle.sidebar-details
                    @click="viewDetails(content.keys)"
                    variant="outline-info"
                    block
                    size="sm"
                  >
                  詳細<b-icon-chat-text class="ml-2"/>
                  </b-button>
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
        viewDetails(value){
          this.$emit('user-details-todo',value)
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