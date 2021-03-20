<template>
  <div id="app">
      <transition name="fademain">
        <div v-show="isFadeIn">
          <todo-header
            :headerMessage="message.headerMessage"
            :userImg="message.userImg"
            :can-view-header="isCanView"
          />
          <todo-change
            v-if="(bool.isLogin || isAlreadyLogin) && isSetting"
            :change-todo-list="changeTodo"
            :can-view="isSetting"
            @finish-setting="finishSetting"
          />
          <todo-list
            @todo-len="headerSub"
            v-if="(bool.isLogin || isAlreadyLogin) && !isSetting"
            :todo-list="textList"
            :can-view-todo="isCanView"
            @user-change-todo="settingText"
          />
          <login-view
            v-if="!(bool.isLogin || isAlreadyLogin) && !isSetting"
            @login-success="successLogin"
          />
        </div>
      </transition>
  </div>
</template>

<script>

import TodoHeader from './components/Header'
import TodoList from './components/TodoList'
import LoginView from './components/LoginView'
import TodoChange from './components/TodoChange'

export default {
  name: 'App',
  components:{
      TodoHeader,TodoList,LoginView,TodoChange
  },
  data(){
    return{
      message:{
        headerMessage: "Made by Vue.js",
        userImg: "./assets/logo.png"
      },
      bool:{
        isLogin: false,
        viewStart: true
      },
      textList:[],
      isFadeIn: false,
      isCanView: false,
      isSetting: false,
      changeTodo: null       //変更する時用の配列
    }
  },
  computed:{
    isAlreadyLogin(){
      fetch("./php/getSession.php")
      .then(response => {return response.json()})
      .then(data=>{
        this.bool.isLogin = data.canLogin && !!data.userid
        if(this.bool.isLogin){
          this.$set(this.message, 'userImg', data.userImg)
          this.addTextList()
        }
        return
      })
    }
  },
  methods:{
    headerSub(value){
      if(value) this.$set(this.message, 'headerMessage', `${value}個追加`)
      else  this.$set(this.message, 'headerMessage', "Made By Vue.js")
    },
    successLogin(value){
      if(value.isSuccess == "ok"){
        this.$set(this.bool, 'isLogin', true)
        this.$set(this.message, 'userImg', value.userImg)
        this.addTextList()
      }
    },
    settingText(value){
      this.isCanView = false
      this.message.headerMessage = "設定"
      this.changeTodo = value
      this.isSetting = true
    },
    finishSetting(value){
      this.isSetting = false
      if(value){
        this.textList = []
        this.addTextList()
      }else{
        this.isCanView = true
      }
    },
    addTextList(){
      setTimeout(() => {
        let that = this
        let request = new Request("./php/setText.php")
        fetch(request)
        .then(function(response){
          if(!response.ok) throw new Error("HTTP error! statue:" + response.status)
          return response.json()
        })
        .then(function(response){       //PHPとの非同期通信に成功した後の処理
          let todoList = response["todo"]
          let todoIdList = response["todo_id"]
          let todoImportanceList = response["importance"]
          let todoCommentList = response["comment"]
          let todoDateList = response["period"]
          for(let i = 0;i < todoList.length;i++){     //todoの追加
            let obj = {}
            obj.contents = todoList[i]
            obj.keys = Number(todoIdList[i])
            obj.importance = Number(todoImportanceList[i])
            obj.comment = todoCommentList[i]
            obj.period = todoDateList[i]
            obj.viewDetails = false

            let formatDate = (dt, formatType) =>{
              let y = dt.getFullYear()
              let m = ('00' + (dt.getMonth()+1)).slice(-2)
              let d = ('00' + dt.getDate()).slice(-2)
              if(formatType ===  "-")  return (y + '-' + m + '-' + d)
              else if(formatType === "年月日") return (y + '年' + m + '月' + d + '日')
            }
            function getDateDiff(dateString1, dateString2) {
              // 日付を表す文字列から日付オブジェクトを生成
              let date1 = new Date(dateString1);
              let date2 = new Date(dateString2);
              // 2つの日付の差分（ミリ秒）を計算
              let msDiff  = date1.getTime() - date2.getTime();
              // 求めた差分（ミリ秒）を日付に変換
              // 差分÷(1000ミリ秒×60秒×60分×24時間)
              return Math.ceil(msDiff / (1000 * 60 * 60 *24));
            }

            let date = todoDateList[i]
            if(date === null){
              obj.viewDate = ''
              obj.diff = ''
            }else{
              date = new Date(date)
              let viewDate = formatDate(date,"年月日")
              obj.viewDate = viewDate
              let dateString = formatDate(date, "-")
              let today = formatDate(new Date(), "-")
              let diff = getDateDiff(dateString,today)
              let sendComment;
              if(diff < 0){
                sendComment = "超過：" + (diff * -1) + "日"
              }else if(diff == 0){
                sendComment = "今日が予定日です"
              }else{
                sendComment = "残り：" + diff + "日"
              }
              obj.diff = sendComment
            }
            that.textList.push(obj)
          }
          that.isCanView = true
        })
      }, 1000);
    }
  },
  mounted(){
    let that = this
    this.$nextTick(function () {
      that.isFadeIn = true
    })
  }
}
</script>

<style>
body{
  font-family: 'Lato', 'Noto Sans JP', 'ヒラギノ角ゴ ProN', 'Hiragino Kaku Gothic ProN', 'メイリオ', Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;
}

.fademain-enter-active, .fademain-leave-active {
  transition: opacity 2s;
}
.fademain-enter, .fademain-leave-to{
  opacity: 0;
}
</style>