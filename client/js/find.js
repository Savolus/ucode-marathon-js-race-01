import { setCookie, getCookie, deleteCookie } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

deleteCookie("enemy")

socket.onmessage = event => {
    const data = JSON.parse(event.data)

    switch (data.type) {
        case 'logged':
            socket.send(
                JSON.stringify({
                    type: 'find',
                    user: getCookie('login')
                })
            )

            break
        case 'start':
            setCookie('enemy', data.enemy)

            location.replace('/battle.html')

            break
    }
}

socket.onerror = event => {
    console.log(event.message)
}

socket.onopen = () => {
    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )
}
