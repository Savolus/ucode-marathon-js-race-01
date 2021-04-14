import { setCookie, getCookie, deleteCookie } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

const title = document.querySelector('title')

title.innerHTML += ` ${getCookie('enemy')} vs ${getCookie('login')}`

const enemyBoard = document.querySelector('.cards.enemy')
const selfBoard = document.querySelector('.cards.self')
const enemyHand = document.querySelector('.hand.enemy')
const selfHand = document.querySelector('.hand.self')
const selfStonesElement = document.querySelector('.stones.self')

let enemyHandSize = 4
const enemyBoardArray = []
const selfBoardArray = []
const selfHandArray = []
let selfStones = 0
let currentSelfStones = selfStones

let turn = []

const rope = document.querySelector('.rope')
const timer = document.querySelector('.timer')
const endTrunButton = document.querySelector('.end-turn')

const restartInterval = id => {
    if (!id) {
        return setInterval(() => {
            let time = +timer.innerHTML - 1
        
            if (time === 0) {
                return endTrunButton.click()
            }
        
            const size = (time + 3) * 3 + 1
        
            timer.innerHTML = time.toString()
        
            rope.style.setProperty('--width', size.toString())
        }, 1000)
    }

    clearInterval(id)
    return restartInterval()
}

let ropeTimer = restartInterval()

endTrunButton.addEventListener('click', () => {
    timer.innerHTML = (30).toString()
    ropeTimer = restartInterval(ropeTimer)

    // send to server that turn end
})

const render = state => {
    switch (state) {
        case 'self_hand':
            selfHand.innerHTML = ''

            selfHandArray.forEach(card => {
                selfHand.innerHTML +=
                `<div class="self-card-box" data-card="${card[0]}">
                    <img src="/images/cards/${card[0].toLowerCase()}.jpg"class="self-card">
                    <span class="card-info">
                        <span class="at">${card[1]}</span>
                        <span class="hp">${card[2]}</span>
                        <span class="pr">${card[3]}</span>
                    </span>
                </div>`
            })

            selfHand.querySelectorAll('.self-card-box').forEach(card => {
                card.addEventListener('click', event => {
                    const element = event.currentTarget

                    const pr = +element.querySelector('.pr').innerText

                    if (pr > currentSelfStones) {
                        return
                    }
                    
                    currentSelfStones -= pr

                    selfBoard.innerHTML +=
                    `<div class="card self">
                        <img src="${element.querySelector('img').getAttribute('src')}">
                        <span class="drop at">
                            <span class="attack">${element.querySelector('.at').innerText}</span>
                        </span>
                        <span class="drop hp">
                            <span class="health">${element.querySelector('.hp').innerText}</span>
                        </span>
                    </div>`
                
                    selfHand.removeChild(element)
                
                    turn.push({
                        card: element.getAttribute('data-card'),
                        action: 'play'
                    })
                
                    render('self_stones')
                })
            })

            break
        case 'enemy_hand':
            enemyHand.innerHTML = ``

            for (let i = 0; i < enemyHandSize; i++) {
                enemyHand.innerHTML += `<img src="/images/deck.png"class="deck-card">`
            }

            break
        case 'self_stones':
            selfStonesElement.innerHTML = ``

            for (let i = 0; i < currentSelfStones; i++) {
                selfStonesElement.innerHTML += `<img src="/images/stone.png"class="stone">`
            }

            break
    }
}

socket.onmessage = event => {
    const data = JSON.parse(event.data)

    console.log(data)
    
    switch (data.type) {
        case 'logged':
            socket.send(
                JSON.stringify({
                    type: 'draw_hand'
                })
            )
            break
        case 'draw_hand':
            for (const card of data.hand) {
                selfHandArray.push(card)
            }

            // add to this object to 

            render('self_hand')
            render('enemy_hand')

            if (data.coin) {
                socket.send(
                    JSON.stringify({
                        type: 'draw'
                    })
                )
            }

            break
        case 'draw':
            if (selfStones < 6) {
                selfStones++
                currentSelfStones = selfStones
            }

            turn = [
                { card: null, action: 'draw' }
            ]

            selfHandArray.push(data.card)

            render('self_hand')
            render('self_stones')

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
