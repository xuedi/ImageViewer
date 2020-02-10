import Api from '@/services/Api'

export default {
  register (credentials) {
    return Api().get('register', credentials)
  },
  login (credentials) {
    return Api().get('login', credentials)
  }
}
