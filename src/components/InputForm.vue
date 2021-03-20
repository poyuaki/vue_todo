<template>
    <div class="input-form">
        <b-container fluid>
            <b-row>
                <b-col sm="10">
                    <b-form-input
                        id="input-todo"
                        v-model="addText"
                        placeholder="Todoを追加"
                        class="user-input mb-3"
                        :state="canAdd"
                        @keyup.enter.shift ="EnterTodo"
                        @input="addUserInput()"
                    >
                    </b-form-input>
                    <b-form-invalid-feedback id="input-todo-feedback">
                        {{errorMessage}}
                    </b-form-invalid-feedback>
                </b-col>
                <b-col>
                    <b-button
                        variant="outline-success"
                        class="pl-3 pr-3"
                        @click="EnterTodo"
                    >
                        {{buttoncomment}}
                        <b-icon-clipboard-plus></b-icon-clipboard-plus>
                    </b-button>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
export default {
    props:{
        buttoncomment:{
            type: String
        }
    },
    data(){
        return{
            addText: '',
            canAdd: null,
            errorMessage: ''
        }
    },
    methods:{
        addUserInput(){
            this.$emit('now-user-input', this.addText)
        },
        EnterTodo(){
            if(this.addText.length > 100){
                this.canAdd = false
                this.errorMessage = "100文字以内で入力してください"
                return
            }
            if(this.addText === "" || this.addText === null){
              this.errorMessage = "値を入れてください"
              this.canAdd = false
              return
            }
            this.$emit('user-enter-todo',this.addText)
            this.addText = ''
            this.canAdd = null
        }
    }
}
</script>

<style>
.input-form{
    text-align: center;
    margin-top: 1.3em;
}


.user-input{
    width: 50%;
    margin-right: 1em
}
</style>