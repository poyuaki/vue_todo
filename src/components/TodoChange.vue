<template>
  <transition name="vsetting">
    <div v-if="canView">
      <!--Todo-->
      <div role="group">
        <label for="input-contents">Todo</label>
        <b-form-input
          v-model="afterTodoList.contents"
          :placeholder="changeTodoList.contents"
          id="input-contents"
          :state="inputErrorObj.contents"
        ></b-form-input>
        <b-form-invalid-feedback id="input-contents-feedback">
          100文字以内で入力してください
        </b-form-invalid-feedback>
      </div>
      <!--重要度-->
      <div role="group" class="mt-3">
        <label for="input-importance">重要度</label>
        <b-form-input
          v-model="afterTodoList.importanceChar"
          :placeholder="changeTodoList.importance"
          id="input-importance"
          type="number"
          :state="inputErrorObj.importance"
        ></b-form-input>
        <b-form-invalid-feedback id="input-importance-feedback">
          0から5までの整数を入力してください
        </b-form-invalid-feedback>
      </div>
      <!--コメント-->
      <div role="group" class="mt-3">
        <label for="input-comment">コメント</label>
        <b-form-textarea
          v-model="afterTodoList.comment"
          :placeholder="changeTodoList.comment"
          id="input-comment"
          rows="3"
          :state="inputErrorObj.comment"
        ></b-form-textarea>
        <b-form-invalid-feedback id="input-comment-feedback">
          200文字以内で入力してください
        </b-form-invalid-feedback>
      </div>
      <!--日付-->
      <div role="group" class="mt-3">
        <label for="input-period">期限</label>
        <b-form-datepicker
          id="input-period"
          :disabled="isPeriodNull"
          v-model="afterTodoList.period"
          :placeholder="changeTodoList.period"
          :state="inputErrorObj.period"
        ></b-form-datepicker>
        <b-form-invalid-feedback id="input-period-feedback">
          未来の日付を入力してください
        </b-form-invalid-feedback>
      </div>
      <div style="text-align: center">
        <b-button
          @click="changePeriod()"
          variant="outline-info"
          size="sm"
          class="mt-2"
        >
        {{periodButtonMessage}}
        </b-button>
      </div>
      <b-container fluid class="mt-3">
        <b-row class="text-center">
          <b-col sm="6" class="mb-3">
            <b-button
              @click="decisionSetting()"
              variant="outline-success"
            >
              確定
            </b-button>
          </b-col>
          <b-col sm="6">
            <b-button
              @click="finishSetting()"
              variant="outline-secondary"
            >
              閉じる
            </b-button>
          </b-col>
        </b-row>
      </b-container>
      <error-alert
        :error-color="errorObj.color"
        :error-message="errorObj.message"
        :error-count="errorObj.count"
        @is-finish="errorFinish"
      />
    </div>
  </transition>
</template>

<script>
import ErrorAlert from './ErrorAlert'

export default {
  components:{
    ErrorAlert
  },
  props:{
    changeTodoList:{
      type: Object
    },
    canView:{
      type: Boolean
    }
  },
  data(){
    return{
      changeTodoList: this.changeTodoList,        //設定前の値を格納するオブジェクト
      afterTodoList:{     //設定後の値を格納するオブジェクト
        contents: '',
        importance: 0,
        comment: '',
        period: '',
        keys: this.changeTodoList.keys,
        importanceChar: '',
        isChanged: false,    //チェンジされたか
      },
      errorObj:{
        count: 0,
        message: '',
        color: ''
      },
      inputErrorObj:{
        contents: null,
        comment: null,
        period: null,
        importance: null
      },
      isPeriodNull: false,
      canView: this.canView
    }
  },
  methods:{
    finishSetting(){
      this.$emit('finish-setting',false)
    },
    decisionSetting(){
      for(let i = 0; i < Object.keys(this.inputErrorObj).length;i++){   //初期化
        this.inputErrorObj[Object.keys(this,this.inputErrorObj)[i]] = null
      }


      if(this.afterTodoList.contents == '') this.afterTodoList.contents = this.changeTodoList.contents
      if(this.afterTodoList.comment == '') this.afterTodoList.comment = this.changeTodoList.comment
      if(this.afterTodoList.importanceChar == '') this.afterTodoList.importanceChar = this.changeTodoList.importance
      if((this.afterTodoList.period == '' || this.afterTodoList.period == null) && !this.isPeriodNull) this.afterTodoList.period = this.changeTodoList.period
      if(this.isPeriodNull) this.afterTodoList.period = null

      this.afterTodoList.importance = Number(this.afterTodoList.importanceChar)

      let formatDate = dt =>{
        let y = dt.getFullYear()
        let m = ('00' + (dt.getMonth()+1)).slice(-2)
        let d = ('00' + dt.getDate()).slice(-2)
        return (y + '-' + m + '-' + d)
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
      let today = formatDate(new Date())
      let diff = getDateDiff(this.afterTodoList.period,today)
      if(this.afterTodoList.contents.length > 100 || this.afterTodoList.comment.length > 200 || (diff < 0 && !this.isPeriodNull) || this.afterTodoList.importance < 0 || this.afterTodoList.importance > 5 || !Number.isInteger(this.afterTodoList.importance)){
        if(this.afterTodoList.contents.length > 100)  this.inputErrorObj.contents = false
        else if(this.afterTodoList.comment.length > 200) this.inputErrorObj.comment = false
        else if(this.afterTodoList.importance < 0 || this.afterTodoList.importance > 5 || !Number.isInteger(this.afterTodoList.importance)) this.inputErrorObj.importance = false
        else if(diff < 0 && !this.isPeriodNull) this.inputErrorObj.period = false
        return
      }
      let data = {
        update: this.afterTodoList.contents,
        index: this.afterTodoList.keys,
        comment: this.afterTodoList.comment,
        importance: this.afterTodoList.importance,
        period: this.afterTodoList.period
      }
      let that = this
      fetch("./php/updateTodo.php",{
        method:'POST',
        headers:{
          'Content-Type' : 'application/json',
        },
        body: JSON.stringify(data)
      }).then(response => {return response})
        .then(data=>{
          this.$emit('finish-setting',true)
        })
    },
    errorFinish(value){
      if(value) this.errorObj.count = 0
    },
    changePeriod(){
      this.isPeriodNull = !this.isPeriodNull
    }
  },
  computed:{
    periodButtonMessage(){
      if(!this.isPeriodNull) return "期限を無効にする"
      else return "期限を有効にする"
    }
  }
}
</script>

<style>
.vsetting-enter-active, .vsetting-leave-active{
    transition: opacity .5s
}

.vsetting-enter, .vsetting-leave-to{
    opacity: 0;
}
</style>