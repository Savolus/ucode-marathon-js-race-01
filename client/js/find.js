import { getCookie } from './cookies.js'

const socket = new WebSocket('ws://127.0.0.1:8080');

socket.onmessage = event => {
    console.log(event.data)
}

socket.onopen = () => {
    const data = 
    socket.send(
        JSON.stringify({
            type: 'find',
            user: getCookie('login')
        })
    )
}
