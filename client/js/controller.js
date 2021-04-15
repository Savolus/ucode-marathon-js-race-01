import { getCookie, deleteCookie, logger } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

deleteCookie("enemy")

const login = document.querySelector('.login')

login.innerHTML = getCookie('login')

const play = document.querySelector('.controller[data-play]')
const collection = document.querySelector('.controller[data-collection]')
const logout = document.querySelector('.controller[data-log-out]')

play.addEventListener('click', () => {
    location.replace('/find.html')
})

collection.addEventListener('click', () => {
    location.replace('/collection.html')
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
