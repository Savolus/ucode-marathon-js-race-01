import { setCookie, logger } from './cookies.js'

const login = document.querySelector('input[data-login]')

login.addEventListener('click', () => {
    setCookie('login', document.querySelector('#login').value)
    logger()
})
