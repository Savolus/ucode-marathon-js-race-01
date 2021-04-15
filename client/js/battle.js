import { getCookie } from './cookies.js'

const socket = new WebSocket('ws://10.11.7.10:8080');

if (!getCookie('enemy')) {
    location.replace('/')
}

const title = document.querySelector('title')

title.innerHTML += ` ${getCookie('enemy')} vs ${getCookie('login')}`

const enemy = document.querySelector('.credentials.enemy .login')
const self = document.querySelector('.credentials.self .login')

enemy.innerHTML = getCookie('enemy')
self.innerHTML = getCookie('login')

const enemyBoard = document.querySelector('.cards.enemy')
const selfBoard = document.querySelector('.cards.self')
const enemyHand = document.querySelector('.hand.enemy')
const selfHand = document.querySelector('.hand.self')
const selfStonesElement = document.querySelector('.stones.self')
const enemyStonesElement = document.querySelector('.stones.enemy')

const enemyHero = document.querySelector('.hero.enemy')
const selfHero = document.querySelector('.hero.self')

let enemyHP = 20
let selfHP = 20

let enemyHandSize = 3
let enemyBoardArray = []
let selfBoardArray = []
let selfHandArray = []
let selfStones = 0, enemyStones = 0
let currentSelfStones = selfStones
let currentEnemyStones = enemyStones

let is_turn = false

let activeCard = null

const rope = document.querySelector('.rope')
const timer = document.querySelector('.timer')
const endTrunButton = document.querySelector('.end-turn')

const attacked = []

const restartInterval = id => {
    if (!id) {
        return setInterval(() => {
            let time = +timer.innerHTML - 1
        
            if (time === 0) {
                return endTrunButton.click()
            }

            if (time < 16) {
                const size = time * 7 - 5
            
                rope.style.setProperty('--width', size.toString())
            }

            timer.innerHTML = time.toString()
        }, 1000)
    }

    clearInterval(id)

    timer.innerHTML = (30).toString()
    rope.style.setProperty('--width', (100).toString())

    return restartInterval()
}

let ropeTimer = 0

endTrunButton.addEventListener('click', () => {
    if (!is_turn) {
        return
    }

    socket.send(
        JSON.stringify({
            type: 'end_turn'
        })
    )
})

const showResults = (winner, status) => {
    const messageBox = document.querySelector('.message-box')
    messageBox.querySelector('.container.small').innerHTML =
    `${winner} ${status}!
    <input type="button" class="controller" value="Home" onclick="location.replace('/')">
    `

    messageBox.style.display = 'flex'

    clearInterval(ropeTimer)
    socket.close()
}

enemyHero.addEventListener('click', () => {
    if (!is_turn || !activeCard) {
        return
    }

    const activeName = activeCard.dataset.self

    if (attacked.includes(activeName)) {
        return
    }

    attacked.push(activeName)

    activeCard.classList.remove('active')

    const damage = +activeCard.querySelector('.at').innerText

    enemyHP -= damage

    if (enemyHP <= 0) {
        return showResults(getCookie('login'), "win")
    }

    render('enemy_hero')

    socket.send(
        JSON.stringify({
            type: 'attack_face',
            damage
        })
    )
})

