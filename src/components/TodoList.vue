<template>
    <main>
        <transition name="vshow">
            <div v-if="isFadeOut && canViewTodo">
                <input-form
                    :buttoncomment="buttonComment"
                    @user-enter-todo="addTextToList"
                    @now-user-input="setInput"
                />
                <b-container fluid>
                    <b-row>
                        <b-col md="9">
                        </b-col>
                        <b-col md="3">
                            <div style="text-align: center;">
                                <b-button
                                    variant="outline-warning"
                                    @click="logOut()"
                                    class="pl-2 pr-2 mb-3 mt-3"
                                    size="sm"
                                >
                                ログアウト
                                <b-icon-box-arrow-right></b-icon-box-arrow-right>
                                </b-button>
                            </div>
                        </b-col>
                    </b-row>
                </b-container>
                <view-todo
                    :user-todo="textList"
                    @user-delete-todo="deleteText"
                    @user-change-todo="changeText"
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
        }
    },
    computed:{
        buttonComment(){
            this.$emit('todo-len',this.textList.length)
            if(this.textList.length) return "追加"
            else return "新規"
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