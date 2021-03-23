<template>
    <div>
        <div class="login-view">
            <h1 class="login-title mb-3">ログイン</h1>
            <b-button
                variant="outline-danger"
                size="lg"
                @click="handleSignIn()"
            >
                <b-icon-google/>
                Googleアカウントでログイン
            </b-button>
        </div>
        <error-alert
            :error-color="errorObj.color"
            :error-message="errorObj.message"
            :error-count="errorObj.count"
            @is-finish="errorFinish"
        />
    </div>
</template>

<script>
import ErrorAlert from './ErrorAlert'

export default {
    components:{
        ErrorAlert
    },
    data(){
        return{
            loginSuccess:{
                isSuccess: "ng",
                userImg: "./assets/logo.png"
            },
            errorObj:{
                count: 0,
                color: '',
                message: '',
                trigger: false
            }
        }
    },
    methods:{
        async handleSignIn () {
            try {
                const googleUser = await this.$gAuth.signIn()
                if (!googleUser) {
                    this.errorObj.color = 'danger'
                    this.errorObj.message = 'ログインに失敗しました。再度試してください。'
                    this.errorObj.count = 5
                    return null
                }
                let id = googleUser.getId()
                let user = googleUser.getBasicProfile()
                let userImg = user.getImageUrl()
                if(!userImg) userImg = "./assets/logo.png"
                let data = {
                    idtoken: id,
                    userimg: userImg
                }
                fetch("./php/userLogin.php",{
                    method: 'POST',
                    headers:{
                        'Content-Type' : 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(response => {
                })
                this.$set(this.loginSuccess, 'isSuccess', 'ok')
                this.$set(this.loginSuccess, 'userImg',userImg)
                this.$emit('login-success',this.loginSuccess)
            } catch (error) {
                this.errorObj.color = 'danger'
                this.errorObj.message = 'ログインに失敗しました。再度試してください。'
                this.errorObj.count = 5
                return null
            }
        },
        errorFinish(value){
            if(value) this.errorObj.count = 0
        }
    }
}
</script>

<style scoped>
.login-title{
    text-align: center;
    color: #42b983;
}

.login-view{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
}

</style>