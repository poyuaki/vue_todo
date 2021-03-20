<template>
  <div class="error-button">
    <b-alert
      :show="errorCount"
      :variant="errorColor"
      dismissible
      fade
      @dismiss-count-down="decErrorCount"
      class="mt-0"
        >
            {{errorMessage}}
        </b-alert>
    </div>
</template>

<script>
export default {
    props:{
        errorColor:{
            type: String,
            default: 'danger'
        },
        errorMessage:{
            type: String
        },
        errorCount:{
            type: Number,
            default: 0
        }
    },
    data(){
        return{
            errorCount: this.errorCount,
            errorMessage: this.errorMessage,
            errorColor: this.errorColor
        }
    },
    methods:{
        decErrorCount(errorCount){
            this.errorCount = errorCount
            if(this.errorCount == 0) this.$emit('is-finish',true)
        }
    }
}
</script>

<style>

.error-button{
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
}

</style>