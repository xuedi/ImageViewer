<template>
  <div>
    <h1>Register</h1>
    <p>
      <label>
        User:
        <input
          name="email"
          type="email"
          v-model="email"
        />
      </label>

      <br/>

      <label>
        Password:
        <input
          name="password"
          type="password"
          v-model="password"
        />
      </label>

      <br/>

      <button @click="register">Register</button>

    </p>
  </div>
</template>

<script>
import AuthenticationService from '../services/AuthenticationService'
export default {
  name: 'HelloWorld',
  data () {
    return {
      email: 'test@test.de',
      password: '123456'
    }
  },
  methods: {
    async register () {
      try {
        const response = await AuthenticationService.register({
          email: this.email,
          password: this.password
        })
        console.log('got response: ', response.data)
      } catch (error) {
        console.log('error: ', error.response.data.error)
      }
    }
  }
}
</script>

<style scoped>

</style>
