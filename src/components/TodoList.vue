<template>
    <main>
        <transition name="vshow">
            <div v-if="isFadeOut && canViewTodo">
                <input-form
                    :buttoncomment="buttonComment"
                    @user-enter-todo="addTextToList"
                    @now-user-input="setInput"
                />
                <b-container class="mt-4 mb-2">
                    <b-row>
                        <b-col col="6" class="text-center">
                          <div class="sort">
                            <b-dropdown id="dropdown-sort" variant="outline-primary">
                              <template #button-content>
                                <b-icon-clipboard-data/>ソート順
                              </template>
                              <b-dropdown-item :active="sortActive" @click="sortBy('period')"><b-icon-calendar class="mr-1"/>日付優先</b-dropdown-item>
                              <b-dropdown-item :active="!sortActive" @click="sortBy('importance')"><b-icon-exclamation class="mr-1"/>重要度優先</b-dropdown-item>
                            </b-dropdown>
                          </div>
                        </b-col>
                        <b-col col="6" class="text-center">
                          <b-button
                            variant="outline-warning"
                            @click="logOut()"
                            class="pl-2 pr-2"
                            size="sm"
                          >
                            ログアウト
                            <b-icon-box-arrow-right/>
                          </b-button>
                        </b-col>
                    </b-row>
                </b-container>
                <view-todo
                    :user-todo="textList"
                    @user-details-todo="viewDetails"
                />
                <error-alert
                    :error-color="errorObj.color"
                    :error-message="errorObj.message"
                    :error-count="errorObj.count"
                    @is-finish="errorFinish"
                />
                <div class="retry-button">
                    <b-alert
                        :show="retryCount"
                        variant="secondary"
                        dismissible
                        fade
                        @dismiss-count-down="decRetryCount"
                        class="mt-0"
                    >
                        完了しました!<a @click="backDelete()" class="alert-link ml-3">元に戻す</a>
                    </b-alert>
                </div>
                <div class="sidebar">
                  <b-sidebar
                    id='sidebar-details'
                    shadow
                    backdrop-variant="dark"
                    backdrop
                    :title="detail.contents"
                  >
                    <div class="importance-star">
                      <div v-if="detail.importance == 5">
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                      </div>
                      <div v-else-if="detail.importance == 4">
                        <b-icon-star/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                      </div>
                      <div v-else-if="detail.importance == 3">
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                      </div>
                      <div v-else-if="detail.importance == 2">
                        <b-icon-star></b-icon-star>
                        <b-icon-star></b-icon-star>
                        <b-icon-star></b-icon-star>
                        <b-icon-star-fill/>
                        <b-icon-star-fill/>
                      </div>
                      <div v-else-if="detail.importance == 1">
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star-fill/>
                      </div>
                      <div v-else-if="detail.importance == 0">
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star/>
                        <b-icon-star/>
                      </div>
                    </div>
                    <div class="mb-1 viewDate">
                      <p class="mb-0">{{detail.viewDate | isViewDateNaN}}</p>
                    </div>
                    <div class="diff">
                      <p>{{detail.diff | isDiffNaN}}</p>
                    </div>
                    <div class="comment pl-2 pr-2">
                      <p>{{detail.comment}}</p>
                    </div>
                    <div class="mb-3">
                      <b-container class="mb-3">
                        <b-row>
                          <b-col cols="6">
                            <b-button
                              variant="outline-primary"
                              @click="changeText(detail.keys)"
                              class="pl-2 pr-2"
                              block
                            >
                              変更
                            </b-button>
                          </b-col>
                          <b-col cols="6">
                            <b-button
                              variant="success"
                              @click="deleteText(detail.keys)"
                              class="pl-2 pr-2"
                              block
                            >
                              完了
                              <b-icon-clipboard-check/>
                            </b-button>
                          </b-col>
                        </b-row>
                      </b-container>
                    </div>
                  </b-sidebar>
                </div>
            </div>
        </transition>
    </main>
</template>

<script>
import InputForm from './InputForm'
import ViewTodo from './ViewTodo'
import ErrorAlert from './ErrorAlert'

