<template>
  <q-page class="">
    <div class="row q-mb-xl" style="height: 70vh;">
      <div class="col col-xs-12 col-sm-12 col-md-6 flex items-center justify-center" style="z-index: 9999;">
        <q-card class="my-card" flat bordered>
            <q-card-section>
                <q-form ref="form" class="q-gutter-md q-mt-xl" @submit="submitLogin">
                    <q-card-section style="max-height: 60vh;" class="scroll">
                        
                        <q-input 
                            bottom-slots
                            v-model="form.username"
                            v-bind="formRules.username"
                            :label="$t('form_labels.user')" 
                            :dense="true"
                        >
                            <template v-slot:prepend>
                                <q-icon name="account_circle" />
                            </template>
                            <template v-slot:append>
                                <q-icon name="close" @click="form.username = ''" class="cursor-pointer" />
                            </template>
                        </q-input>

                        <q-input 
                            bottom-slots 
                            v-model="form.password"
                            v-bind="formRules.password"
                            :type="isPwd ? 'password' : 'text'"
                            :label="$t('form_labels.pass')" 
                            :dense="true"
                        >
                            <template v-slot:prepend>
                                <q-icon name="password" />
                            </template>
                            <template v-slot:append>
                                <q-icon
                                    :name="isPwd ? 'visibility_off' : 'visibility'"
                                    class="cursor-pointer"
                                    @click="isPwd = !isPwd"
                                />
                            </template>
                        </q-input>
                    </q-card-section>

                    <q-card-actions>
                        <q-btn 
                            type="submit" 
                            label="Login" 
                            :loading="btnLoading"
                            class="full-width fredoka q-mb-sm customLoginBtn"  
                            @click="submitForm"
                        />
                    </q-card-actions>
                </q-form>
            </q-card-section>
        </q-card>
      </div>
      <div class="col col-xs-12 col-sm-12 col-md-4 flex items-center" style="z-index:999;">
        <span class="text-h2 text-bold">MASTER IN <span class="text-orange">HOSPITAL ADMINISTRATION</span></span>
        <span class="text-subtitle1">Mauris malesuada enim eget blandit gravida. Duis hendrerit cursus turpis, id mollis est rutrum nec. Sed interdum nisi id libero tincidunt, sit amet vestibulum tortor porttitor. Cras ligula lacus, ullamcorper sed</span>
      
      </div>
      <div class="col col-md-2"></div>
      <div class="backgroundHeader"></div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { loginapi } from 'boot/loginAxios'

export default defineComponent({
  name: 'PageIndex',
  components: {},
  data(){
    return {
      text: '',
      slide: 1,
        form: {
            username: "",
            password: "",
        },
        formRules: {
            username: {
                rules: [
                    value => !!value || this.$t('validations_error.empty'),
                ]
            },
            password: {
                rules: [
                    val => !!val || this.$t('validations_error.empty'),
                ]
            },
        },
        isPwd: true,
        submitting: false,
    }
  },
  methods: {
    async submitLogin(){
        this.submitting = true;
        let vm = this;
        let payload = vm.form;

        loginapi.post('auth/login', payload).then((response) => {
          
            const data = {...response.data};
            if(!data.error){
                LocalStorage.set('userData', data.jwt);
                // Session Login with Expiration
                SessionStorage.set('userDataLogin', data.jwt);
                this.$router.push('/admin/dashboard')
            } else {
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    title:data.title,
                    message: this.$t(`errors.${data.error}`),
                    icon: 'report_problem'
                })
            }

        })

        this.submitting = false;
    }
  }
})
</script>

<style scoped>
.my-card{
    width: 30vw;
    height: 40vh;
    border-radius: 20px;
}
.backgroundHeader{
  position: absolute;
  background: url('/imgs/loginbg.svg') no-repeat;
  height: 80vh;
  width: 100%;
  background-position: 50% 60%;
  background-size: 60%;
  opacity: 0.2;
}
.backgroundMid{
  position: absolute;
  background: url('/imgs/background2.svg') no-repeat;
  height: 60vh;
  width: 34%;
  background-position: -25% 66%;
  /* background-size: 60%;
  opacity: 0.2; */
}
.footerBackground{
  height: 30vh;
}
.customLoginBtn {
    color: white;
    border-radius: 20px;
    background: rgb(0,177,250);
    background: radial-gradient(circle, #fa6a60 0%, rgb(183 28 28) 91%);
}
</style>
