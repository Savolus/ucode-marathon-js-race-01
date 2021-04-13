import { getCookie, deleteCookie, logger } from './cookies.js'

const socket = new WebSocket('ws://127.0.0.1:8080');

const login = document.querySelector('.login')

login.innerHTML = getCookie('login')

const play = document.querySelector('.controller[data-play]')
const logout = document.querySelector('.controller[data-log-out]')

play.addEventListener('click', () => {
    location.replace('/find.html')
})

logout.addEventListener('click', () => {
    deleteCookie('login')
    logger()
})

socket.onopen = () => {
    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )
}
