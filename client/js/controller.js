import { getCookie, deleteCookie, logger } from './cookies.js'

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