const render = state => {
    switch (state) {
        case 'self_hand':
            selfHand.innerHTML = ``

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
                    if (!is_turn) {
                        return
                    }

                    const element = event.currentTarget

                    const pr = +element.querySelector('.pr').innerText

                    if (pr > currentSelfStones || selfBoardArray.length === 4) {
                        return
                    }
                    
                    currentSelfStones -= pr

                    const card = [
                        element.getAttribute('data-card'),
                        element.querySelector('.at').innerText,
                        element.querySelector('.hp').innerText
                    ]

                    selfBoard.innerHTML +=
                    `<div class="card self">
                        <img src="${element.querySelector('img').getAttribute('src')}">
                        <span class="drop at">
                            <span class="attack">${card[1]}</span>
                        </span>
                        <span class="drop hp">
                            <span class="health">${card[2]}</span>
                        </span>
                    </div>`
                
                    selfHand.removeChild(element)
                    selfHandArray = selfHandArray.filter(handCard => handCard[0] !== card[0])
                    selfBoardArray.push(card)

                    socket.send(
                        JSON.stringify({
                            type: 'play',
                            stones: currentSelfStones,
                            card 
                        })
                    )
                
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
        case 'enemy_stones':
            enemyStonesElement.innerHTML = ``

            for (let i = 0; i < currentEnemyStones; i++) {
                enemyStonesElement.innerHTML += `<img src="/images/stone.png"class="stone">`
            }

            break
        case 'self_board':
            selfBoard.innerHTML = ``

            selfBoardArray = selfBoardArray.filter(card => card[2] > 0)

            selfBoardArray.forEach(card => {
                selfBoard.innerHTML +=
                `<div class="card self" data-self="${card[0]}">
                    <img src="/images/cards/${card[0]}.jpg">
                    <span class="drop at">
                        <span class="attack">${card[1]}</span>
                    </span>
                    <span class="drop hp">
                        <span class="health">${card[2]}</span>
                    </span>
                </div>`
            })

            selfBoard.querySelectorAll('.card.self').forEach(card => {
                card.addEventListener('click', event => {
                    if (!is_turn) {
                        return
                    }

                    const element = event.currentTarget

                    if (activeCard) {
                        activeCard.classList.remove('active')
                    }

                    activeCard = element

                    const name = activeCard.dataset.self

                    if (attacked.includes(name)) {
                        return
                    }

                    if (activeCard) {
                        activeCard.classList.add('active')
                    }
                })
            })

            break
        case 'enemy_board':
            enemyBoard.innerHTML = ``

            enemyBoardArray = enemyBoardArray.filter(card => card[2] > 0)

            enemyBoardArray.forEach(card => {
                enemyBoard.innerHTML +=
                `<div class="card enemy" data-enemy="${card[0]}">
                    <img src="/images/cards/${card[0]}.jpg">
                    <span class="drop at">
                        <span class="attack">${card[1]}</span>
                    </span>
                    <span class="drop hp">
                        <span class="health">${card[2]}</span>
                    </span>
                </div>`
            })

            enemyBoard.querySelectorAll('.card.enemy').forEach(card => {
                card.addEventListener('click', event => {
                    if (!activeCard || !is_turn) {
                        return
                    }

                    const enemyCard = event.currentTarget

                    const enemyName = enemyCard.dataset.enemy
                    const activeName = activeCard.dataset.self

                    if (attacked.includes(activeName)) {
                        return
                    }

                    const enemy_at = +enemyCard.querySelector('.at').innerText
                    const enemy_hp = +enemyCard.querySelector('.hp').innerText

                    const self_at = +activeCard.querySelector('.at').innerText
                    const self_hp = +activeCard.querySelector('.hp').innerText

                    const new_enemy_hp = enemy_hp - self_at
                    const new_self_hp = self_hp - enemy_at

                    let status = false

                    selfBoardArray.forEach(card => {
                        if (card[0] === activeName && !status) {
                            card[2] = new_self_hp
                            status = true
                        }
                    })

                    status = false

                    enemyBoardArray.forEach(card => {
                        if (card[0] === enemyName && !status) {
                            card[2] = new_enemy_hp
                            status = true
                        }
                    })

                    socket.send(
                        JSON.stringify({
                            type: 'attack',
                            self_board: selfBoardArray,
                            enemy_board: enemyBoardArray
                        })
                    )

                    attacked.push(activeName)

                    activeCard.classList.remove('active')
                    activeCard = null

                    render('self_board')
                    render('enemy_board')
                })
            })

            break
        case 'enemy_hero':
            enemyHero.innerHTML = 
            `<img src="/images/cards/thanos.jpg" class="hero-portrait enemy">
            <span class="hero-health drop hp">
                <span class="health self">
                    ${enemyHP}
                </span>
            </span>`

            break
        case 'self_hero':
            selfHero.innerHTML =
            `<img src="/images/cards/thanos.jpg" class="hero-portrait self">
            <span class="hero-health drop hp">
                <span class="health self">
                    ${selfHP}
                </span>
            </span>`

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

            render('self_hand')
            render('enemy_hand')
            render('self_stones')
            render('enemy_stones')

            if (data.coin) {
                is_turn = data.coin

                socket.send(
                    JSON.stringify({
                        type: 'draw'
                    })
                )
            }

            break
        case 'draw':
            if (data.self) {
                if (selfStones < 6) {
                    selfStones++
                }

                if (data.card && selfHandArray.length < 6) {
                    selfHandArray.push(data.card)
                }

                is_turn = true
            } else {
                if (enemyStones < 6) {
                    enemyStones++
                }
    
                if (enemyHandSize < 6 && data.enemy_hand < 6) {
                    enemyHandSize = +data.enemy_hand
                }
    
                is_turn = false
            }

            attacked.length = 0

            currentSelfStones = selfStones
            currentEnemyStones = enemyStones

            ropeTimer = restartInterval(ropeTimer)
            
            render('self_hand')
            render('self_stones')
            render('self_board')
            render('enemy_hand')
            render('enemy_stones')
            render('enemy_board')
            
            break
        case 'play':
            currentEnemyStones = data.stones
            enemyBoardArray.push(data.card)

            if (enemyHandSize > 0) {
                enemyHandSize--
            }

            render('self_stones')
            render('self_board')
            render('enemy_stones')
            render('enemy_board')

            break
        case 'attack':
            selfBoardArray = data.self_board
            enemyBoardArray = data.enemy_board

            render('self_stones')
            render('self_board')
            render('enemy_stones')
            render('enemy_board')

            break
        case 'attack_face':
            selfHP -= data.damage

            if (selfHP <= 0) {
                return showResults(getCookie('enemy'), 'win')
            }

            render('self_hero')

            break
        case 'end':
            const status = data.status
            let winner = getCookie("login")

            if (status !== "win") {
                winner = getCookie("enemy")
            }

            showResults(winner, status)

            break
    }
}

socket.onopen = () => {
    ropeTimer = restartInterval()

    socket.send(
        JSON.stringify({
            type: 'login',
            user: getCookie('login')
        })
    )
}
