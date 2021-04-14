import { setCookie, getCookie, deleteCookie } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

const title = document.querySelector('title')

title.innerHTML += ` ${getCookie('enemy')} vs ${getCookie('login')}`

const p1 = document.querySelector('.p1')
const p2 = document.querySelector('.p2')

p1.innerHTML = getCookie('enemy')
p2.innerHTML = getCookie('login')

socket.onmessage = event => {
    const data = JSON.parse(event.data)

    console.log(data)
    
    switch (data.type) {
        case 'logged':
            // make him draw started hand
            break
        case 'end':
            deleteCookie("enemy")
            location.replace('/')
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