export default {
    components:{
        InputForm,ViewTodo,ErrorAlert
    },
    props: {
        todoList: {
          type: Array
        },
        canViewTodo:{
            type: Boolean
        },
        sortType:{
          type: String
        }
    },
    data(){
        return{
            retryCount: 0,
            textList: this.todoList,
            todoText: '',
            userInputError:{
                judge: false,
                comment: ''
            },
            beforeTodo:{
                index: 0,
                keys: 0,
                contents: '',
                importance: 0,
                comment: '',
                period: '',
                viewDate: '',
                diff: ''
            },
            deleteGrace: false,
            isFadeOut: true,
            canViewTodo: this.canViewTodo,
            errorObj:{
                color: '',
                message: '',
                count: 0
            },
            sortObj:{
              isPeriod: true,
              sortType: this.sortType
            },
            detail:{
              keys: 0,
              contents: '',
              importance: 0,
              comment: '',
              period: '',
              viewDate: '',
              diff: ''
            }
        }
    },
    methods:{
        setInput(value){
            this.todoText = value
        },
        backDelete(){
            if(!this.deleteGrace) return
            let data = {
                keys: this.beforeTodo.keys,
                contents: this.beforeTodo.contents,
                importance: this.beforeTodo.importance,
                comment: this.beforeTodo.comment,
                period: this.beforeTodo.period,
                viewDate: this.beforeTodo.viewDate,
                diff: this.beforeTodo.diff,
                viewDetails: false
            }
            fetch("./php/reAddTodo.php",{
                method: 'POST',
                headers:{
                    'Content-Type' : 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(response => {
                this.textList.splice(this.beforeTodo.index, 0, data)
                this.deleteGrace = false
            })
        },
        addTextToList(value){
            let obj = {}
            if(this.addText === "" || this.addText === null) return
            else obj.contents = value
            if(value.length > 100) return
            let data = { todo: obj.contents }
            fetch("./php/addTodo.php",{
                method: 'POST',
                headers:{
                    'Content-Type' : 'application/json',
                    },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(response => {
                obj.keys = Number(response["id"])
                obj.viewDetails = false
                this.textList.unshift(obj)
                this.deleteGrace = false
            })
        },
        deleteText(value){
            this.deleteGrace = true
            this.retryCount = 5
            let index = this.textList.findIndex(item => item.keys === value)

            this.beforeTodo.index = index
            this.beforeTodo.contents = this.textList[index].contents
            this.beforeTodo.keys = value
            this.beforeTodo.importance = this.textList[index].importance
            this.beforeTodo.comment = this.textList[index].comment
            this.beforeTodo.period = this.textList[index].period
            this.beforeTodo.viewDate = this.textList[index].viewDate
            this.beforeTodo.diff = this.textList[index].diff

            this.textList.splice(index,1)
            let data = {index: value}
            fetch("./php/deleteTodo.php",{
                method:'POST',
                headers:{
                    'Content-Type' : 'application/json',
                },
                body: JSON.stringify(data)
            })
        },
        changeText(value){
            let index = this.textList.findIndex(item => item.keys === value)
            //this.textList[index].contents = this.todoText
            let data = {
                contents: this.textList[index].contents,
                importance: this.textList[index].importance,
                comment: this.textList[index].comment,
                period: this.textList[index].period,
                keys: value
            }
            this.$emit('user-change-todo',data)
        },
        async logOut(){
            const response = await this.$gAuth.signOut()
            if(response){
                let xhr = new XMLHttpRequest()
                let that = this
                xhr.open('GET', './php/deleteToken.php')
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
                xhr.onload = function() {
                    that.isFadeOut = false
                    setTimeout(() => {
                        window.location.href = "./"
                    }, 1000);       //下のvshow-enter-activeのopacityの値と等しくなるように!!
                }
                xhr.send()
            }else{
                this.errorObj.message = "ログアウトに失敗しました。再度試してください。"
                this.errorObj.color = "danger"
                this.errorObj.count = 5
            }
        },
        decRetryCount(retryCount){
            this.retryCount = retryCount
        },
        errorFinish(value){
            if(value) this.errorObj.count = 0
        },
        sortBy(sortType){
          this.$emit('sort-type-name', sortType)
          if(sortType == "period") this.sortObj.isPeriod = true
          else if(sortType == "importance") this.sortObj.isPeriod = false
        },
        viewDetails(value){
          let index = this.textList.findIndex(item => item.keys === value)
          this.detail = this.textList[index]
        }
    },
    computed:{
        buttonComment(){
            this.$emit('todo-len',this.textList.length)
            if(this.textList.length) return "追加"
            else return "新規"
        },
        sortActive(){
          let sortType = this.sortObj.sortType
          if(sortType == "period") this.sortObj.isPeriod = true
          else if(sortType == "importance") this.sortObj.isPeriod = false
          return this.sortObj.isPeriod
        }
    }
}

</script>

<style scoped>
.error{
    text-align: center;
}

.message{
    font-size: 1.2em;
    color: #e2041b
}

.retry-button{
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    z-index: 2;
}

.vshow-enter-active, .vshow-leave-active{
    transition: all .5s
}

.vshow-enter, .vshow-leave-to{
    opacity: 0;
    transform: translateY(30px);
}

</style>