import { setCookie, getCookie } from './cookies.js'

const socket = new WebSocket('ws://127.0.0.1:8080');

socket.onmessage = event => {
    const data = JSON.parse(event.data)

    switch (data.type) {
        case 'start':
            setCookie('enemy', data.enemy)

            location.replace('/battle.html')

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
    socket.send(
        JSON.stringify({
            type: 'find',
            user: getCookie('login')
        })
    )
}
