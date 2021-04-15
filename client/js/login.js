import { setCookie, getCookie, deleteCookie, logger } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

deleteCookie("enemy")

const login = document.querySelector('input[data-login]')

socket.onmessage = event => {
    const data = JSON.parse(event.data)

    switch (data.type) {
        case 'logged':
            if (!data.status) {
                deleteCookie('login')
            }

            logger()

            break
    } 
}

login.addEventListener('click', () => {
    setCookie('login', document.querySelector('#login').value)

    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )
})
