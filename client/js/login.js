import { setCookie, getCookie, deleteCookie, logger } from './cookies.js'

const socket = new WebSocket('ws://127.0.0.1:8080');

const login = document.querySelector('input[data-login]')

login.addEventListener('click', () => {
    setCookie('login', document.querySelector('#login').value)
    deleteCookie("enemy")

    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )

    logger()
})
