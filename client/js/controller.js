import { deleteCookie, logger } from './cookies.js'

const play = document.querySelector(".controller[data-play]")
const logout = document.querySelector(".controller[data-log-out]")

play.addEventListener('click', () => {
    location.replace("/find.html")
})

logout.addEventListener('click', () => {
    deleteCookie('login')
    logger()
})
