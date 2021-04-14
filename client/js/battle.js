import { setCookie, getCookie, deleteCookie } from './cookies.js'

const socket = new WebSocket('ws://127.0.0.1:8080');

const title = document.querySelector('title')

title.innerHTML += ` ${getCookie('enemy')} vs ${getCookie('login')}`

const p1 = document.querySelector('.p1')
const p2 = document.querySelector('.p2')

p1.innerHTML = getCookie('enemy')
p2.innerHTML = getCookie('login')

socket.onmessage = event => {
    const data = JSON.parse(event.data)
    
    switch (data.type) {
        case 'end':
            location.replace('/')
            deleteCookie("enemy")
            break
    }
}

socket.onopen = () => {
    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )
}
